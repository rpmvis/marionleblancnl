<?php
namespace app\Helpers;

class HtmlHelper{
    // class in use by developer for html conversions
    // no further comments
    function removeTags(string $str):string {
        $str = preg_replace("#<(.*)/(.*)>#iUs", "", $str);
        return $str;
    }

    function removeTag(string $tag, string $str):string {
        $str = $this->replaceTag($str, $tag, '');
        return $str;
    }

    function replaceTag(string $tag, string $replace, string $str):string {
        $str = preg_replace("#\<".$tag."(.*)/".$tag.">#iUs", $replace, $str);
        return $str;
    }

    function replaceOpenTag(string $tag, string $replace, string $str):string {
        $str = preg_replace("#\<" . $tag. "(.*)?" . ">#iUs", $replace, $str);
        return $str;
    }

    function replaceCloseTag(string $tag, string $replace, string $str):string {
        $str = preg_replace("#\<\/" . $tag. ">#iUs", $replace, $str);
        return $str;
    }

    function StripTableTags(string $html):string{
        $s = $html;

        // replace "<table.*>" with "<div class = 'table'>"
        $replace = "<div class = 'table'>";
        $s = $this->replaceOpenTag('Table', $replace, $s);

        $replace = "<div class = 'table_row'>";
        $s = $this->replaceOpenTag('TR', $replace, $s);

        $replace = "<div class = 'table_cell'>";
        $s = $this->replaceOpenTag('TD', $replace, $s);

        // replace "</table>" with "</div>"
        $replace = "</div>";
        $s = $this->replaceCloseTag('Table', $replace, $s);
        $s = $this->replaceCloseTag('TR', $replace, $s);
        $s = $this->replaceCloseTag('TD', $replace, $s);

        $find    = "<img height='12' width='12' src='http://www.marionleblanc.nl/web/appImages/BlueDot.gif'>";
        $replace = "{!! \$img_BlueDot !!}";
        $s = str_replace($find, $replace, $s);

        return $s;
    }

    function convertTable(string $html):string{
        $tag = 'tr';
        $count = 0;

        $re = new RegExHelper();
        $matches = $re->tagValueMatches($tag, $html);

        if (count($matches) === 0){
            $html_output = "no matches on tag $tag";
            return $html_output;
        }

        $s='';
        foreach ($matches as $match){
            $s .= "<div class = 'table_row'>\r\n";
            $count += 1;
            $matches2 = $re->tagValueMatches('td', $match);
                foreach ($matches2 as $match2){
                    $s .= "<div class = 'table_cell'>\r\n" . $match2;
                    $s .= "</div>";
                }
            $s .= "</div>";
        }

        return $s;
    }

}