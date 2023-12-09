<?php
include(__DIR__."/_loader.php");
set_time_limit(3600);

$parser = new Parser("datasets/day8.txt");

echo "Answer #1: ".$parser->part1().PHP_EOL;

// part2Bruteforce_NotFastEnough() below is taking (on my machine) about 5s to calc 10M positions
// part2Bruteforce_StillNotFastEnough() is taking about 3s to calc 10M positions !
// Even with this, 10G positions in 50mns is not enough to solve it.

// part2WithShortcut_FailAgain() tried to use shortcuts, but again, it is not the way.
//echo "Answer #2: ".$parser->part2WithShortcut_FailAgain().PHP_EOL;

class Parser {
    public $instructions = [];
    public $mapping = [];

    public function __construct($file) {
        $input = InputHelper::loadFile($file);

        $this->instructions = array_shift($input);

        foreach ($input as $line) {
            if (! strlen($line)) continue;
            $line = str_replace([' ','(',')'], '', $line);
            $line = str_replace('=', ',', $line);
            list($from, $left, $right) = explode(',', $line);
            $this->mapping[$from] = ['L' => $left, 'R' => $right];
        }
    }

    public function part1() {
        $steps = 0;
        $length = strlen($this->instructions);

        $pos = "AAA";
        $end = "ZZZ";

        do {
            $dir = substr($this->instructions, $steps%$length, 1);
            $pos = $this->mapping[$pos][$dir];
            $steps++;
        } while ($pos != $end && $steps < 5000000);

        return $steps;
    }




    public function part2Bruteforce_NotFastEnough() {
        $steps = 0;
        $length = strlen($this->instructions);

        $cursors = [];
        foreach ($this->mapping as $pos => $xxx) {
            if (substr($pos, -1) == 'A') $cursors[] = $pos;
        }

        $nbA = count($cursors);
        
        // local variables seem to be a bit faster
        $mapping = $this->mapping;
        $instructions = $this->instructions;

        $i = 0;
        do {
            $nbZ = 0;
            $dir = $instructions[$i];

            for ($k=0; $k<$nbA; $k++) {
                $cursors[$k] = $mapping[$cursors[$k]][$dir];
                if ($cursors[$k][2] == 'Z') $nbZ++;
            }

            $steps++;
            $i++;
            if ($i >= $length) $i = 0;
        } while ($nbZ < $nbA && $steps < 10000000);

        return $steps;
    }

    public function part2Bruteforce_StillNotFastEnough() {
        $steps = 0;
        $length = strlen($this->instructions);

        $cursors = [];
        foreach ($this->mapping as $pos => $xxx) {
            if (substr($pos, -1) == 'A') $cursors[] = $pos;
        }

        $nbA = count($cursors);
        
        // local variables seem to be a bit faster
        $mapping = $this->mapping;
        $instructions = $this->instructions;

        // Transform all string keys to numeric are faster too
        // And doing mapping[dir][pos] instead of mapping[pos][dir] too
        $flip = array_flip(array_keys($mapping));

        $mapping2 = [];
        $corrects = [];

        foreach ($mapping as $pos => $dirs) {
            foreach ($dirs as $dir => $next) {
                $dir2 = ($dir == 'L') ? 1 : 2;
                $mapping2[$dir2][$flip[$pos]] = $flip[$next];
            }
            if ($pos[2] == 'Z') $corrects[] = $flip[$pos];
        }

        $instructions = strtr($instructions, ['L' => 1, 'R' => 2]);
        foreach ($cursors as $k => $v) $cursors[$k] = $flip[$v];
        $mapping = $mapping2;

        $corrects_flip = array_flip($corrects);

        $i = 0;
        $nbZ = 0;
        for (; $steps < 10000000; $steps++) {
            $dir = $instructions[$i];

            for ($k=$nbZ=0; $k<$nbA; $k++) {
                $cursors[$k] = $mapping[$dir][$cursors[$k]];
                if (isset($corrects_flip[$cursors[$k]])) $nbZ++;
            }

            $i++;
            if ($i >= $length) $i = 0;
            if ($nbZ == $nbA) break;
        }
        $steps++;

        return $steps;
    }

    public function part2WithShortcut_FailAgain() {
        $steps = 0;
        $length = strlen($this->instructions);

        $cursors = [];
        foreach ($this->mapping as $pos => $xxx) {
            if (substr($pos, -1) == 'A') {
                $cursors[] = new Cursor($pos, $this->mapping);
            }
        }
        $nbA = count($cursors);

        $i = 0;
        do {
            $nbZ = 0;
            $dir = $this->instructions[$i];

            for ($k=0; $k<$nbA; $k++) {
                $nbZ += $cursors[$k]->next($dir, $i, $steps);
            }

            $steps++;
            $i++;
            if ($i >= $length) $i = 0;
            if ($nbZ >= $nbA) break;
        } while ($steps < 100000);

        return $steps;
    }
}

class Cursor {
    public $shortcuts = [];
    public $routes = [];
    public $found = null;
    public $steps = 0;
    public function __construct(public $current, private &$mapping) {}

    public function next($dir, $step, $steps) {
        if (! is_null($this->found)) {
            if (($steps-$this->steps)%$this->found['nb'] == 0) return 1;
            return 0;
        }

        $key = $this->current."_".$dir."_".$step;

        if (isset($this->shortcuts[$key])) {
            $this->found = $this->shortcuts[$key];
            $this->steps = $steps;
            return $this->next($dir, $step, $steps);
        } 

        $this->routes[$key] = 0;
        foreach ($this->routes as $k => $xxx) $this->routes[$k]++;

        $next = $this->mapping[$this->current][$dir];

        $this->current = $next;

        if ($next[2] == 'Z') {
            foreach ($this->routes as $k => $n) {
                $this->shortcuts[$k] = [
                    'to' => $next."_".$dir.'_'.$step,
                    'nb' => $n
                ];
            }

            $this->routes = [];
            return 1;
        }

        return 0;
    }
}
