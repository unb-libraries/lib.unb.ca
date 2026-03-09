<?php
/**
 * Drush PHP script to extract path segments from links and create taxonomy terms.
 *
 * Usage examples:
 *   drush php:script scripts/path_terms.php -- --url="https://systems.lib.unb.ca/archives.html" --limit=10
 *   drush scr scripts/path_terms.php -- --limit=50
 *   drush php:script scripts/path_terms.php -- --dry-run
 *
 * Notes:
 * - This script expects an existing term named "finding-aids" in vocabulary "unb_libraries_page_paths".
 * - The script will skip creating terms that already exist in the vocabulary.
 * - It will also skip segments that appear to be filenames (e.g., "eden.html", "file.pdf").
 */

use Drupal\taxonomy\Entity\Term;

/**
 * Parse CLI-style options from an argv array.
 *
 * Accepts forms:
 *  --flag
 *  --key=value
 *  --key value  (not supported here; use --key=value)
 */
function parse_cli_options_from_argv(array $argv) {
  $opts = [];
  foreach ($argv as $arg) {
    if (strpos($arg, '--') === 0) {
      $pair = substr($arg, 2);
      if ($pair === '') {
        // literal "--" separator
        continue;
      }
      if (strpos($pair, '=') !== false) {
        list($k, $v) = explode('=', $pair, 2);
        // normalize boolean-like strings
        if ($v === '1' || strtolower($v) === 'true') {
          $opts[$k] = true;
        } elseif ($v === '0' || strtolower($v) === 'false') {
          $opts[$k] = false;
        } else {
          $opts[$k] = $v;
        }
      }
      else {
        // flag without value
        $opts[$pair] = true;
      }
    }
  }
  return $opts;
}

// Collect raw argv from possible locations Drush might populate.
$raw_argv = [];
if (isset($argv) && is_array($argv)) {
  $raw_argv = $argv;
}
elseif (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
  $raw_argv = $_SERVER['argv'];
}

// Parse options.
$opts = parse_cli_options_from_argv($raw_argv);

// Default URL if none provided.
$url = $opts['url'] ?? 'https://systems.lib.unb.ca/archives.html';
$limit = isset($opts['limit']) ? (int) $opts['limit'] : null;

// Robust dry-run detection: check parsed options and raw argv forms.
$dry_run = false;
if (isset($opts['dry-run']) && $opts['dry-run']) {
  $dry_run = true;
}
elseif (isset($opts['dry_run']) && $opts['dry_run']) {
  $dry_run = true;
}
elseif (isset($opts['dryrun']) && $opts['dryrun']) {
  $dry_run = true;
}
else {
  // Also check raw argv for plain '--dry-run' etc.
  foreach ($raw_argv as $a) {
    if ($a === '--dry-run' || $a === '--dry_run' || $a === '--dryrun') {
      $dry_run = true;
      break;
    }
    if (strpos($a, '--dry-run=') === 0 || strpos($a, '--dry_run=') === 0 || strpos($a, '--dryrun=') === 0) {
      $val = substr($a, strpos($a, '=') + 1);
      if ($val === '1' || strtolower($val) === 'true') {
        $dry_run = true;
        break;
      }
    }
  }
}

echo "Starting script\n";
echo "Target URL: $url\n";
echo $limit ? "Limit: $limit\n" : "Limit: (none)\n";
echo $dry_run ? "Dry-run: ON (no terms will be created)\n" : "Dry-run: OFF\n";

// Fetch HTML using Drupal http client (Guzzle).
try {
  $client = \Drupal::httpClient();
  $response = $client->get($url, [
    'headers' => ['User-Agent' => 'drush-create-terms-script/1.3'],
    'http_errors' => false,
  ]);
  $status = $response->getStatusCode();
  if ($status < 200 || $status >= 300) {
    echo "Failed to fetch URL: HTTP status $status\n";
    return;
  }
  $html = (string) $response->getBody();
}
catch (\Exception $e) {
  echo "Error fetching URL: " . $e->getMessage() . "\n";
  return;
}

// Parse anchors and collect unique path segments after "/finding/".
libxml_use_internal_errors(true);
$dom = new \DOMDocument();
if (trim($html) === '') {
  echo "Fetched page is empty\n";
  return;
}
$dom->loadHTML($html);
$xpath = new \DOMXPath($dom);
$nodes = $xpath->query('//a[@href]');
$found = [];

