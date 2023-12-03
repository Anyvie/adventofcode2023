<?php
include(__DIR__."/_loader.php");

$sum = 0;
$sum2 = 0;

$input = InputHelper::loadFile("datasets/day2.txt");

$MAX_RED = 12;
$MAX_GREEN = 13;
$MAX_BLUE = 14;

foreach ($input as $line) {
    $game_number = @intval(str_replace("Game ", "", array_shift(explode(':', $line))));
    $valid = 1;
    
    $minRed = $minGreen = $minBlue = 0;

    $games = explode(';', $line);
    foreach ($games as $game) {
        $matches = $red = $green = $blue = [];
        
        preg_match_all("/([0-9]+) red/", $game, $matches);
        if (isset($matches[1])) $red = $matches[1];
        if (array_sum($red) > $MAX_RED) $valid = 0;

        preg_match_all("/([0-9]+) green/", $game, $matches);
        if (isset($matches[1])) $green = $matches[1];
        if (array_sum($green) > $MAX_GREEN) $valid = 0;

        preg_match_all("/([0-9]+) blue/", $game, $matches);
        if (isset($matches[1])) $blue = $matches[1];
        if (array_sum($blue) > $MAX_BLUE) $valid = 0;

        // part 2
        $minRed = max($minRed, array_sum($red));
        $minGreen = max($minGreen, array_sum($green));
        $minBlue = max($minBlue, array_sum($blue));
    }

    if ($valid) $sum += $game_number;
    $sum2 += $minRed*$minGreen*$minBlue;
}

echo "Answer #1: ".$sum.PHP_EOL;
echo "Answer #2: ".$sum2.PHP_EOL;
