<?php

$input = "a (b c (d e (f) g) h) i (j k)";


function findMatch($string, $index)
{
    $pair = [
        '(' => ')',
        '{' => '}'
    ];

    $arr = str_split($string);
    $parentChar = $string[$index];
    $parentPair = $pair[$parentChar];
    $charCount = 0;

    foreach ($arr as $key => $value) {
        if ($key > $index) {

            if ($value == $parentChar) {
                $charCount++;
            }

            if ($value == $parentPair && $charCount == 0) {
                return $key;
            }

            if ($value == $parentPair && $charCount != 0) {
                $charCount--;
            }
        }
    }
}
echo findMatch($input, 24);