foreach ($nodes as $node) {
  $href = $node->getAttribute('href');
  if (!$href) {
    continue;
  }
  // Look for "/finding/<segment>/"
  if (preg_match('#/finding/([^/]+)#i', $href, $m)) {
    $segment = $m[1];
    // Lowercase per requirement, and trim whitespace.
    $segment = mb_strtolower(trim($segment));
    if ($segment === '') {
      continue;
    }
    $found[$segment] = $href; // keep href if needed later
  }
}

$total_found = count($found);
if ($total_found === 0) {
  echo "No '/finding/' links found on the page.\n";
  return;
}

echo "Found $total_found unique '/finding/...' segments.\n";

// Prepare taxonomy storage and find parent term "finding-aids".
$storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
$parent_candidates = $storage->loadByProperties([
  'name' => 'finding-aids',
  'vid' => 'unb_libraries_page_paths',
]);

if (empty($parent_candidates)) {
  echo "Parent term 'finding-aids' not found in vocabulary 'unb_libraries_page_paths'.\n";
  echo "Please ensure a term named 'finding-aids' exists in that vocabulary and re-run.\n";
  return;
}

$parent_term = reset($parent_candidates);
$parent_tid = $parent_term->id();
echo "Using parent term 'finding-aids' (tid: $parent_tid)\n";

// List of extensions to treat as filenames (lowercase).
$skip_exts = [
  'html','htm','php','pdf','asp','aspx','jsp','cfm','xhtml','shtml','xml','txt',
  'jpg','jpeg','png','gif','zip','tar','gz','csv','doc','docx','odt','ppt','pptx',
  'xls','xlsx','svg','mp3','mp4','m4a','mov','avi','json','rss','ics','exe','bin'
];

// Helper to determine whether segment looks like a filename.
function is_filename_segment($segment, array $skip_exts) {
  // If there's no dot, it's not a filename.
  if (strpos($segment, '.') === false) {
    return false;
  }
  // If it ends with .<1-6 alnum>, consider it an extension candidate.
  if (preg_match('/\.([a-zA-Z0-9]{1,6})$/', $segment, $m)) {
    $ext = strtolower($m[1]);
    if (in_array($ext, $skip_exts)) {
      return true;
    }
  }
  return false;
}

// Process segments with limit if provided.
$segments = array_keys($found);
if ($limit !== null && $limit > 0) {
  $segments = array_slice($segments, 0, $limit);
  echo "Processing first " . count($segments) . " segments per the --limit parameter.\n";
}

$created = 0;
$skipped_existing = 0;
$skipped_filename = 0;
$errors = 0;
$created_term_names = [];

foreach ($segments as $segment) {
  // Ensure the term name exactly matches the lowercase segment.
  $term_name = $segment;

  // Skip if the segment appears to be a filename.
  if (is_filename_segment($term_name, $skip_exts)) {
    $skipped_filename++;
    echo "[SKIP-FILE] Segment appears to be a filename, skipping: '$term_name'\n";
    continue;
  }

  // Check for existing term with same name in the vocabulary.
  $existing = $storage->loadByProperties([
    'name' => $term_name,
    'vid' => 'unb_libraries_page_paths',
  ]);

  if (!empty($existing)) {
    $skipped_existing++;
    echo "[SKIP] Term already exists: '$term_name'\n";
    continue;
  }

  if ($dry_run) {
    echo "[DRY] Would create term: '$term_name'\n";
    $created++;
    $created_term_names[] = $term_name;
    continue;
  }

  try {
    $term = Term::create([
      'vid' => 'unb_libraries_page_paths',
      'name' => $term_name,
      // parent expects an array of tids for hierarchical vocabs
      'parent' => [$parent_tid],
    ]);
    $term->save();
    $created++;
    $created_term_names[] = $term_name;
    echo "[CREATE] Created term '{$term->id()}' -> '$term_name'\n";
  }
  catch (\Exception $e) {
    $errors++;
    echo "[ERROR] Creating term '$term_name': " . $e->getMessage() . "\n";
  }
}

echo "Done.\n";
echo "Summary: found={$total_found}, processed=" . count($segments) . ", created={$created}, skipped_existing={$skipped_existing}, skipped_filename={$skipped_filename}, errors={$errors}\n";

// Print the list of created terms, one per line (if any).
if (!empty($created_term_names)) {
  echo "\nCreated terms:\n";
  foreach ($created_term_names as $name) {
    echo $name . "\n";
  }
}
else {
  echo "\nNo terms were created.\n";
}