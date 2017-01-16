<?php
namespace app\Helpers;

class RegExHelper{
    function search(string $pattern, string $test_string):array{
        // regex search on string $test_string with pattern $pattern
        // returns an array of matches or null
        $pattern = trim($pattern);
        preg_match($pattern, $test_string, $matches);
        return (array)$matches;
    }

    function search_all(string $pattern, string $test_string):array{
        // regex search on string $test_string with pattern $pattern
        // returns an array of all matches (main match or match on capturing group) or null
        $pattern = trim($pattern);
        preg_match_all($pattern, $test_string, $matches);
        return (array)$matches;
    }

    function tagValueMatches(string $tag, string $html):array{
        // regex search on html text with pattern derived from html $tag
        // pattern uses $tag and the 1st capturing group (?:.*?) to capture tag values
        // returns an array of tag value matches or null
        $pattern = "/<" . $tag . "(?:.*?)>(.*?)<\/" . $tag . ">/is";
        $matches = $this->search_all($pattern, $html);
        $valueMatches = array();

        // assumption: 1st capturing group (?:.*?) contains tag values
        if (count($matches) >= 2 and
            !empty($matches[0]) and
            !empty($matches[1])) {
            $valueMatches = $matches[1];
        }
        return $valueMatches;
    }
}