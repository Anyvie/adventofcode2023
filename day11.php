<?php
include(__DIR__."/_loader.php");

$parser = new Parser("datasets/day11.txt");

echo "Answer #1: ".$parser->part1().PHP_EOL;
echo "Answer #2: ".$parser->part2().PHP_EOL;

class Parser {
    public $sky = [];

    public function __construct($file) {
        $input = InputHelper::loadFile($file);
        $y = 0;
        foreach ($input as $line) {
            $this->sky[$y] = [];
            for ($x=0; $x<strlen($line); $x++) {
                $c = $line[$x];
                $this->sky[$y][$x] = $c;
            }
            $y++;
        }

        //$this->view($this->sky);
    }

    public function part1() {
        return $this->calcDistance(1);
    }

    public function part2() {
        return $this->calcDistance(999999);
    }

    public function calcDistance($expansion=0) {
        $total = 0;

        $emptyY = [];
        $emptyX = [];
        $galaxies = [];

        $yy = 0;
        foreach ($this->sky as $y => $line) {
            if (! in_array('#', $line)) $emptyY[] = $y;
        }

        // expansion columns
        $height = count($this->sky);
        $width = count($this->sky[0]);
        for ($x=0; $x<$width; $x++) {
            $col = "";
            for ($y=0; $y<$height; $y++) $col .= $this->sky[$y][$x];
            if (strpos($col, '#') === false) $emptyX[] = $x;
        }

        $yy=0;
        foreach ($this->sky as $y => $line) {
            if (in_array($y, $emptyY)) $yy += $expansion;

            $xx=0;
            foreach ($line as $x => $c) {
                if (in_array($x, $emptyX)) $xx += $expansion;
                if ($c == '#') $galaxies[] = [$yy,$xx];
                $xx++;
            }
            
            $yy++;
        }

        for ($g1=0; $g1<count($galaxies); $g1++) {
            $p1 = $galaxies[$g1];

            for ($g2=($g1+1); $g2<count($galaxies); $g2++) {
                $p2 = $galaxies[$g2];

                $total += abs($p1[0] - $p2[0]);
                $total += abs($p1[1] - $p2[1]);
            }
        }

        return $total;
    }

    public function view($array) {
        foreach ($array as $y => $line) {
            foreach ($line as $x => $c) {
                echo $c;
            }
            echo PHP_EOL;
        }
    }
}
