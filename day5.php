<?php
include(__DIR__."/_loader.php");

$parser = new Day5Parser("datasets/day5.txt");

$answer1 = $answer2 = 0;

$locations = [];
foreach ($parser->seeds as $seed) $locations[$seed] = $parser->getLocation($seed);
$answer1 = min($locations);

// PART 2 is about optimizing.
// So, first, approximating it by doing steps=100000
$i = $seedStart = 0;
$locations = [];
foreach ($parser->seeds as $seed) {
    $i++;
    if ($i%2 == 0) {
        $max = $seed;
        for ($j=0; $j<$max; $j+=100000) {
            $seed = $seedStart+$j;
            $locations[$seed] = $parser->getLocation($seed);
        }
        continue;
    }
    $seedStart = $seed;
}

// Okay, then we'll lookup the minimal in this $locations list
// We'll take the previous and next seed, and recalculate with steps=1
$key = array_search(min($locations), $locations);
$key2 = array_search($key, array_keys($locations));
list($start, $dontcare, $max) = array_keys(array_slice($locations, $key2-1, 3, true));

$locations = [];
for ($seed=$start; $seed<$max; $seed++) $locations[$seed] = $parser->getLocation($seed);
$answer2 = min($locations);


echo "Answer #1: ".$answer1.PHP_EOL;
echo "Answer #2: ".$answer2.PHP_EOL;



class Day5Parser {
    public $seeds = [];
    public $mappings = [];

    public function __construct($file) {
        $input = InputHelper::loadFile($file);
        $key = "undef";

        foreach ($input as $line) {
            if (! strlen($line)) continue;

            if (strpos($line, "seeds:") !== false) {
                $this->seeds = array_filter(str_getcsv(str_replace('seeds:', '', $line), ' '));
                continue;
            }

            if (strpos($line, "map:") !== false) {
                $key = str_replace(' map:', '', $line);
                continue;
            }

            list($destination, $source, $range) = str_getcsv($line, ' ');

            if (! isset($this->mappings[$key])) $this->mappings[$key] = [];

            $this->mappings[$key][] = [
                'min' => $source,
                'map' => $destination,
                'max' => $source+$range,
            ];
        }
    }

    public function getLocation($seed) {
        foreach ($this->mappings as $ranges) {
            foreach ($ranges as $range) {
                if ($seed >= $range['min'] && $seed <= $range['max']) {
                    $delta = $seed - $range['min'];
                    $seed = $range['map'] + $delta;
                    break;
                }
            }
        }
        return $seed;
    }
}
