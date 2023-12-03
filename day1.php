<?php
include(__DIR__."/_loader.php");

$sum = 0;

$input = InputHelper::loadFile("datasets/day1.txt");

foreach ($input as $line) {
    $line = preg_replace("/[^0-9]/", "", $line);

    if (! strlen($line)) continue;
    if (strlen($line) > 2) $line = substr($line,0,1).substr($line,-1);
    elseif (strlen($line) < 2) $line = $line.$line;

    $sum += intval($line);
}

echo "Answer #1: ".$sum.PHP_EOL;


$sum = 0;

$map = [
    'one' => 1,
    'two' => 2,
    'three' => 3,
    'four' => 4,
    'five' => 5,
    'six' => 6,
    'seven' => 7,
    'eight' => 8,
    'nine' => 9,

    '1' => 1,
    '2' => 2,
    '3' => 3,
    '4' => 4,
    '5' => 5,
    '6' => 6,
    '7' => 7,
    '8' => 8,
    '9' => 9,
];

function cleanTextualDigits ($line, $map) {
    $positions = [];
    
    foreach (array_keys($map) as $word) {
        $pos = 0;
        do {
            $pos = strpos($line, $word, $pos);
            if ($pos === false) continue;
            $positions[$pos] = $word;
            $pos++;
        } while ($pos !== false);
    }

    ksort($positions);
    if (empty($positions)) return $line;

    $s = "";
    foreach ($positions as $pos) $s .= strtr($pos, $map);
    return $s;
}

foreach ($input as $line) {
    $line = cleanTextualDigits($line, $map);
    $line = preg_replace("/[^0-9]/", "", $line);

    if (! strlen($line)) continue;
    if (strlen($line) > 2) $line = substr($line,0,1).substr($line,-1);
    elseif (strlen($line) < 2) $line = $line.$line;

    $sum += intval($line);
}

echo "Answer #2: ".$sum.PHP_EOL;
