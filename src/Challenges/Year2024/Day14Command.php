<?php

namespace App\Command\y2024;

use App\Util\SolveHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'txurdi:2024:day14',
    description: 'Day 14 of the Advent code of 2024',
)]
class Day14Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="14";
    private ?string $test=null;
    private int $nCols=0;
    private int $nLines=0;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('half', InputArgument::REQUIRED, 'First (1) or second (2) half of the puzzle')
            ->addOption('test', 't', InputOption::VALUE_OPTIONAL, 'Test')
        ;
    }
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $half = $input->getArgument('half');
        $this->test = $input->getOption('test');

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
        $points = [];
        foreach ($data as $line) {
            $point = [
                'x' => (int)substr($line[0],2),
                'y' => (int)$line[1],
                'vx' => (int)substr($line[2],2),
                'vy' => (int)$line[3],
            ];
            $points[] = $point;
        }
        $startedAt = microtime(true);
//        $this->dibujaCoordenadas($points);

        $newPoints = [];
        $times =100;
        foreach ($points as $point) {
            $newPoint = $this->calculaPosicion($point['x'], $point['y'], $point['vx'], $point['vy'], $times);
            $newPoints[] = $newPoint;
        }
//        $this->dibujaCoordenadas($newPoints);

        $pointsPerSector = $this->sumaPuntosSectorPorSector($newPoints);

//        var_dump($pointsPerSector);

        $result = 1;
        foreach ($pointsPerSector as $points) {
            $result *= $points;
        }

        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function dibujaCoordenadas($coordenadas)
    {
        $this->io->info("MAPA:");
        $map = [];
        for ($y=0; $y < $this->nLines; $y++) {
            for ($x=0; $x < $this->nCols; $x++) {
                  $map[$x][$y] = '0';
            }
        }
        foreach ($coordenadas as $coordenada) {
            $map[$coordenada['x']][$coordenada['y']] += 1;
        }
        for ($y=0; $y < 50; $y++) {
            for ($x=0; $x < $this->nCols; $x++) {
                if ($map[$x][$y]>0) {
                    echo $map[$x][$y];
                } else {
                    echo '.';
                }
            }
            echo "\n";
        }
    }


    private function calculaPosicion(mixed $x, mixed $y, mixed $vx, mixed $vy, $times)
    {
        $newX = (($times*($vx+$this->nCols)) +$x) % $this->nCols;
        $newY = (($times*($vy+$this->nLines)) +$y) % $this->nLines;

        return [
            'x'=>$newX,
            'y'=>$newY
        ];


//        $x = 2;
//        $y = 4;
//        $vx = 2;
//        $vy = -3;
//        for ($i = 0; $i <= 16; $i++) {
//            echo $i.'     ';
//            echo ($i % $this->nLines).'     ';
//            echo ($i * $vy) % $this->nLines.'     ';
//            echo ($i * $vy) + $y.'     ';
//            echo (($i*($vy+$this->nLines)) +$y) % $this->nLines;
//            echo "\n";
//        }
//        exit();
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $points = [];
        foreach ($data as $line) {
            $point = [
                'x' => (int)substr($line[0],2),
                'y' => (int)$line[1],
                'vx' => (int)substr($line[2],2),
                'vy' => (int)$line[3],
            ];
            $points[] = $point;
        }
        $startedAt = microtime(true);
//        $this->dibujaCoordenadas($points);


        $times =10000;
        for ($i=1; $i < $times; $i+=1) {
            $newPoints = [];
            foreach ($points as $point) {
                $newPoint = $this->calculaPosicion($point['x'], $point['y'], $point['vx'], $point['vy'], $i);
                $newPoints[] = $newPoint;
            }
//            $maxRellenasJuntos = $this->calculaMaximoEnLinea($newPoints);
            $this->dibujaCoordenadas($newPoints);
            $answer = $this->io->ask('ITERACION: '.$i);//.' En linea max: '.$maxRellenasJuntos);
//            var_dump($answer);
//            exit();
        }

        $pointsPerSector = $this->sumaPuntosSectorPorSector($newPoints);

//        var_dump($pointsPerSector);

        $result = 1;
        foreach ($pointsPerSector as $points) {
            $result *= $points;
        }

        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function loadData(): array
    {
        if (!isset($this->test)) {
            $filePath = __DIR__ . "/../../Data/2024/day" . self::DAY . ".txr";
            $this->nCols = 101;
            $this->nLines = 103;
        } else {
            $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test".$this->test.".txr";
            $this->nCols = 11;
            $this->nLines = 7;
        }
        $this->io->warning('Loading file: '.$filePath);
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLineAndCol($filePath);
    }

    private const SECTOR_UP_LEFT = 1;
    private const SECTOR_UP_RIGHT = 2;
    private const SECTOR_DOWN_LEFT = 3;
    private const SECTOR_DOWN_RIGHT = 4;
    private function sumaPuntosSectorPorSector($points) : array
    {
        $xCenter = intdiv($this->nCols, 2);
        $yCenter = intdiv($this->nLines, 2);
        $pointsPerSector = [
            self::SECTOR_UP_LEFT => 0,
            self::SECTOR_UP_RIGHT => 0,
            self::SECTOR_DOWN_LEFT => 0,
            self::SECTOR_DOWN_RIGHT => 0,
        ];
        foreach ($points as $point) {
            if (($point['x'] < $xCenter) && ($point['y'] < $yCenter)) {
                $pointsPerSector[self::SECTOR_UP_LEFT] += 1;
            }
            if (($point['x'] > $xCenter) && ($point['y'] < $yCenter)) {
                $pointsPerSector[self::SECTOR_UP_RIGHT] += 1;
            }
            if (($point['x'] < $xCenter) && ($point['y'] > $yCenter)) {
                $pointsPerSector[self::SECTOR_DOWN_LEFT] += 1;
            }
            if (($point['x'] > $xCenter) && ($point['y'] > $yCenter)) {
                $pointsPerSector[self::SECTOR_DOWN_RIGHT] += 1;
            }
        }
        return $pointsPerSector;
    }

//    private function calculaMaximoEnLinea(array $points)
//    {
//        $pointsOrdered = [];
//        foreach ($points as $point) {
//            $pointsOrdered[$point['x']] = $point['y'];
//        }
//
//        foreach ($pointsOrdered as $line => $linePoints) {
//            foreach ($linePoints as $linePoint) {
//
//            }
//        }
//
//    }


}
