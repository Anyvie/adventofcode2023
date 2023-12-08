<?php
include(__DIR__."/_loader.php");

$parser = new Parser("datasets/day7.txt");

$answer1 = 0;
$i = 1;
foreach ($parser->hands as $hand) {
    $answer1 += $i * $parser->handsBid[$hand];
    $i++;
}

$answer2 = 0;
$i = 1;
foreach ($parser->hands2 as $hand) {
    $answer2 += $i * $parser->handsBid[$hand];
    $i++;
}

echo "Answer #1: ".$answer1.PHP_EOL;
echo "Answer #2: ".$answer2.PHP_EOL;

class Parser {
    public $hands = [];
    public $hands2 = [];
    public $handsBid = [];
    public $chars = ['A','K','Q','J','T','9','8','7','6','5','4','3','2'];
    public $chars2 = ['A','K','Q','T','9','8','7','6','5','4','3','2','J'];

    public function __construct($file) {
        $input = InputHelper::loadFile($file);

        foreach ($input as $line) {
            if (! strlen($line)) continue;
            list($hand, $bid) = explode(' ', $line);
            $this->handsBid[$hand] = $bid;
        }

        $this->hands = array_keys($this->handsBid);
        $this->hands2 = $this->hands;

        $flip = array_flip($this->chars);
        usort($this->hands, function($a, $b) use ($flip) {
            $scoreA = $this->calculateScore($a);
            $scoreB = $this->calculateScore($b);
            if ($scoreA != $scoreB) return ($scoreA < $scoreB) ? -1 : 1;

            for ($i=0; $i<strlen($a); $i++) {
                $cA = substr($a, $i, 1);
                $cB = substr($b, $i, 1);

                if ($cA != $cB) {
                    return ($flip[$cA] > $flip[$cB]) ? -1 : 1;
                }
            }

            return 0;
        });

        $flip = array_flip($this->chars2);
        usort($this->hands2, function($a, $b) use ($flip) {
            $scoreA = $this->calculateScorePart2($a);
            $scoreB = $this->calculateScorePart2($b);
            if ($scoreA != $scoreB) return ($scoreA < $scoreB) ? -1 : 1;

            for ($i=0; $i<strlen($a); $i++) {
                $cA = substr($a, $i, 1);
                $cB = substr($b, $i, 1);

                if ($cA != $cB) {
                    return ($flip[$cA] > $flip[$cB]) ? -1 : 1;
                }
            }

            return 0;
        });
    }

    public function calculateScore($hand) {
        $score = 0;
        foreach ($this->chars as $idx => $char) {
            $count = substr_count($hand, $char);
            if ($count <= 1) continue;
            else $score += pow(1000, $count);
        }
        return $score;
    }

    public function calculateScorePart2($hand) {
        if (strpos($hand, 'J') === false || $hand == 'JJJJJ') return $this->calculateScore($hand);

        $counts = [];

        foreach ($this->chars2 as $idx => $char) {
            if ($char == 'J') continue;
            $count = substr_count($hand, $char);
            if ($count == 0) continue;
            $counts[$char] = $count;
        }

        arsort($counts);
        $first = @array_shift(array_keys($counts));
        return $this->calculateScore(str_replace('J', $first, $hand));
    }
}
