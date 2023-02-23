<?php

/**
 * @file
 * Audit path associations for common issues.
 */

use Drupal\node\Entity\Node;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPath;
use Drupal\taxonomy\Entity\Term;

echo "\nTERM ASSOCIATIONS:\n";
$vid = 'unb_libraries_page_paths';
$terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
foreach ($terms as $term) {
  $term_obj = Term::load($term->tid);
  $term_path = NodeTaxonomyPath::getTermPathValue($term_obj);
  echo "\n$term_path [{$term->tid}]:\n";


  $connection = \Drupal::database();
  $query = $connection->query("SELECT nid FROM {node_taxonomy_path} where tid={$term->tid}");
  $results = $query->fetchAll();
  foreach ($results as $result) {
    $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $result->nid);
    echo "- $alias\n";
  }
}

echo "\nORPHANED LIBRARY PAGES:\n";
$query = $connection->query("SELECT nid, tid FROM {node_taxonomy_path}");
$results = $query->fetchAll();
foreach ($results as $result) {
  $term_obj = Term::load($result->tid);
  if (empty($term_obj)) {
    echo "Orphaned Node {$result->nid} references non-existent TID {$result->tid}...\n";
  }
}

echo "\nUNASSIGNED LIBRARY PAGES:\n";
$query = $connection->query("SELECT nid FROM {node} WHERE type='library_page'");
$results = $query->fetchAll();
foreach ($results as $result) {
  $node_obj = Node::load($result->nid);
  $path = NodeTaxonomyPath::getNodePath($node_obj);
  if (empty($path)) {
    echo "Unassigned Node {$result->nid}...\n";
  }
}

echo "\nROOT LIBRARY PAGES:\n";
$query = $connection->query("SELECT nid FROM {node} WHERE type='library_page'");
$results = $query->fetchAll();
foreach ($results as $result) {
  $node_obj = Node::load($result->nid);
  $path = NodeTaxonomyPath::getNodePath($node_obj);
  if ($path == '/') {
    $full_path = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $result->nid);
    echo "Root Node [$full_path]...\n";
  }
}
