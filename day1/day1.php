<?php

    function Day1_Part1(string $filename): void
    {
        // Current position on the dial
        $position = 50;

        // Number of times the dial ended up on zero after a turn
        $time_left_on_zero = 0;

        foreach (file($filename) as $line)
        {
            // First letter is direction, 'L' or 'R'
            $direction = substr($line, 0, 1);

            // Rest of line is a integer amount
            $amount = (int)substr(trim($line), 1); // trim rather than -1 length just in case there's not a final newline

            // An amount of 100 results in the dial being at the same place
            // So R 550 is the same as R 50
            $extra_rotations = intdiv($amount, 100);  // 99 -> 0, 101 -> 1, 199 -> 1, etc
            $amount -= $extra_rotations * 100;

            if ($direction === 'L')
            {
                $position -= $amount;
                if ($position < 0)
                {
                    $position += 100;
                }
            } else if ($direction === 'R')
            {
                $position += $amount;
                if ($position > 99)
                {
                    $position -= 100;
                }
            }

            $at_zero = ($position === 0);

            if ($at_zero)
            {
                $time_left_on_zero++;
            }

            printf("Turned %s %d clicks.  Current position is %d.  Times at zero %d\n", $direction, $amount, $position, $time_left_on_zero);
        }
    }


    function Day1_Part2(string $filename): void
    {
        // Current position on the dial
        $position = 50;

        // Number of times the dial "passes" zero during (or at the end) or a turn
        $time_passes_zero = 0;

        foreach (file($filename) as $line)
        {
            // First letter is direction, 'L' or 'R'
            $direction = substr($line, 0, 1);

            // Rest of line is a integer amount
            $raw_amount = (int)substr(trim($line), 1); // trim rather than -1 length just in case there's not a final newline

            // An amount of 100 results in the dial being at the same place
            // So R 550 is the same as R 50
            $extra_rotations = intdiv($raw_amount, 100);  // 99 -> 0, 101 -> 1, 199 -> 1, etc
            $amount = $raw_amount - ($extra_rotations * 100);

            // Also count each one as passing zero
            $time_passes_zero += ($extra_rotations);

            if ($direction === 'L')
            {
                if ($position !== 0)
                {
                    $position -= $amount;
                    if ($position < 0)
                    {
                        $position += 100;
                        $time_passes_zero++;
                    }
                }
                else
                {
                    $position -= $amount;
                    if ($position < 0)
                    {
                        $position += 100;
                    }
                }

                if ($position === 0)
                {
                    $time_passes_zero++;
                }
            }
            else if ($direction === 'R')
            {
                $position += $amount;
                if ($position > 99)
                {
                    $position -= 100;
                    $time_passes_zero++;
                }
            }

            printf("Turned %s %d (%d) clicks.  Current position is %d.  Times passing zero %d\n",
                $direction, $amount, $raw_amount, $position, $time_passes_zero);
        }
    }

    // I could not get the above function to work correctly.  So here I made a reference version so I could compare
    // and find the small errors I was making above.  This version is very slow, but has a smaller chance of making
    // off-by-one or logic errors as there are ~6 lines of actual code.

    class Day1_Part2_Dial
    {
        private int $size;
        public int $position {
            get {
                return $this->position;
            }
        }
        public int $timesOnZero {
            get {
                return $this->timesOnZero;
            }
        }

        public function __construct(int $size, int $position)
        {
            $this->size = $size;
            $this->position = $position;
            $this->timesOnZero = 0;
        }

        public function turnRight(int $amount): void
        {
            for ($i = 0; $i < $amount; $i++)
            {
                $this->incPosition();
            }
        }

        public function turnLeft(int $amount): void
        {
            for ($i = 0; $i < $amount; $i++)
            {
                $this->decPosition();
            }
        }

        private function incPosition(): void
        {
            $this->position++;
            if ($this->position > ($this->size - 1))
            {
                $this->position = 0;
            }

            if ($this->position === 0)
            {
                $this->timesOnZero++;
            }
        }

        private function decPosition(): void
        {
            $this->position--;
            if ($this->position < 0)
            {
                $this->position = $this->size - 1;
            }

            if ($this->position === 0)
            {
                $this->timesOnZero++;
            }
        }
    }

    function Day1_Part2_Reference(string $filename): void
    {
        $dial = new Day1_Part2_Dial(100, 50);

        foreach (file($filename) as $line)
        {
            // First letter is direction, 'L' or 'R'
            $direction = $line[0];

            // Rest of line is a integer amount
            $amount = (int)substr(trim($line), 1);

            match ($direction)
            {
                'L' => $dial->turnLeft($amount),
                'R' => $dial->turnRight($amount),
            };

            printf("Turned %s %d clicks.  Current position is %d.  Times passing zero %d\n",
                $direction, $amount, $dial->position, $dial->timesOnZero);
        }
    }


    Day1_Part1('sample_input.txt');
    Day1_Part1('full_input.txt');

    echo "----\n";

    Day1_Part2('sample_input.txt');
    Day1_Part2('full_input.txt');

    echo "----\n";

    Day1_Part2_Reference('sample_input.txt');
    Day1_Part2_Reference('full_input.txt');
