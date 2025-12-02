<?php

    class Range
    {
        public int $start;
        public int $end;

        public function __construct(int $start, int $end)
        {
            $this->start = $start;
            $this->end = $end;
        }

        public function allValues() : array
        {
            $all = [];
            for($i = $this->start; $i <= $this->end; $i++)
            {
                $all[] = $i;
            }
            return $all;
        }

        public function __toString() : string
        {
            return "{$this->start}-{$this->end}";
        }
    }

    /**
     * @param Range[] $ranges
     * @return void
     */
    function Day2_Part1(array $ranges) : void
    {
        $isRepeated = static function(int $x) : bool
        {
            $s = (string)$x;
            $a = substr($s, 0, strlen($s) / 2);
            $b = substr($s, strlen($s) / 2);

            // printf("s:%s a:%s b:%s\n", $s, $a, $b);

            return $a === $b;
        };

        $sum = 0;

        foreach($ranges as $range)
        {
            foreach($range->allValues() as $value)
            {
                if($isRepeated($value))
                {
                    // printf("Invalid ID: %d\n", $value);
                    $sum += $value;
                }
            }
        }

        printf("Part 1 - Sum of invalid IDs: %d\n", $sum);
    }

    /**
     * @param Range[] $ranges
     * @return void
     */
    function Day2_Part2(array $ranges) : void
    {
        // Take a string, and length, and return an array of string pieces each a maximum of $pieceLength length
        // e.g.  $splitString('123456', 2) -> ['12', '34', '56']
        //       $splitString('123456', 3) -> ['123', '456']
        $splitString = function (string $s, int $pieceLength) : array
        {
            $parts = [];
            while(strlen($s) > 0)
            {
                $parts[] = substr($s, 0, $pieceLength);
                $s = substr($s, $pieceLength);
            }

            return $parts;
        };

        $isRepeated = function(int $x) use ($splitString): bool
        {
            // Turn the number into a string
            $s = (string)$x;
            $l = strlen($s);

            // Find all the ways to divide up the string into even pieces
            // For a string on length 12: 6, 4, 2, 1
            //
            // Then we can split the string into pieces, first of length 6, then length 4, then length 2, and finally length 1
            for($div = (int)$l/2; $div >= 1; $div--)
            {
                if($l % $div !== 0)
                {
                    continue;
                }

                // Now split it up to even pieces and see if they are all the same
                // e.g.  $splitString('121212', 2) -> ['12','12','12'] -> array_unique -> ['12'] -> therefor repeating
                $parts = $splitString($s, $div);

                if(count(array_unique($parts)) === 1)
                {
                    // printf("s:%s l:%d div:%d\n", $s, $l, $div);
                    return true;
                }
            }

            return false;
        };

        $sum = 0;

        foreach($ranges as $range)
        {
            foreach($range->allValues() as $value)
            {
                if($isRepeated($value))
                {
                    // printf("Invalid ID: %d\n", $value);
                    $sum += $value;
                }
            }
        }

        printf("Part 2 - Sum of invalid IDs: %d\n", $sum);
    }

    function Day2_LoadRanges(string $filename) : array
    {
        $str = file_get_contents($filename);
        $pairs = explode(',', $str);

        $ranges = [];

        foreach ($pairs as $pair)
        {
            [$start, $end] = explode('-', $pair);
            $ranges[] = new Range($start, $end);
        }

        return $ranges;
    }

    $sampleRanges = Day2_LoadRanges('sample_input.txt');
    // Day2_Part1($sampleRanges);
    // Day2_Part2($sampleRanges);

    $fullRanges = Day2_LoadRanges('full_input.txt');
    Day2_Part1($fullRanges);
    Day2_Part2($fullRanges);
