<?php
include(__DIR__."/_loader.php");

$parser = new Parser("datasets/day6.txt");

echo "Answer #1: ".array_product($parser->scores).PHP_EOL;
echo "Answer #2: ".$parser->score2.PHP_EOL;



class Parser {
    public $scores = [];
    public $score2;

    public function __construct($file) {
        $input = InputHelper::loadFile($file);

        $times = array_values(array_filter(str_getcsv($input[0], ' ')));
        $records = array_values(array_filter(str_getcsv($input[1], ' ')));

        unset($times[0]);
        unset($records[0]);

        foreach ($times as $race => $time) {
            $this->scores[$race] = 0;

            $dist = 0;
            for ($t=0; $t<$time; $t++) {
                $score = ($time-$t)*$dist;
                if ($score > $records[$race]) $this->scores[$race]++;
                $dist++;
            }
        }

        // part2
        $time = implode('', $times);
        $record = implode('', $records);

        $peak = false;

        $dist = 0;

        for ($t=0; $t<$time; $t++) {
            $score = ($time-$t)*$dist;

            if ($score > $record) {
                $peak = true;
                $this->score2++;
            } elseif ($peak) { // optimization: it's useless to continue, all ongoing scores are lower than record
                break;
            }

            $dist++;
        }
    }
}
