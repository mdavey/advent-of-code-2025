<?php

    /**
     * Wrote this assuming I'd be doing permutations and thought it would be easier to abstract that in here
     */
    class Bank
    {
        /**
         * @var int[]
         */
        public array $batteries = [] {
            get {
                return $this->batteries;
            }
        }

        public function __construct(array $batteries = [])
        {
            $this->batteries = $batteries;
        }

        /**
         * Load a string like:  '12345'
         * @param string $input
         * @return void
         */
        public function setBatteriesFromString(string $input) : void
        {
            $this->batteries = array_map('intval', str_split(trim($input)));
        }

        public function __toString() : string
        {
            return sprintf("Bank: %s", implode("", $this->batteries));
        }

        public static function NewFromString(string $input) : Bank
        {
            $x = new self();
            $x->setBatteriesFromString($input);
            return $x;
        }
    }

    /**
     * @param string $filename
     * @return Bank[]
     */
    function Day3_LoadBatteryBanks(string $filename) : array
    {
        return array_map( fn($line) => Bank::NewFromString($line), file($filename));
    }

    /**
     * @param Bank[] $banks
     * @return void
     */
    function Day3_Part1(array $banks) : void
    {
        $sum = 0;

        foreach($banks as $bank)
        {
            $totalBatteries = count($bank->batteries);

            $firstHighestIndex = 0;
            $firstHighestValue = 0;
            for($a = 0; $a < $totalBatteries-1; $a++)
            {
                if($bank->batteries[$a] > $firstHighestValue)
                {
                    $firstHighestIndex = $a;
                    $firstHighestValue = $bank->batteries[$a];
                }
            }

            $secondHighestValue = 0;
            for($b = $firstHighestIndex+1; $b < $totalBatteries; $b++)
            {
                if($bank->batteries[$b] > $secondHighestValue)
                {
                    $secondHighestValue = $bank->batteries[$b];
                }
            }

            printf("%s -> %d%d\n", $bank, $firstHighestValue, $secondHighestValue);
            $sum += (int)"{$firstHighestValue}{$secondHighestValue}";
        }

        printf("Total output joltage: %d\n", $sum);
    }

    /**
     * @param Bank[] $banks
     * @param int $numberOfBatteriesToFind Because this is generic, passing '2' should return the same output as part 1
     * @return void
     */
    function Day3_Part2(array $banks, int $numberOfBatteriesToFind = 12) : void
    {
        $nextBiggest = static function (array $batteries, int $startOffset, int $leaveOffset) : array
        {
            $totalBatteries = count($batteries);

            $highestIndex = 0;
            $highestValue = 0;

            for($i = $startOffset; $i < $totalBatteries-$leaveOffset; $i++)
            {
                if($batteries[$i] > $highestValue)
                {
                    $highestIndex = $i;
                    $highestValue = $batteries[$i];
                }
            }

            return [$highestIndex, $highestValue];
        };

        $sum = 0;
        foreach($banks as $bank)
        {
            $highestIndex = -1;

            $selectedBatteries = [];

            for($i = $numberOfBatteriesToFind; $i > 0; $i--)
            {
                [$highestIndex, $highestValue] = $nextBiggest($bank->batteries, $highestIndex+1, $i-1);
                $selectedBatteries[] = $highestValue;
            }

            $value = (int)implode($selectedBatteries);
            $sum += $value;
            printf("%s -> %d\n", $bank, $value);
        }

        printf("Total output joltage: %d\n", $sum);
    }


    $sampleInput = Day3_LoadBatteryBanks('sample_input.txt');
    $fullInput = Day3_LoadBatteryBanks('full_input.txt');

    Day3_Part1($sampleInput);
    Day3_Part1($fullInput);

    Day3_Part2($sampleInput, 2);
    Day3_Part2($sampleInput, 12);
    Day3_Part2($fullInput, 12);
