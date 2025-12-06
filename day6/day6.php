<?php

    class Grid_Part1
    {
        public array $rows = [];
        public array $operators;

        public static function LoadFromFile(string $filename) : self
        {
            $grid = new self();

            foreach(file($filename) as $line)
            {
                if(str_contains($line, '+'))
                {
                    $grid->operators = preg_split('/\s+/', trim($line));
                }
                else
                {
                    $grid->rows[] = array_map('intval', preg_split('/\s+/', trim($line)));
                }
            }

            return $grid;
        }

        public function numRows() : int
        {
            return count($this->rows);
        }

        public function numColumns() : int
        {
            return count($this->rows[0]);
        }

        /**
         * @param int $column
         * @return int[]
         */
        public function getColumn(int $column) : array
        {
            $values = [];
            for($row = 0; $row < $this->numRows(); $row++)
            {
                $values[] = $this->rows[$row][$column];
            }
            return $values;
        }

        public function getOperator(int $column) : string
        {
            return $this->operators[$column];
        }

        public function __toString() : string
        {
            $buffer = [];
            $buffer[] = sprintf("Grid %d x %d", count($this->rows[0]), count($this->rows));
            foreach($this->rows as $row)
            {
                $buffer[] = implode(' ', $row);
            }
            $buffer[] = implode(' ', $this->operators);
            return implode("\n", $buffer);
        }
    }

    function Day6_Part1(Grid_Part1 $grid) : void
    {
        $sum = 0;

        for($col = 0; $col < $grid->numColumns(); $col++)
        {
            $values = $grid->getColumn($col);
            $operator = $grid->getOperator($col);

            if($operator === '+')
            {
                $sum += array_reduce($values, function($carry, $item)
                {
                    return $carry + $item;
                }, 0);
            }
            else if($operator === '*')
            {
                $sum += array_reduce($values, function($carry, $item)
                {
                    return $carry * $item;
                }, 1);
            }
        }

        printf("Sum of homework for grid of %dx%d: %s\n", $grid->numColumns(), $grid->numRows(), $sum);
    }


    // Wow, Grid is useless for Part2.  I hate you.
    function Day6_Part2(string $filename) : void
    {
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        $operators = preg_split('/\s+/', array_pop($lines));


        // Step 1: Find columns
        $separatorPosition = [];
        $numColumns = strlen($lines[0]);
        for($col = 0; $col < $numColumns; $col++)
        {
            // Is there a blank space vertically?
            // if(($lines[0][$col] === ' ') && ($lines[1][$col] === ' ') && ($lines[2][$col] === ' ')) // for sample
            if(($lines[0][$col] === ' ') && ($lines[1][$col] === ' ') && ($lines[2][$col] === ' ') && ($lines[3][$col] === ' '))
            {
                $separatorPosition[] = $col+1;
            }
        }


        // Step 2: Manually add in the very first, and last columns
        $separatorPosition = [0, ...$separatorPosition, strlen($lines[0])+1];
        // echo "Column Separators: " . implode('|', $separatorPosition) . PHP_EOL;


        // Step 3: Split the data up into those fixed width columns
        $grid = [];
        foreach($lines as $line)
        {
            $row = [];

            for($i = 0; $i < count($separatorPosition)-1; $i++)
            {
                $row[] = substr($line, $separatorPosition[$i], $separatorPosition[$i+1]-$separatorPosition[$i]-1);
            }

            $grid[] = $row;
        }


        // Step 4: Work out how to add up a grid of numbers
        // e.g.  ['123', ' 45', '  6'], '*'
        //       -> 356 * 24 * 1 = 8544
        $sumColumn = function($values, $operator) : int
        {
            $verticalNumbers = [];
            for($place = strlen($values[0]); $place > 0; $place--)
            {
                $digits = '';

                foreach($values as $row)
                {
                    $digit = substr($row, $place-1, 1);
                    $digits .= $digit;
                }

                $verticalNumbers[] = (int)$digits;
            }

            echo implode($operator, $verticalNumbers) . PHP_EOL;

            if($operator === '+')
            {
                return array_reduce($verticalNumbers, function($carry, $item)
                {
                    return $carry + $item;
                }, 0);
            }

            if($operator === '*')
            {
                return array_reduce($verticalNumbers, function($carry, $item)
                {
                    return $carry * $item;
                }, 1);
            }

            throw new InvalidArgumentException("Unknown operator");
        };


        // Step 5: Group of the data by columns
        $numColumns = count($grid[0]);
        $sum = 0;
        for($col = 0; $col < $numColumns; $col++)
        {
            $data = [];
            foreach($grid as $row)
            {
                $data[] = $row[$col];
            }
            $sum += $sumColumn($data, $operators[$col]);
        }

        printf("Sum of homework for grid part 2: %s\n", $sum);
    }

    $grid = Grid_Part1::LoadFromFile('sample_input.txt');
    Day6_Part1($grid);

    $grid = Grid_Part1::LoadFromFile('full_input.txt');
    Day6_Part1($grid);

    // Day6_Part2('sample_input.txt');
    Day6_Part2('full_input.txt');