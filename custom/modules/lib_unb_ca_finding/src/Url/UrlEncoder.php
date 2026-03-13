<?php

namespace Drupal\lib_unb_ca_finding\Url;

/**
 * Helper for percent-encoding URLs safely for HTTP requests.
 */
class UrlEncoder {

  /**
   * Encode a URL preserving scheme, host, and port.
   *
   * - Encodes each path segment with rawurlencode (RFC 3986).
   * - Encodes query keys/values with urlencode.
   * - Encodes fragment with rawurlencode.
   *
   * @param string $url
   *   The raw URL that may contain spaces or other illegal characters.
   *
   * @return string
   *   The percent-encoded URL safe for HTTP requests.
   */
  public static function encodeUrl(string $url): string {
    $parts = @parse_url($url);
    if ($parts === false || !isset($parts['host'])) {
      // Best-effort fallback: replace spaces with %20.
      return str_replace(' ', '%20', $url);
    }

    $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
    $user   = $parts['user'] ?? '';
    $pass   = isset($parts['pass']) ? ':' . $parts['pass']  : '';
    $auth   = $user === '' ? '' : $user . $pass . '@';
    $host   = $parts['host'] ?? '';
    $port   = isset($parts['port']) ? ':' . $parts['port'] : '';

    // Encode path by splitting on '/' and rawurlencode each segment.
    $path = '';
    if (isset($parts['path'])) {
      $segments = explode('/', $parts['path']);
      $encoded_segments = array_map(function ($seg) {
        return rawurlencode($seg);
      }, $segments);
      $path = implode('/', $encoded_segments);
      // If original path started with '/', ensure leading slash remains.
      if (strpos($parts['path'], '/') === 0 && strpos($path, '/') !== 0) {
        $path = '/' . ltrim($path, '/');
      }
    }

    // Encode query if present.
    $query = '';
    if (isset($parts['query'])) {
      parse_str($parts['query'], $qpairs);
      $encoded_pairs = [];
      foreach ($qpairs as $k => $v) {
        if (is_array($v)) {
          foreach ($v as $item) {
            $encoded_pairs[] = urlencode($k) . '[]=' . urlencode((string) $item);
          }
        }
        else {
          $encoded_pairs[] = urlencode($k) . '=' . urlencode((string) $v);
        }
      }
      if (!empty($encoded_pairs)) {
        $query = '?' . implode('&', $encoded_pairs);
      }
    }

    // Fragment
    $fragment = '';
    if (isset($parts['fragment'])) {
      $fragment = '#' . rawurlencode($parts['fragment']);
    }

    return $scheme . $auth . $host . $port . $path . $query . $fragment;
  }

}