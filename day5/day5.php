<?php

    declare(strict_types=1);

    ini_set('memory_limit', '20G');

    class IngredientDatabase_Range
    {
        public int $start;
        public int $end;

        public function __construct(int $start, int $end)
        {
            $this->start = $start;
            $this->end = $end;
        }

        public function elementInRange(int $id) : bool
        {
            return $id >= $this->start && $id <= $this->end;
        }

        /**
         * Hehe
         * @return int[]
         */
        public function flatten() : array
        {
            echo ($this->end - $this->start) . PHP_EOL;
            $all = [];
            for($i = $this->start; $i <= $this->end; $i++)
            {
                $all[] = $i;
            }
            return $all;
        }

        public function overlap(self $other) : bool
        {
            if($this->start >= $other->start && $this->start <= $other->end) { return true; }
            if($this->end >= $other->start && $this->end <= $other->end) { return true; }

            if($other->start >= $this->start && $other->start <= $this->end) { return true; }
            if($other->end >= $this->start && $other->end <= $this->end) { return true; }

            return false;
        }

        public function minStart(self $other) : int
        {
            return min($this->start, $other->start);
        }

        public function maxEnd(self $other) : int
        {
            return max($this->end, $other->end);
        }

        public function size() : int
        {
            return ($this->end - $this->start) + 1;
        }

        public function __toString() : string
        {
            return "{$this->start}-{$this->end}";
        }
    }

    class IngredientDatabase
    {
        /**
         * @var IngredientDatabase_Range[]
         */
        public array $freshRanges;

        /**
         * @var int[]
         */
        public array $ingredientIds;

        public function __construct(array $freshRanges = [], array $ingredientId = [])
        {
            $this->freshRanges = $freshRanges;
            $this->ingredientIds = $ingredientId;
        }

        public function isIngredientIdFresh(int $id) : bool
        {
            foreach($this->freshRanges as $freshRange)
            {
                if($freshRange->elementInRange($id))
                {
                    return true;
                }
            }

            return false;
        }

        public static function FromFile(string $filename) : self
        {
            $db = new self();

            $lines = file($filename);
            $numLines = count($lines);

            for($i = 0; $i < $numLines; $i++)
            {
                $line = trim($lines[$i]);

                if($line === '') { continue; }

                if(str_contains($line, '-'))
                {
                    [$start, $end] = explode('-', $line);
                    $db->freshRanges[] = new IngredientDatabase_Range((int)$start, (int)$end);
                }
                else
                {
                    $db->ingredientIds[] = (int)$line;
                }
            }

            return $db;
        }
    }

    function Day5_Part1(string $filename) : void
    {
        $db = IngredientDatabase::FromFile($filename);

        $count = 0;
        foreach($db->ingredientIds as $id)
        {
            if($db->isIngredientIdFresh($id))
            {
                $count++;
            }
        }

        printf("Number of fresh ranges: %d.  Number of ingredients: %d.  Number of fresh ingredients: %d\n",
            count($db->freshRanges),
            count($db->ingredientIds),
            $count);
    }

    // Stupid Way, Pt 1  turns out there are a **lot** of big numbers :-)
    function Day5_Part2_Stupid(string $filename) : void
    {
        $db = IngredientDatabase::FromFile($filename);

        $allIds = [];

        foreach($db->freshRanges as $range)
        {
            // manually merge and make unique
            foreach($range->flatten() as $id)
            {
                $allIds[$id] = true;
            }
        }

        $allIds = array_keys($allIds);

        printf("Number of ids considered fresh: %d.\n", count($allIds));
    }

    /**
     * We are going to have to merge overlapping ranges until we are left with a set of unique ranges
     * Then we can count the number of elements in each and return the sum
     * @param string $filename
     * @return void
     */
    function Day5_Part2(string $filename) : void
    {
        $db = IngredientDatabase::FromFile($filename);
        $ranges = $db->freshRanges;
        $numRanges = count($ranges);

        // Sort the ranges by the start value to max merging easier
        usort($ranges, function($a, $b) {
            return $a->start - $b->start;
        });

        $newRanges = [];

        for($a = 0; $a < $numRanges; $a++)
        {
            if(!isset($ranges[$a])) { continue; }

            $newRange = new IngredientDatabase_Range($ranges[$a]->start, $ranges[$a]->end);

            for ($b = $a + 1; $b < $numRanges; $b++)
            {
                if(!isset($ranges[$b])) { continue; }

                if ($newRange->overlap($ranges[$b]))
                {
                    // printf("A overlaps with B\n");
                    // printf("%s\n%s\n\n", $newRange, $ranges[$b]);
                    //
                    // printf("Min start: %d\n", $newRange->minStart($ranges[$b]));
                    // printf("Max end: %d\n\n", $newRange->maxEnd($ranges[$b]));

                    $newRange->start = $newRange->minStart($ranges[$b]);
                    $newRange->end = $newRange->maxEnd($ranges[$b]);

                    unset($ranges[$b]);
                }
            }

            $newRanges[] = $newRange;
        }

        $count = 0;
        foreach($newRanges as $range)
        {
            // printf("%s\n", $range);
            $count += $range->size();
        }

        printf("Number of ids considered fresh: %d.\n", $count);
    }

    $start = microtime(true);

    // Day5_Part1('sample_input.txt');
    Day5_Part1('full_input.txt');
    // Day5_Part2('sample_input.txt');
    Day5_Part2('full_input.txt');

    printf("%0.3f sec\n", microtime(true) - $start);