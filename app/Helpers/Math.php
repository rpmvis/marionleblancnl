<?php

namespace app\Helpers;

class Math{
    public function sum($i, $j)
    {
        return $i+$j;
    }

    public function say_hello()
    {
        $uri = $_SERVER['REQUEST_URI'];
        echo 'uri: ' . $uri . '<br>';
        return "hello";
    }
}
?>
