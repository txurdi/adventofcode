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
    name: 'txurdi:2024:day06',
    description: 'Day 6 of the Advent code of 2024',
)]
class Day06Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="06";

    public function __construct()
    {
        parent::__construct();
    }

    private array $bloqueosPosibles=[];

    /**
     * @param array $position
     * @param array $map
     * @param int $result
     * @return int
     */
    public function recorreMapa(array $position, array $map, string $direction, $calculaBloqueosPosibles=false): ?array
    {
        $nMovimientos = 0;
        $rotated = false;
        while (!$this->isOutOfMap($position, $map)) {
            $newPosition = $this->goForward($position, $direction);
            if ($this->isOutOfMap($newPosition, $map)) return $map;
//            var_dump($newPosition);
            // Si llegamos a "+" y seguido hay un bloque, es que hemos entrado en bucle
            if ($map[$newPosition[0]][$newPosition[1]] == self::GUARD_ALL) {
                $new2Position = $this->goForward($newPosition, $direction);
                if ($this->isBlock($new2Position, $map)) {
                    return null;
                }
            }
            if ($calculaBloqueosPosibles && ($this->isBlank($newPosition, $map))) {
                // Calcular si habiendo un bloque aquí crearíamos un bucle.
                $posibleMap = $map;
                $posibleMap[$newPosition[0]][$newPosition[1]] = self::NEW_BLOCK;
                if (null == $this->recorreMapa($position, $posibleMap, $direction, false)) {
                    $this->bloqueosPosibles[] = $newPosition;
                }
            }
            // Si tenemos un bloque giramos
            if ((!$this->isOutOfMap($newPosition, $map))
                && ($this->isBlock($newPosition, $map))) {
                $direction = $this->rotateRight($direction);
                $rotated = true;
            } else {
                // Marcamos la posicion como recorrida:
//                $map[$position[0]][$position[1]] = self::PATROLLED;
                if ($rotated) {
                    $rotated = false;
                    $map[$position[0]][$position[1]] = self::GUARD_ALL;
                } else if (
                    ($direction == self::GUARD_DOWN)
                    || ($direction == self::GUARD_UP)
                ) {
                    $map[$position[0]][$position[1]] = self::GUARD_VERTICAL;
                } else if (
                    ($direction == self::GUARD_LEFT)
                    || ($direction == self::GUARD_RIGHT)
                ) {
                    $map[$position[0]][$position[1]] = self::GUARD_HORIZONTAL;
                }
                // Movemos adelante
                $position = $newPosition;
                $map[$newPosition[0]][$newPosition[1]] = $direction;
            }
            $nMovimientos++;
//            $this->dibujaMapa($map);
//            $this->io->confirm("BLOQUEOS POSIBLES: ".count($this->bloqueosPosibles)." Movimientos: ".$nMovimientos);
        }
        return $map;
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

    private const GUARD_UP = '^';
    private const GUARD_RIGHT = '>';
    private const GUARD_DOWN = 'v';
    private const GUARD_LEFT = '<';
    private const GUARD_HORIZONTAL = '-';
    private const GUARD_VERTICAL = '|';
    private const GUARD_ALL = '+';
    private const BLANK = '.';
    private const BLOCK = '#';
    private const NEW_BLOCK = 'O';
    private const PATROLLED = 'X';
    private function executeHalf1(InputInterface $input, OutputInterface $output): int
    {
        $map = $this->loadData();
        $result = 0;

        foreach ($map as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($this->isGuard($cell)) {
                    $position = [$nr,$nc];
                    break;
                }
            }
        }
        $direction = $map[$position[0]][$position[1]];
        while (!$this->isOutOfMap($position, $map)) {
            $newPosition = $this->goForward($position);
//            var_dump($newPosition);
            if ((!$this->isOutOfMap($newPosition, $map))
                && ($this->isBlock($newPosition, $map))) {
                $this->rotateRight();
            } else {
                $map[$position[0]][$position[1]] = self::PATROLLED;
                $position = $newPosition;
                $map[$newPosition[0]][$newPosition[1]] = $direction;
            }
            $this->dibujaMapa($map);
            $this->io->confirm($result);
        }
        foreach ($map as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($map[$nr][$nc] == self::PATROLLED) {
                    $result++;
                }
            }
        }


        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $map = $this->loadData();

        foreach ($map as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($this->isGuard($cell)) {
                    $position = [$nr,$nc];
                    break;
                }
            }
        }
        $direction = $map[$position[0]][$position[1]];

        $map = $this->recorreMapa($position, $map, $direction, true);

        if (is_null($map)) {
            $this->io->error("BUCLE");
            exit(1);
        }

        $result = 0;
        foreach ($this->bloqueosPosibles as $bloqueosPosible) {
//            var_dump($bloqueosPosible);
            $result++;
        }

        $this->io->success('RESULT: '.$result);

        return 1;
