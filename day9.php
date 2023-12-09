<?php
include(__DIR__."/_loader.php");

$parser = new Parser("datasets/day9.txt");

echo "Answer #1: ".$parser->part1().PHP_EOL;
echo "Answer #2: ".$parser->part2().PHP_EOL;

class Parser {
    public $rows;

    public function __construct($file) {
        $input = InputHelper::loadFile($file);
        foreach ($input as $line) {
            $this->rows[] = explode(' ', $line);
        }
    }

    public function part1() {
        $total = 0;
        foreach ($this->rows as $row) $total += $this->calc($row);
        return $total;
    }
    public function calc($row, $depth=0) {
        if (! count(array_filter($row))) return 0;

        $row2 = [];
        for ($i=1; $i<count($row); $i++) $row2[] = $row[$i] - $row[$i - 1];

        return array_pop($row)+$this->calc($row2);
    }

    public function part2() {
        $total = 0;
        foreach ($this->rows as $row) $total += $this->calc2($row);
        return $total;
    }
    public function calc2($row, $depth=0) {
        if (! count(array_filter($row))) return 0;

        $row2 = [];
        for ($i=1; $i<count($row); $i++) $row2[] = $row[$i] - $row[$i - 1];

        return array_shift($row)-$this->calc2($row2);
    }
}
