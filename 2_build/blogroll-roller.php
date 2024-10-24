<?php

/*
 * @author @dylan-k Dylan Kinnett
 * forked from code by @skinofstars Kevin Carmody
 * GPLv3 - http://www.gnu.org/copyleft/gpl.html
 *
 * This is a command line app with no flags
 * for turning a bunch of URLs into an OPML file.
 *
 * 1. Takes input file of newline-separated URLs, normally blogs.
 * 2. Finds (autodiscovery) associated RSS of each URL.
 * 3. Outputs an OPML file for you to use in a feed reader.
 */

/* ================================
 *          Configuration
 * ================================ */

// File config
const INPUT_FILE = "URLlist.txt";
const OUTPUT_FILE = "blogroll.opml";

// OPML config
const OPML_TITLE = "Some Select Blogs";
const OPML_OWNER_NAME = "Author Name";
const OPML_OWNER_EMAIL = "author@example.com";

/* ================================
 *              Functions
 * ================================ */

/**
 * Opens a file and returns the handle.
 * @param string $filename
 * @param string $mode
 * @return resource
 * @throws \Exception
 */
function openFile(string $filename, string $mode)
{
  $handle = @fopen($filename, $mode);
  if (!$handle) {
    throw new \Exception('Cannot open file: ' . $filename);
  }
  return $handle;
}

/**
 * Processes a URL and writes the OPML entry to the output file.
 * @param string $url
 * @param resource $outHandle
 */
function processUrl(string $url, $outHandle): void
{
  $url = convertToHttps($url);
  $result = fetchHeadContent($url);
  $source = $result['content'];
  $error = $result['error'];

  if ($error) {
    echo "Failed to fetch content for: " . $url . " - Error: " . $error . "\n";
    return;
  }

  $rssURL = getRSSLocation($source, $url);
  $rssTitle = htmlentities(getTitleAlt($source));

  if ($rssURL) {
    $entryOut = opmlEntry($rssURL, $rssTitle ?: $rssURL);
    fwrite($outHandle, $entryOut);
  } else {
    echo "No RSS feed found for: " . $url . "\n";
  }
}

/**
 * Converts a URL from http to https if necessary.
 * @param string $url
 * @return string
 */
function convertToHttps(string $url): string
{
  return preg_replace('/^http:\/\//i', 'https://', $url);
}

/**
 * Fetches the <head> content of a URL.
 * @param string $url
 * @return array
 */
function fetchHeadContent(string $url): array
{
  $content = '';
  $error = '';
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Connection: close']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use (&$content) {
    $content .= $chunk;
    // Stop once </head> is found
    if (stripos($content, '</head>') !== false) {
      return -1; // Stop cURL early
    }
    return strlen($chunk);
  });

  $response = curl_exec($ch);
  if (curl_errno($ch)) {
    $error = curl_error($ch);
  }
  curl_close($ch);

  return ['content' => $content, 'error' => $error];
}

/**
 * Generates the OPML header.
 * @return string
 */
function opmlHeader(): string
{
  return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
    . "<opml version=\"1.1\">\n"
    . " <head>\n"
    . "     <title>" . OPML_TITLE . "</title>\n"
    . "     <dateCreated>" . date("r") . "</dateCreated>\n"
    . "     <ownerName>" . OPML_OWNER_NAME . "</ownerName>\n"
    . "     <ownerEmail>" . OPML_OWNER_EMAIL . "</ownerEmail>\n"
    . " </head>\n"
    . " <body>\n";
}

/**
 * Generates the OPML footer.
 * @return string
 */
function opmlFooter(): string
{
  return "  </body>\n</opml>";
}

/**
 * Generates an OPML entry.
 * @param string $feedURL
 * @param string $feedTitle
 * @return string
 */
function opmlEntry(string $feedURL, string $feedTitle): string
{
  return "    <outline text=\"{$feedTitle}\" type=\"rss\" xmlUrl=\"{$feedURL}\"/>\n";
}

/**
 * Extracts the title from the HTML source.
 * @param string $html
 * @return string|null
 */
function getTitleAlt(string $html): ?string
{
  if (preg_match('/<title>(.*?)<\/title>/is', $html, $found)) {
    return $found[1];
  }
  return null;
}

/**
 * Finds the RSS feed URL from the HTML source.
 * @param string $html
 * @param string $location
 * @return string|false
 */
function getRSSLocation(string $html, string $location)
{
  if (!$html || !$location) {
    return false;
  }

  preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
  $links = $matches[1];
  $final_links = [];

  foreach ($links as $link) {
    $attributes = preg_split('/\s+/s', $link);
    $final_link = [];

    foreach ($attributes as $attribute) {
      $att = preg_split('/\s*=\s*/s', $attribute, 2);
      if (isset($att[1])) {
        $att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
        $final_link[strtolower($att[0])] = $att[1];
      }
    }
    $final_links[] = $final_link;
  }

  foreach ($final_links as $final_link) {
    if (strtolower($final_link['rel']) == 'alternate') {
      if (strtolower($final_link['type']) == 'application/rss+xml' || strtolower($final_link['type']) == 'text/xml') {
        return absolutizeUrl($final_link['href'], $location);
      }
    }
  }

  return false;
}

/**
 * Converts a relative URL to an absolute URL.
 * @param string $href
 * @param string $location
 * @return string
 */
function absolutizeUrl(string $href, string $location): string
{
  if (strpos($href, "http://") !== false || strpos($href, "https://") !== false) {
    return $href;
  }

  $url_parts = parse_url($location);
  $full_url = "{$url_parts['scheme']}://{$url_parts['host']}";

  if (isset($url_parts['port'])) {
    $full_url .= ":{$url_parts['port']}";
  }

  if ($href[0] != '/') {
    $full_url .= dirname($url_parts['path']);
    if (substr($full_url, -1) != '/') {
      $full_url .= '/';
    }
  }

  return $full_url . $href;
}

/* ================================
 *            Execution
 * ================================ */

try {
  $inHandle = openFile(INPUT_FILE, "r");
  $outHandle = openFile(OUTPUT_FILE, "w"); // Overwrite the file each time

  fwrite($outHandle, opmlHeader());

  while (!feof($inHandle)) {
    $buffer = trim(fgets($inHandle, 4096));
    if (!empty($buffer)) {
      processUrl($buffer, $outHandle);
    }
  }

  fwrite($outHandle, opmlFooter());

  fclose($inHandle);
  fclose($outHandle);

  echo "\nAll done :)\n";
} catch (\Exception $e) {
  echo 'Error: ' . $e->getMessage() . "\n";
}
