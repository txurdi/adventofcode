<?php

namespace App\Command\y2024;

use App\Util\SolveHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'txurdi:2024:day11',
    description: 'Day 11 of the Advent code of 2024',
)]
class Day11Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="11";
    private mixed $blinking;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array|false $stones
     * @param array $newStones
     * @return array
     */
    public function blink(array $stones): array
    {
        $newStones = [];
        foreach ($stones as $stone) {
            $blinked = $this->applyRules($stone);
            $newStones[] = $blinked[0];
            if (isset($blinked[1])) {
                $newStones[] = $blinked[1];
            }
        }
        return $newStones;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('half', InputArgument::REQUIRED, 'First (1) or second (2) half of the puzzle')
        ;
    }
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $half = $input->getArgument('half');

        $this->io->warning('Day '.self::DAY.'..... GOOOOOOOO!!!');

        switch ($half) {
            case 1:
                if ($this->executeHalf1($input, $output)) {
                    return Command::SUCCESS;
                }
            case 2:
                if ($this->executeHalf2($input, $output)) {
                    return Command::SUCCESS;
                }

        }

        return Command::FAILURE;
    }

    private function executeHalf1(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $stones = preg_split("/[\s,]+/", (trim($data)));
        SolveHelper::dibujaSeguido($stones, '-');
        $startedAt = microtime(true);

//        for ($i=1; $i <= 25; $i++) {
//            $stones = $this->blinkRecursive($stones);
//            $this->io->success("Blink ".$i);
////            SolveHelper::dibujaSeguido($stones, ' ');
//        }

        for ($i=1; $i <= 25; $i++) {
            $newStones = [];
//            for ($j=count($stones)-1; $j>=0; $j--) {
//                $newStones[] = $this->applyRules($stones[$j]);
//            }
            foreach ($stones as $stone) {
                $blinked = $this->applyRules($stone);
                $newStones[] = $blinked[0];
                if (isset($blinked[1])) {
                    $newStones[] = $blinked[1];
                }
            }
            $stones = $newStones;
            $this->io->success("Blink ".$i);
//            SolveHelper::dibujaSeguido($stones, ' ');
        }

//        SolveHelper::dibujaSeguido($stones, ' ');
        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $result = count($newStones);
        $this->io->success('RESULT: '.$result);


        return 1;
    }

    private function blinkRecursive(array $stones) : array
    {
//        SolveHelper::dibujaSeguido($stones,'-');
//        if (count($stones) == 1) {
//            echo $stones[0];
//            return $this->applyRules($stones[0]);
//        }
        $left = $this->applyRules($stones[0]);
//        echo " - mod: ";
//        var_dump($left[0]);
//        $right = $stones;
        array_shift($stones);
        if (count($stones)>1) {
            $right = $this->blinkRecursive($stones);
        } else {
            $right = $this->applyRules($stones[0]);
        }
//        SolveHelper::dibujaSeguido($left,'<');
//        SolveHelper::dibujaSeguido($right,'>');
        array_unshift($right, $left[0]);
        if (count($left) > 1) {
            array_unshift($right, $left[1]);
        }
        return $right;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $stones = preg_split("/[\s,]+/", (trim($data)));
        SolveHelper::dibujaSeguido($stones, '-');
        $startedAt = microtime(true);

        $times = 75;
        $sum = 0;
        foreach ($stones as $stone) {
            $sum += $this->blinkTimes($stone, $times);
        }

        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('RESULT: '.$sum);

        return 1;
    }

    private function loadData(): string
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToString($filePath);
    }

    private function applyRules(int|string $number):array
    {
        echo "NUMBER: ".$number." => ";
        if ($number == 0) {
            $result = [1];
        } else {
            $numberStr = strval($number);
            $cont = strlen($numberStr);
            if ($cont % 2 == 0) {
                $left = substr($numberStr, 0, $cont / 2);
                $right = substr($numberStr, $cont / 2);
                $result = [(int)$left, (int)$right];
                echo "$result[0]-$result[1]\n";
            } else {
                $result = [$number * 2024];
                echo "$result[0]\n";
            }
        }
//        var_dump($result);
        return $result;
    }

    private function blinkTimes(int $stone, int $times) :int
    {
        $this->io->text("Blinking $stone ($times)");
        if (isset($this->blinking[$stone][$times])) return $this->blinking[$stone][$times];
        $stones = $this->applyRules($stone);
        if ($times > 1) {
            $suma = $this->blinkTimes($stones[0], $times - 1);
            if (count($stones) > 1) {
                $suma += $this->blinkTimes($stones[1], $times - 1);
            }
        } else {
            $this->io->text("#####################Blinked $stone ($times) = ".count($stones));
            return count($stones);
        }
        $this->blinking[$stone][$times] = $suma;
        $this->io->text("-------------------Blinked $stone ($times) = $suma");
        return $suma;
    }

}
