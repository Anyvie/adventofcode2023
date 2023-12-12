<?php
include(__DIR__."/_loader.php");

$parser = new Parser("datasets/day10.txt");

echo "Answer #1: ".$parser->part1().PHP_EOL;
echo "Answer #2: ".$parser->part2().PHP_EOL;

class Parser {
    public $map = [];
    public $distances = [];
    public $start = ['x'=>0, 'y'=>0];

    public function __construct($file) {
        $input = InputHelper::loadFile($file);
        $y = 0;
        foreach ($input as $line) {
            $this->map[$y] = [];
            $this->distances[$y] = [];
            for ($x=0; $x<strlen($line); $x++) {
                $c = $line[$x];
                $this->map[$y][$x] = $c;
                $this->distances[$y][$x] = null;
                if ($c == 'S') $this->start = ['x' => $x, 'y' => $y];
            }
            $y++;
        }
    }

    public function part1() {
        $cursor = new Cursor(
            $this->start['x'],
            $this->start['y'],
            $this->map,
            $this->distances,
        );

        $cursor->calcFarthest();

        $max = -1;
        foreach ($this->distances as $y => $line) {
            foreach ($line as $x => $N) {
                $max = max($max, $N);
                //echo ($N ?? ".")." ";
            }
            //echo PHP_EOL;
        }

        return $max;
    }

    public function part2() {
        $total = 0;

        $newmap = [];
        foreach ($this->distances as $y => $line) {
            $newmap[$y] = [];
            foreach ($line as $x => $N) {
                $newmap[$y][$x] = is_null($N) ? "." : "x";
            }
        }

        $height = count($newmap);
        $width = count($newmap[0]);

        for ($y=0; $y<$height; $y++) {
            $nbCross = 0;
            $inL = $inF = false;

            for ($x=0; $x<$width; $x++) {
                $cnew = $newmap[$y][$x];
                $c = $this->map[$y][$x];

                if ($cnew == 'x') {
                    if ($c == 'F') $inF = true;
                    if ($c == 'J') {
                        if ($inL) $inL = false;
                        elseif ($inF) {
                            $nbCross++;
                            $inF = false;
                        }
                    }

                    if ($c == 'L') $inL = true;
                    if ($c == '7') {
                        if ($inF) $inF = false;
                        elseif ($inL) {
                            $nbCross++;
                            $inL = false;
                        }
                    }
                    
                    if ($c == '|') $nbCross++;
                }
                elseif ($cnew == ' ') continue;
                else $newmap[$y][$x] = ($nbCross%2 == 1) ? '_' : ' ';
            }
        }

        /*$mapviewer = [];
        foreach ($newmap as $y => $line) {
            $mapviewer[$y] = [];
            foreach ($line as $x => $c) {
                if ($c == 'x') {
                    $mapviewer[$y][$x] = $this->map[$y][$x];
                } else {
                    if ($c == '_') $mapviewer[$y][$x] = "■";
                    else $mapviewer[$y][$x] = ".";
                }
            }
        }

        echo PHP_EOL;
        $this->view($mapviewer, 1, true);
        echo PHP_EOL;*/

        $total = 0;
        foreach ($newmap as $y => $line) {
            $total += substr_count(implode(',',$line), '_');
        }

        return $total;
    }

    public function view($array, $pad=2, $fancy=false) {
        foreach ($array as $y => $line) {
            foreach ($line as $x => $c) {
                if ($fancy) {
                    $c = match ($c) {
                        '|' => '┃',
                        '-' => '━',
                        'L' => '┗',
                        'J' => '┛',
                        '7' => '┓',
                        'F' => '┏',
                        //'.' => ' ',
                        default => $c,
                    };

                    echo str_repeat(' ', $pad-mb_strlen($c)).$c;
                } else {
                    echo str_pad($c, $pad, ' ', STR_PAD_LEFT);
                }
            }
            echo PHP_EOL;
        }
    }
}

class Cursor {
    public function __construct(
        public $x,
        public $y,
        private &$map,
        private &$distances,
        private $steps=[]
    ) {

    }

    public function calcFarthest($dist=0) {
        $ground = $this->map[$this->y][$this->x];
        if ($ground == ".") return;

        $this->distances[$this->y][$this->x] = min($this->distances[$this->y][$this->x] ?? PHP_INT_MAX, $dist);

        if ($ground == 'S') {
            $e = $this->map[$this->y][$this->x+1] ?? '.';
            $w = $this->map[$this->y][$this->x-1] ?? '.';
            $n = $this->map[$this->y-1][$this->x] ?? '.';
            $s = $this->map[$this->y+1][$this->x] ?? '.';

            if (in_array($e, ['-','7','J'])) {
                if (in_array($w, ['-','F','L'])) $ground = '-';
                elseif (in_array($n, ['|','7','F'])) $ground = 'L';
                elseif (in_array($s, ['|','L','J'])) $ground = 'F';
            } elseif (in_array($w, ['-','F','L'])) {
                if (in_array($n, ['|','7','F'])) $ground = 'J';
                elseif (in_array($s, ['|','L','J'])) $ground = '7';
            } elseif (in_array($n, ['|','7','F'])) {
                if (in_array($s, ['|','L','J'])) $ground = '|';
            }

            $this->map[$this->y][$this->x] = $ground;
        } 

        $movables = [];
        if (in_array($ground, ['|','L','J'])) $movables[] = function($x,$y) { return [$x,$y-1]; };
        if (in_array($ground, ['|','7','F'])) $movables[] = function($x,$y) { return [$x,$y+1]; };
        if (in_array($ground, ['-','L','F'])) $movables[] = function($x,$y) { return [$x+1,$y]; };
        if (in_array($ground, ['-','7','J'])) $movables[] = function($x,$y) { return [$x-1,$y]; };
        if (empty($movables)) return;

        foreach ($movables as $f) {
            list($x,$y) = $f($this->x, $this->y);

            if (! isset($this->map[$y]) || ! isset($this->map[$y][$x])) continue;
            if (! is_null($this->distances[$y][$x]) && $this->distances[$y][$x] <= $dist) continue;

            $cursor = new Cursor($x, $y, $this->map, $this->distances);
            $cursor->calcFarthest($dist+1);
        }
    }
}