//        $this->map = $this->loadData();
//        $posibleBlocks = [];
//
//        foreach ($this->map as $nr => $row) {
//            foreach ($row as $nc => $cell) {
//                if ($this->isGuard($cell)) {
//                    $position = [$nr,$nc];
//                    $this->direction = $this->map[$position[0]][$position[1]];
//                    $this->map[$position[0]][$position[1]] = self::BLANK;
//                    break;
//                }
//            }
//        }
//        $rotate = false;
//        while (!$this->isOutOfMap($position)) {
//            $newPosiblePosition = $this->goForward($position);
//            if ($this->posibleLoop($newPosiblePosition)) {
//                $posibleBlocks[$newPosiblePosition[0]][$newPosiblePosition[1]] = self::NEW_BLOCK;
//            }
//            //            var_dump($newPosiblePosition);
//            if ((!$this->isOutOfMap($newPosiblePosition))
//                && ($this->isBlock($newPosiblePosition))) {
//                $this->rotateRight();
//                $this->saveRotate($position);
//                $rotate = true;
//            } else {
//                if ($rotate) {
//                    $this->map[$position[0]][$position[1]] = self::GUARD_ALL;
//                } else {
//                    if (!$this->isBlank($position)) {
//                        $this->map[$position[0]][$position[1]] = self::GUARD_ALL;
//                    } else {
//                        switch ($this->direction) {
//                            case self::GUARD_UP:
//                            case self::GUARD_DOWN:
//                                $this->map[$position[0]][$position[1]] = self::GUARD_VERTICAL;
//                                break;
//                            case self::GUARD_LEFT:
//                            case self::GUARD_RIGHT:
//                                $this->map[$position[0]][$position[1]] = self::GUARD_HORIZONTAL;
//                                break;
//                        }
//                    }
//                }
//                $rotate = false;
//                $position = $newPosiblePosition;
//                //$this->map[$newPosiblePosition[0]][$newPosiblePosition[1]] = $this->direction;
//            }
////            $this->dibujaRotate();
////            $this->dibujaMapa();
////            $this->io->confirm($posibleBlocks);
//        }
//
//        $result = 0;
//        foreach ($posibleBlocks as $nr => $row) {
//            foreach ($row as $nc => $cell) {
//                $result++;
//            }
//        }
//        $this->io->success('RESULT: '.$result);
//
//        return 1;
    }

    private function loadData(): array
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLineAndChar($filePath);
    }

    private function isGuard(string $cell):bool
    {
        if (
            ($cell === self::GUARD_UP) ||
            ($cell === self::GUARD_DOWN) ||
            ($cell === self::GUARD_LEFT) ||
            ($cell === self::GUARD_RIGHT)
        ) {
            return true;
        }
        return false;
    }

    private function isOutOfMap(array $position, array $map):bool
    {
//        var_dump($position,count($map),count($map[0]));
        if ( ($position[0] < 0)
            || ($position[1] < 0)
            || ($position[0] >= count($map[0]))
            || ($position[1] >= count($map))
        ) {
            return true;
        }
        return false;
    }

    private function goForward(array $position, string $direction):array
    {
        switch ($direction) {
            case self::GUARD_UP:
                $position[0] = $position[0] - 1;
                break;
            case self::GUARD_DOWN:
                $position[0] = $position[0] + 1;
                break;
            case self::GUARD_LEFT:
                $position[1] = $position[1] - 1;
                break;
            case self::GUARD_RIGHT:
                $position[1] = $position[1] + 1;
                break;
        }
        return $position;
    }

    /*map*/
    private function isBlock(array $position, array $map) :bool
    {
        if (($map[$position[0]][$position[1]] === self::BLOCK)
            || ($map[$position[0]][$position[1]] === self::NEW_BLOCK)) {
            return true;
        }
        return false;
    }
    private function isBlank(array $position, array $map) :bool
    {
        if ($map[$position[0]][$position[1]] === self::BLANK) {
            return true;
        }
        return false;
    }

    private function rotateRight(string $direction) : string
    {
        switch ($direction) {
            case self::GUARD_UP:
                $direction = self::GUARD_RIGHT;
                break;
            case self::GUARD_DOWN:
                $direction = self::GUARD_LEFT;
                break;
            case self::GUARD_LEFT:
                $direction = self::GUARD_UP;
                break;
            case self::GUARD_RIGHT:
                $direction = self::GUARD_DOWN;
                break;
        }
        return $direction;
    }

    private function dibujaMapa(array $map)
    {
        foreach ($map as $nl => $row) {
            foreach ($row as $nr => $cell) {
                echo $cell;
            }
            echo "\n";
        }
    }

    private function dibujaRotate()
    {
        echo "RIGHT: ";
        foreach ($this->rot_right as $coord) {
            echo $coord[0].",".$coord[1]."#";
        }
        echo "\n";
        echo "DOWN: ";
        foreach ($this->rot_down as $coord) {
            echo $coord[0].",".$coord[1]."#";
        }
        echo "\n";
        echo "LEFT: ";
        foreach ($this->rot_left as $coord) {
            echo $coord[0].",".$coord[1]."#";
        }
        echo "\n";
        echo "UP: ";
        foreach ($this->rot_up as $coord) {
            echo $coord[0].",".$coord[1]."#";
        }
        echo "\n";
    }

    private array $rot_right = [];
    private array $rot_left = [];
    private array $rot_up = [];
    private array $rot_down = [];
    private function posibleLoop(array $position, array $map, string $direction) : bool
    {
        if (($this->isOutOfMap($position, $map))
            || ($this->isBlock($position, $map))) {
            return false;
        }
        switch ($direction) {
            case self::GUARD_LEFT:
                $validRotationsR = $this->giveRotations($this->rot_right, 1, $position[1]+1);
                foreach ($validRotationsR as $posR) {
                    $validRotationsD = $this->giveRotations($this->rot_down, 0, $posR[0]);
                    foreach ($validRotationsD as $posD) {
                        $validRotationsL = $this->giveRotations($this->rot_left, 1, $posD[1]);
                        if (count($validRotationsL) > 0) {
                            return true;
                        }
                    }
                }
                break;
            case self::GUARD_UP:
                $validRotationsD = $this->giveRotations($this->rot_down, 0, $position[0]+1);
                foreach ($validRotationsD as $posD) {
                    $validRotationsL = $this->giveRotations($this->rot_left, 1, $posD[1]);
                    foreach ($validRotationsL as $posL) {
                        $validRotationsU = $this->giveRotations($this->rot_up, 0, $posL[0]);
                        if (count($validRotationsU) > 0) {
                            return true;
                        }
                    }
                }
                break;
            case self::GUARD_RIGHT:
                $validRotationsL = $this->giveRotations($this->rot_left, 1, $position[1]-1);
                foreach ($validRotationsL as $posL) {
                    $validRotationsU = $this->giveRotations($this->rot_up, 0, $posL[0]);
                    foreach ($validRotationsU as $posU) {
                        $validRotationsR = $this->giveRotations($this->rot_right, 1, $posU[1]);
                        if (count($validRotationsR) > 0) {
                            return true;
                        }
                    }
                }
                break;
            case self::GUARD_DOWN:
                $validRotationsU = $this->giveRotations($this->rot_up, 0, $position[0]-1);
                foreach ($validRotationsU as $posU) {
                    $validRotationsR = $this->giveRotations($this->rot_right, 1, $posU[1]);
                    foreach ($validRotationsR as $posR) {
                        $validRotationsD = $this->giveRotations($this->rot_down, 0, $posR[0]);
                        if (count($validRotationsD) > 0) {
                            return true;
                        }
                    }
                }
                break;
        }
        return false;
    }

    private function giveRotations(array $rotations, int $colrow, mixed $val)
    {
        $validRotations = [];
        foreach ($rotations as $pos) {
            if ($pos[$colrow] == $val) {
                $validRotations[] = $pos;
            }
        }
        return $validRotations;
    }

    private function saveRotate(array $position, string $direction)
    {
        switch ($direction) {
            case self::GUARD_UP:
                $this->rot_up[] = $position;
                break;
            case self::GUARD_DOWN:
                $this->rot_down[] = $position;
                break;
            case self::GUARD_LEFT:
                $this->rot_left[] = $position;
                break;
            case self::GUARD_RIGHT:
                $this->rot_right[] = $position;
                break;
        }
    }



}
