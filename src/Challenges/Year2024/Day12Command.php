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
    name: 'txurdi:2024:day12',
    description: 'Day 12 of the Advent code of 2024',
)]
class Day12Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="12";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $letters
     * @return void
     */
    public function dibujaPuntosLetras(array $letters): void
    {
        echo "LETRAS:\n";
        foreach ($letters as $letter => $letterNumbers) {
            foreach ($letterNumbers as $letterNumber => $points) {
                echo $letter . $letterNumber;
                foreach ($points as $nl => $lines) {
                    foreach ($lines as $nr => $contentLetter) {
                        echo " ($nl-$nr)";
                    }
                }
                echo "\n";
            }
        }
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
        $map = $this->loadData();
        $startedAt = microtime(true);

        $mapeado = $map;
        $sections = [];
//        SolveHelper::dibujaMapa($mapeado);
        foreach ($map as $nl=>$line) {
            foreach ($line as $nr => $letter) {
                if ($mapeado[$nl][$nr] == '.') {
                    continue;
                }
                $letterNumber = 0;
                if (isset($sections[$letter])) {
                    $letterNumber = count($sections[$letter]);
                }
                $contiguas = $this->dameContiguasQueCoincidan($nl, $nr, $letter, $mapeado);
//                SolveHelper::dibujaMapa($mapeado);
                foreach ($contiguas as $nl2 => $line2) {
                    foreach ($line2 as $nr2 => $letter2) {
                        $sections[$letter][$letterNumber][$nl2][$nr2] = $letter;
                    }
                }
//                echo $letter.$letterNumber.'-';
//                $this->dibujaPuntosLetras($sections);
            }
        }
//        $this->dibujaPuntosLetras($sections);

//        $letters = [];
//        foreach ($map as $nl=>$line) {
//            foreach ($line as $nr => $letter) {
//                $letters[$letter][$nl][$nr] = $letter;
//            }
//        }
//        echo "LETRAS:\n";
//        foreach ($letters as $letter=>$points) {
//            echo $letter;
//            foreach ($points as $nl=>$lines) {
//                foreach ($lines as $nr=>$contentLetter) {
//                    echo " ($nl-$nr)";
//                }
//            }
//            echo "\n";
//        }
//        $sections = [];
//        echo "organizando ZONAS:\n";
//        foreach ($letters as $letter => $points) {
//            foreach ($points as $nl=>$lines) {
//                foreach ($lines as $nr=>$contentLetter) {
//                    $sectionLetter = (isset($sections[$letter])) ? $sections[$letter] : [];
//                    $sectionNumber = $this->searchContigous($nl, $nr, $sectionLetter);
//                    echo "--------[$letter][$sectionNumber][$nl][$nr]\n";
//                    $sections[$letter][$sectionNumber][$nl][$nr] = $letter;
//                }
//            }
//        }
//        echo "ZONAS:\n";
//        foreach ($sections as $letter=>$letterZones) {
//            foreach ($letterZones as $sectionNumber => $points) {
//                echo $letter.$sectionNumber.':';
//                foreach ($points as $nl=>$lines) {
//                    foreach ($lines as $nr=>$contentLetter) {
//                        echo " ($nl-$nr)";
//                    }
//                }
//                echo "\n";
//            }
//        }
        $price = 0;
        echo "CALCULOS:\n";
        foreach ($sections as $letter => $sectionZones) {
            foreach ($sectionZones as $sectionName => $points) {
                $area = $this->calculateArea($points);
                $perimeter = $this->calculatePerimeter($points);
//            exit();
                $sectionPrice = $area * $perimeter;
                $this->io->text("$letter$sectionName -> ($area)x($perimeter)=$sectionPrice");
                $price += $sectionPrice;
            }
        }

        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('RESULT: '.$price);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $map = $this->loadData();
        $startedAt = microtime(true);

        $mapeado = $map;
        $sections = [];
//        SolveHelper::dibujaMapa($mapeado);
        foreach ($map as $nl=>$line) {
            foreach ($line as $nr => $letter) {
                if ($mapeado[$nl][$nr] == '.') {
                    continue;
                }
                $letterNumber = 0;
                if (isset($sections[$letter])) {
                    $letterNumber = count($sections[$letter]);
                }
                $contiguas = $this->dameContiguasQueCoincidan($nl, $nr, $letter, $mapeado);
//                SolveHelper::dibujaMapa($mapeado);
                foreach ($contiguas as $nl2 => $line2) {
                    foreach ($line2 as $nr2 => $letter2) {
                        $sections[$letter][$letterNumber][$nl2][$nr2] = $letter;
                    }
                }
//                echo $letter.$letterNumber.'-';
//                $this->dibujaPuntosLetras($sections);
            }
        }
        $this->dibujaPuntosLetras($sections);
        $price = 0;
        echo "CALCULOS:\n";
        foreach ($sections as $letter => $sectionZones) {
            foreach ($sectionZones as $sectionName => $points) {
                $area = $this->calculateArea($points);
                $perimeter = $this->calculatePerimeterEspecial($points);
//            exit();
                $sectionPrice = $area * $perimeter;
                $this->io->text("$letter$sectionName -> ($area)x($perimeter)=$sectionPrice");
                $price += $sectionPrice;
            }
        }

        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('RESULT: '.$price);

        return 1;
    }

    private function loadData(): array
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_testb.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2b.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_testc.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2c.txr";

        return SolveHelper::fileToArrayByLineAndChar($filePath);
    }

    private function searchContigous(int $line, int $row, array $sections) :int
    {
        foreach ($sections as $sectionNumber => $points) {
            if (isset($points[$line+1][$row])) {
                return $sectionNumber;
            }
            if (isset($points[$line-1][$row])) {
                return $sectionNumber;
            }
            if (isset($points[$line][$row+1])) {
                return $sectionNumber;
            }
            if (isset($points[$line][$row-1])) {
                return $sectionNumber;
            }
        }
        return count($sections);
    }

    private function calculateArea(array $points): int
    {
        $sum = 0;
        foreach ($points as $nl=>$lines) {
            $sum += count($lines);
        }
        return $sum;

    }

    private function calculatePerimeter(array $points) : int
    {
        $perimeter = 0;
        foreach ($points as $nl=>$lines) {
            foreach ($lines as $nr=>$cell) {
                $line = $nl;
                $row = $nr;
//                var_dump((isset($points[$line-1][$row]))? $points[$line-1][$row]:'NO');
//                var_dump((isset($points[$line+1][$row]))? $points[$line+1][$row]:'NO');
//                var_dump((isset($points[$line][$row-1]))? $points[$line][$row-1]:'NO');
//                var_dump((isset($points[$line][$row+1]))? $points[$line][$row+1]:'NO');
//                var_dump($points);
                if (!isset($points[$line-1][$row])) {
//                    echo '^';
                    $perimeter += 1;
                }
                if (!isset($points[$line+1][$row])) {
//                    echo 'v';
                    $perimeter += 1;
                }
                if (!isset($points[$line][$row-1])) {
//                    echo '<';
                    $perimeter += 1;
                }
                if (!isset($points[$line][$row+1])) {
//                    echo '>';
                    $perimeter += 1;
                }
//                echo "$line-$row\n";
//                var_dump($perimeter);
//                exit();
            }
        }
        return $perimeter;
    }

    private function dameContiguasQueCoincidan(int|string $nl, int|string $nr, mixed $letter, array &$map): array
    {
        $contiguas[$nl][$nr] = $letter;
        if ((isset($map[$nl+1][$nr])) && ($map[$nl+1][$nr] == $letter)) {
            $map[$nl+1][$nr] = '.';
            $masContiguas = $this->dameContiguasQueCoincidan($nl+1, $nr, $letter, $map);
            $contiguas = $this->mergear($contiguas, $masContiguas);
        }
        if ((isset($map[$nl-1][$nr])) && ($map[$nl-1][$nr] == $letter)) {
            $map[$nl-1][$nr] = '.';
            $masContiguas = $this->dameContiguasQueCoincidan($nl-1, $nr, $letter, $map);
            $contiguas = $this->mergear($contiguas, $masContiguas);
        }
        if ((isset($map[$nl][$nr+1])) && ($map[$nl][$nr+1] == $letter)) {
            $map[$nl][$nr+1] = '.';
            $masContiguas = $this->dameContiguasQueCoincidan($nl, $nr+1, $letter, $map);
            $contiguas = $this->mergear($contiguas, $masContiguas);
        }
        if ((isset($map[$nl][$nr-1])) && ($map[$nl][$nr-1] == $letter)) {
            $map[$nl][$nr-1] = '.';
            $masContiguas = $this->dameContiguasQueCoincidan($nl, $nr-1, $letter, $map);
            $contiguas = $this->mergear($contiguas, $masContiguas);
        }
        return $contiguas;
    }

    private function mergear(array $contiguas, array $masContiguas):array
    {
        foreach ($masContiguas as $nl=>$lines) {
            foreach ($lines as $nr=>$mcl) {
                $contiguas[$nl][$nr] = $contiguas;
            }
        }
        return $contiguas;
    }

    private function calculatePerimeterEspecial(mixed $points)
    {
        $perimeter = 0;
        $perimetral = [];
        foreach ($points as $nl=>$lines) {
            foreach ($lines as $nr=>$cell) {
                $line = $nl;
                $row = $nr;
                if (!isset($points[$line-1][$row])) {
//                    echo '^';
                    $perimetral['^'][$line][$row] = 1;
                    $perimeter += 1;
                }
                if (!isset($points[$line+1][$row])) {
//                    echo 'v';
                    $perimetral['v'][$line][$row] = 1;
                    $perimeter += 1;
                }
                if (!isset($points[$line][$row-1])) {
//                    echo '<';
                    $perimetral['<'][$row][$line] = 1;
                    $perimeter += 1;
                }
                if (!isset($points[$line][$row+1])) {
//                    echo '>';
                    $perimetral['>'][$row][$line] = 1;
                    $perimeter += 1;
                }
            }
        }
        return $this->contarLadosCompletos($perimetral);
//        var_dump($perimetral);
//        exit();
//        return $perimeter;
    }

    private function contarLadosCompletos(array $perimetral) : int
    {
        var_dump($perimetral);
        $perimeter = 0;
        foreach ($perimetral as $tipo=>$puntos) {
            ksort($puntos);
            $cont[$tipo] = 0;
            foreach ($puntos as $nl=>$lines) {
                ksort($lines);
                $first[$tipo] = true;
                foreach ($lines as $nr=>$cell) {
                    if ($tipo=='>') {
                        echo "$tipo-$nl$nr\n";
                        var_dump(/* $inicio[$tipo] ,*/(isset($ladoanterior[$tipo])? $ladoanterior[$tipo]:'-'),$cont[$tipo]);
                    }
                    if ($first[$tipo]) {
                        $first[$tipo] = false;
                        $inicio[$tipo] = true;
                        $cont[$tipo] ++;
                        $ladoanterior[$tipo] = $nr-1;
                    }
//                    if ($inicio[$tipo]) {
//                        $inicio[$tipo] = false;
//                    }
                    if ($ladoanterior[$tipo] != $nr-1) {
//                        $inicio[$tipo] = true;
                        $cont[$tipo] ++;
                    }
                    $ladoanterior[$tipo] = $nr;
                }
            }
            $perimeter += $cont[$tipo];
        }
        var_dump($cont);
//        exit();
        return $perimeter;
    }

//    private function contarLadosCompletos(array $perimetral) : int
//    {
//        var_dump($perimetral);
//        $perimeter = 0;
//        foreach ($perimetral as $tipo=>$puntos) {
//            $cont[$tipo] = 0;
//            foreach ($puntos as $nl=>$lines) {
//                $inicio[$tipo] = true;
//                $cont[$tipo] ++;
//                $ladoanterior[$tipo] = -1;
//                foreach ($lines as $nr=>$cell) {
//                    if ($tipo=='>') {
//                        echo "$nl$nr\n";
//                        var_dump($inicio[$tipo],(isset($ladoanterior[$tipo])? $ladoanterior[$tipo]:'-'),$cont[$tipo]);
//                    }
//                    if ($inicio[$tipo]) {
//                        $inicio[$tipo] = false;
//                    }
//                    if ($ladoanterior[$tipo] == $nr-1) {
//                        $ladoanterior[$tipo] = $nr;
//                    } else {
//                        $inicio[$tipo] = true;
//                        $cont[$tipo] ++;
//                    }
//                }
//            }
//            $perimeter += $cont[$tipo];
//        }
//        var_dump($cont);
//        exit();
//        return $perimeter;
//    }
}
