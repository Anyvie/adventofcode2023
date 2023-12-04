<?php
include(__DIR__."/_loader.php");

$parser = new Day4Parser("datasets/day4.txt");

$answer1 = $answer2 = 0;

foreach ($parser->cards as $cardNumber => $card) {
    // for the part2, we modify $parser->cards in each iteration.
    // so we "refresh" the data with this call
    $card = $parser->cards[$cardNumber];

    $commons = array_intersect($card['winners'], $card['mine']);
    $nbCommons = count($commons);
    if (! $nbCommons) continue;

    $answer1 += pow(2, $nbCommons-1);

    // part2
    for ($i=1; $i<=$nbCommons; $i++) {
        $next = intval($cardNumber+$i);
        if (! isset($parser->cards[$next])) continue;
        $parser->cards[$next]['nb'] += $card['nb'];
    }
}

foreach ($parser->cards as $cardNumber => $card) $answer2 += $card['nb'];

echo "Answer #1: ".$answer1.PHP_EOL;
echo "Answer #2: ".$answer2.PHP_EOL;


class Day4Parser {
    public $cards = [];

    public function __construct($file) {
        $input = InputHelper::loadFile($file);

        foreach ($input as $line) {
            $line = str_replace('Card', '', $line);
            list($cardNumber, $data) = explode(':', $line);
            list($winners, $mine) = explode('|', $data);

            $cardNumber = trim($cardNumber);

            $winners = array_filter(str_getcsv($winners, ' '));
            $mine = array_filter(str_getcsv($mine, ' '));

            $this->cards[$cardNumber] = [
                'winners' => $winners,
                'mine' => $mine,
                'nb' => 1,
            ];
        }
    }
}
