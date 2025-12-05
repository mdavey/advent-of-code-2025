<?php

    declare(strict_types=1);

    class Grid
    {
        public int $width
        {
            get
            {
                return $this->width;
            }
        }

        public int $height
        {
            get
            {
                return $this->height;
            }
        }

        private array $cells = [];

        public function loadData(string $filename) : void
        {
            $lines = file($filename);
            $this->height = count($lines);
            $this->width = strlen(trim($lines[0]));
            $this->cells = [];

            foreach($lines as $line)
            {
                $this->cells[] = str_split(trim($line));
            }
        }

        public function loadBlank(int $width, int $height) : void
        {
            $this->width = $width;
            $this->height = $height;
            $this->cells = [];

            for($y = 0; $y < $height; $y++)
            {
                $this->cells[] = str_split(str_repeat('.', $this->width));
            }
        }

        public function get(int $x, int $y) : ?string
        {
            if($x < 0)              { return null; }
            if($x >= $this->width)  { return null; }
            if($y < 0)              { return null; }
            if($y >= $this->height) { return null; }

            return $this->cells[$y][$x];
        }

        public function set(int $x, int $y, string $value) : void
        {
            if($this->get($x, $y) !== null)
            {
                $this->cells[$y][$x] = $value;
            }
        }

        public function getAdjacent(int $x, int $y) : array
        {
            return [
                $this->get($x-1, $y-1),
                $this->get($x+0, $y-1),
                $this->get($x+1, $y-1),

                $this->get($x-1, $y+0),
                // $this->get($x+0, $y+0),  // Not this one!
                $this->get($x+1, $y+0),

                $this->get($x-1, $y+1),
                $this->get($x+0, $y+1),
                $this->get($x+1, $y+1),
            ];
        }

        public function __toString() : string
        {
            $buffer  = '';
            $buffer .= sprintf("Grid Width: %s  Grid Height: %s\n", $this->width, $this->height);
            foreach($this->cells as $row)
            {
                $buffer .= implode('', $row) . PHP_EOL;
            }
            return trim($buffer); // just use lines[] ...
        }
    }

    function Day4_Part1(Grid $grid)
    {
        $results = new Grid();
        $results->loadBlank($grid->width, $grid->height);

        $count = 0;

        for($x = 0; $x < $grid->width; $x++)
        {
            for($y = 0; $y < $grid->height; $y++)
            {
                // Fill in our results grid.
                $results->set($x, $y, $grid->get($x, $y));

                // Is there a roll of paper at this coord?
                if($grid->get($x, $y) !== '@')
                {
                    continue;
                }

                $adjacent = $grid->getAdjacent($x, $y);
                $aggregate = @array_count_values($adjacent);

                if(!isset($aggregate['@']) || $aggregate['@'] < 4)
                {
                    $results->set($x, $y, 'x');
                    $count++;
                }
            }
        }

        printf("Part 1 Results:\n");
        printf("%s\n\n", $results);
        printf("Rolls of Paper to move: %s\n", $count);
    }


    /**
     * @param Grid $grid  Note: This get's modified during runtime
     * @return void
     */
    function Day4_Part2(Grid $grid)
    {
        $iterations = 0;
        $totalCount = 0;

        while(true)
        {
            $iterations++;
            $count = 0; // Just for this iteration

            for($x = 0; $x < $grid->width; $x++)
            {
                for ($y = 0; $y < $grid->height; $y++)
                {
                    // Is there a roll of paper at this coord?
                    if ($grid->get($x, $y) !== '@')
                    {
                        continue;
                    }

                    $adjacent = $grid->getAdjacent($x, $y);
                    $aggregate = @array_count_values($adjacent);

                    if (!isset($aggregate['@']) || $aggregate['@'] < 4)
                    {
                        $grid->set($x, $y, '.');
                        $count++;
                    }
                }
            }

            if($count === 0)
            {
                break;
            }

            $totalCount += $count;
        }

        printf("Part 2 Results:\n");
        printf("%s\n\n", $grid);
        printf("Rolls of Paper to move: %s\n", $totalCount);
        printf("Found in %d iterations\n", $iterations);
    }

    $start = microtime(true);

    $grid  = new Grid();
    // $grid->loadData('sample_input.txt');
    $grid->loadData('full_input.txt');

    // Day4_Part1($grid);
    Day4_Part2($grid);

    printf("%0.3f sec\n", microtime(true) - $start);