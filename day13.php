<?php
include(__DIR__."/_loader.php");
set_time_limit(30);

$parser = new Parser("datasets/day13.txt");

echo "Answer #1: ".$parser->part1().PHP_EOL;
echo "Answer #2: ".$parser->part2().PHP_EOL;

class Parser {
    public $patterns = [];

    public $useLevenshtein = false;
    public $flipedPositions = [];
    public $k = 0;

    public function __construct($file) {
        $k = 0;
        $this->input = InputHelper::loadFile($file);
        foreach ($this->input as $line) {
            if (! strlen($line)) {
                $k++;
                continue;
            }

            $this->patterns[$k][] = $line;
        }
    }

    public function part1() {
        $nbColumns = $nbRows = 0;

        foreach ($this->patterns as $k => $pattern) {
            $this->k = $k;
            $patternReversed = $this->reversePattern($pattern);

            $scores = [
                'nbRows' => $this->findMirrorRow($pattern),
                'nbColumns' => $this->findMirrorRow($patternReversed),
            ];

            $mirrored = 0;
            $nb = 0;
            $var = "";
            foreach ($scores as $v => $data) {
                foreach ($data as $score) {
                    if ($score['mirrored'] > $mirrored) {
                        $mirrored = $score['mirrored'];
                        $nb = $score['nb'];
                        $var = $v;
                    }
                }
            }

            echo "Pattern ".$k.": ".$var." += ".$nb.PHP_EOL;

            //print_r($scores);

            ${$var} += $nb;
        }

        return $nbRows*100 + $nbColumns;
    }

    public function part2() {
        $this->useLevenshtein = true;
        $patterns = [];

        foreach ($this->patterns as $k => $pattern) {
            //echo PHP_EOL."Pattern ".$k."...".PHP_EOL;

            for ($i=0; $i<count($pattern)-1; $i++) {
                $nb = levenshtein($pattern[$i], $pattern[$i+1]);

                /*echo $pattern[$i].PHP_EOL;
                echo $pattern[$i+1].PHP_EOL;
                echo $nb.PHP_EOL;*/

                if ($nb == 1) {
                    $this->flipedPositions[$k] = $i;
                    $pattern[$i+1] = $pattern[$i];
                    $patterns[] = $pattern;
                    continue 2;
                }
            }

            //echo "...reverse...".PHP_EOL;
            $pattern = $this->reversePattern($pattern);
            for ($i=0; $i<count($pattern)-1; $i++) {
                $nb = levenshtein($pattern[$i], $pattern[$i+1]);

                /*echo $pattern[$i].PHP_EOL;
                echo $pattern[$i+1].PHP_EOL;
                echo $nb.PHP_EOL;*/

                if ($nb == 1) {
                    $this->flipedPositions[$k] = $i;
                    $pattern[$i+1] = $pattern[$i];
                    $patterns[] = $this->reversePattern($pattern);
                    continue 2;
                }
            }

            $patterns[] = $this->reversePattern($pattern);
        }

        // 36622 < x < 37517

        //print_r($patterns);

        $this->patterns = $patterns;

        return $this->part1();
    }

     

    public function findMirrorRow($pattern) {
        $scores = [];

        for ($i=0; $i<count($pattern)-1; $i++) {
            if ($pattern[$i] == $pattern[$i+1]) {
                $scores[] = $this->checkMirror($pattern, $i);
            }
        }

        return $scores;
    }

    public function checkMirror($pattern, $nb) {
        $mirrored = $levenshtein = 0;
        $breaked = false;

        for ($i=$nb,$j=$nb+1 ; $i>=0 && $j<count($pattern); $i--,$j++,$mirrored++) {
            // ---------------------------------------------------------------------
            // for part2
            if ($this->useLevenshtein) {
                if (in_array($this->flipedPositions[$this->k] ?? -1, [$i,$j])) $mirrored += 100000;

                $levenshtein = levenshtein($pattern[$i],$pattern[$j]);
                if ($levenshtein == 1) $mirrored += 10000;
            }
            // ---------------------------------------------------------------------
            if ($pattern[$i] != $pattern[$j] && $levenshtein != 1) {
                $breaked = true;
                break;
            }
        }

        if (! $breaked) $mirrored += 1000;

        return ['mirrored' => $mirrored, 'nb' => $nb+1];
    }

    public function reversePattern($pattern) {
        $new = [];

        foreach ($pattern as $r => $line) {
            $line = str_split($line);

            for ($c=0; $c<count($line); $c++) {
                if (! isset($new[$c])) $new[$c] = [];
                $new[$c][$r] = $line[$c];
            }
        }

        foreach ($new as $k => $line) {
            $new[$k] = implode('', $line);
        }

        return $new;
    }
}
