<?php
include(__DIR__."/_loader.php");

$parser = new Day3Parser("datasets/day3.txt");

$answer1 = $answer2 = 0;

foreach ($parser->markers as $coords => $c) {
    list($x, $y) = explode(';', $coords);

    $adjacents = [];
    
    $adjacents[] = $parser->getNumberAt(($x-1), $y);
    $adjacents[] = $parser->getNumberAt(($x-1), ($y-1));
    $adjacents[] = $parser->getNumberAt(($x-1), ($y+1));
    $adjacents[] = $parser->getNumberAt($x, ($y-1));
    $adjacents[] = $parser->getNumberAt($x, ($y+1));
    $adjacents[] = $parser->getNumberAt(($x+1), $y);
    $adjacents[] = $parser->getNumberAt(($x+1), ($y-1));
    $adjacents[] = $parser->getNumberAt(($x+1), ($y+1));
    $adjacents = array_diff($adjacents, [0]);

    $answer1 += array_sum($adjacents);
    
    if ($c == '*' && count($adjacents) == 2) $answer2 += array_product($adjacents);
}

echo "Answer #1: ".$answer1.PHP_EOL;
echo "Answer #2: ".$answer2.PHP_EOL;


class Day3Parser {
    public $numbers;
    public $markers;

    public $bufferStart = "";
    public $bufferData = "";

    public $x = 0;
    public $y = 0;

    public function __construct($file) {
        $input = InputHelper::loadFile($file);

        foreach ($input as $line) {
            $len = strlen($line);
        
            for ($this->x=0; $this->x < $len; $this->x++) {
                $c = substr($line, $this->x, 1);
        
                if (is_numeric($c)) {
                    $this->bufferAdd($c);
                } else {
                    $this->bufferEnd();
                    if ($c != ".") $this->markers[$this->x.";".$this->y] = $c;
                }
            }

            $this->bufferEnd();

            $this->y++;
        }
    }

    public function getNumberAt($x, $y) {
        if (! isset($this->numbers[$x.';'.$y])) return 0;

        $n = $this->numbers[$x.';'.$y];
        for ($i=$n['start']; $i<$n['end']; $i++) unset($this->numbers[$i.';'.$y]);

        return $n['number'];
    }

    public function bufferAdd($c) {
        if ($this->bufferStart == "") $this->bufferStart = $this->x;
        $this->bufferData .= $c;
    }
    public function bufferEnd() {
        if ($this->bufferStart == "") return;

        for ($x=$this->bufferStart; $x < $this->x; $x++) {
            $this->numbers[$x.";".$this->y] = [
                'start' => $this->bufferStart, 
                'end' => $this->x,
                'number' => $this->bufferData
            ];
        }

        $this->bufferData = "";
        $this->bufferStart = "";
    }
}
