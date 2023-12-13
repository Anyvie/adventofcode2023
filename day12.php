<?php
include(__DIR__."/_loader.php");
set_time_limit(3600);

// THIS CAN SOLVE THE EXAMPLE EASILY
// FOR YOUR REAL INPUT, IT WILL TAKE A LOT OF TIME.
// LIKE 120 HOURS ! BE PREPARED.

$parser = new Parser("datasets/day12.txt");

echo "Answer #1: ".$parser->part1().PHP_EOL;
echo "Answer #2: ".$parser->part2().PHP_EOL;

class Parser {
    public $input = [];
    public $scores = [];
    public $tests = [];

    public function __construct($file) {
        $this->input = InputHelper::loadFile($file);
    }

    public function part1() {
        $total = 0;

        foreach ($this->input as $line) {
            list($line, $numbers) = explode(' ', $line);

            $score = $this->bruteforce($line, $numbers);
            $total += $score;

            $this->scores[$line] = $score;
        }

        return $total;
    }

    public function part2() {
        $total = 0;

        foreach ($this->input as $line) {
            list($line, $numbers) = explode(' ', $line);
            //echo $line;

            $line2 = str_repeat($line."?", 2);
            $numbers = str_repeat($numbers.",", 2);

            $line2 = substr($line2, 0, strlen($line2)-1);
            $numbers = substr($numbers, 0, strlen($numbers)-1);

            $score = $this->bruteforce($line2, $numbers);
            //echo " -> ".$score.PHP_EOL;

            $div = $score / $this->scores[$line];
            $score = $this->scores[$line];
            for ($i=0; $i<4; $i++) $score *= $div;

            $total += $score;
        }

        return $total;
    }

    public function bruteforce($line, $numbers) {
        $positions = [];

        for ($i=0; $i<strlen($line); $i++) {
            if ($line[$i] == '?') $positions[] = $i;
        }
        
        $line = str_replace('?', '#', $line);
        $line = str_split($line);

        $nbGroups = substr_count($numbers,',');
        $max = max(explode(',', $numbers));
        $max = str_repeat('#', $max);

        $this->tests = [];
        $this->getTests($line, $positions, $nbGroups, $numbers, $max);

        $total = array_sum($this->tests);

        return $total;
    }

    public function getTests($line, &$positions, &$nbGroups, $numbers, $max, $i=0) {
        if (! isset($positions[$i])) return;

        $tests = [];
        $line[ $positions[$i] ] = '.';
        $imp = implode('', $line);
        if (!isset($this->tests[$imp]) && strpos($imp, $max) !== false && (substr_count($imp,'.#') >= $nbGroups || substr_count($imp,'#.') >= $nbGroups)) {
            $this->tests[$imp] = $this->test($line, $numbers);
        }
        $this->getTests($line, $positions, $nbGroups, $numbers, $max, ($i+1));


        $line[ $positions[$i] ] = '#';
        $imp = implode('', $line);
        if (!isset($this->tests[$imp]) && strpos($imp, $max) !== false && (substr_count($imp,'.#') >= $nbGroups || substr_count($imp,'#.') >= $nbGroups)) {
            $this->tests[$imp] = $this->test($line, $numbers);
        }
        $this->getTests($line, $positions, $nbGroups, $numbers, $max, ($i+1));
    }

    public function test($line, $numbers) {
        $data = [];
        $k = 0;
        foreach ($line as $c) {
            if ($c == '.') $k++;
            else {
                if (! isset($data[$k])) $data[$k] = 0;
                $data[$k]++;
            }
        }

        $score = array_values($data) == explode(',', $numbers) ? 1 : 0;

        return $score;
    }
}
