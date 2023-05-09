<?php

error_reporting(E_ERROR | E_PARSE);

/**
 * Util functions for the game.
 */
class Util {

    private const URL_PREFIX = "https://en.wikipedia.org";

    private const RAND_URL = "https://en.wikipedia.org/wiki/Special:Random";

    /**
     * Extracting all links from a Wikipedia webpage.
     *
     * Only extract links which are lead to another Wikipedia pages.
     * 
     * @param string $url Url from which links will be extracted.
     * @return array Array of links extracted from provided page.
     */ 
    public static function extract_links(string $url) {
        $html = file_get_contents($url);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $xpath = new DOMXpath($doc);
        $nodes = $xpath->query('//a');
        $result = [];
        foreach ($nodes as $node) {
            $url = $node->getAttribute('href');
            if (substr($url, 0, 5) === "/wiki") {
                if (strlen($url) < 10 || substr($url, 6, 4) !== "File") {
                    $result[] = self::URL_PREFIX . $node->getAttribute('href');
                }
            }
        }
        return $result;
    }

    /**
     * Get link to a random page from a Wikipedia.
     *
     * @return string Url of random page.
     */ 
    public static function get_random_link() {
        $links = self::extract_links(self::RAND_URL);
        $rand_key = array_rand($links, 1);
        return $links[$rand_key];
    }

    /**
     * Calculating minimal distance between two pages.
     *
     * Distance is a number of transitions you made on the way
     * from one page to another only using links on your current page.
     * This method is slow because it have to download huge amount of pages.
     * 
     * @param string $url1 Url of the starting page.
     * @param string $url2 Url of the end page.
     * @return int Distance between pages or -1 if the end page is unreachable from the starting page.
     */ 
    public static function calculate_min_distance(string $url1, string $url2) {
        $current_depth = 1;
        $used = [];
        $queue = [$url1];
        while (!empty($queue)) {
            $queue_size = count($queue);
            while ($queue_size-- != 0) {
                $current_url = array_shift($queue);
                foreach (self::extract_links($current_url) as $url) {
                    if (!$used[$url]) {
                        $used[$url] = true;
                        if ($url == $url2) {
                            return $current_depth;
                        }
                        $queue[] = $url;
                    }
                }       
            }
            ++$current_depth;
        }
        return -1;
    }
}