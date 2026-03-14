<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day6Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_CHARS;

    private array $bloqueosPosibles = [];

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

    private array $rot_right = [];
    private array $rot_left = [];
    private array $rot_up = [];
    private array $rot_down = [];

    public function recorreMapa(array $position, array $map, string $direction, $calculaBloqueosPosibles = false): ?array
    {
        $nMovimientos = 0;
        $rotated = false;
        while (!$this->isOutOfMap($position, $map)) {
            $newPosition = $this->goForward($position, $direction);
            if ($this->isOutOfMap($newPosition, $map)) return $map;
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
        }
        return $map;
    }

    protected function executePart1(): void
    {
        $map = $this->data;
        $result = 0;

        foreach ($map as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($this->isGuard($cell)) {
                    $position = [$nr, $nc];
                    break;
                }
            }
        }
        $direction = $map[$position[0]][$position[1]];
        while (!$this->isOutOfMap($position, $map)) {
            $newPosition = $this->goForward($position);
            if ((!$this->isOutOfMap($newPosition, $map))
                && ($this->isBlock($newPosition, $map))) {
                $this->rotateRight();
            } else {
                $map[$position[0]][$position[1]] = self::PATROLLED;
                $position = $newPosition;
                $map[$newPosition[0]][$newPosition[1]] = $direction;
            }
            $this->dibujaMapa($map);
        }
        foreach ($map as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($map[$nr][$nc] == self::PATROLLED) {
                    $result++;
                }
            }
        }

        $this->result = (string)$result;
    }

    protected function executePart2(): void
    {
        $map = $this->data;
        $this->bloqueosPosibles = [];

        foreach ($map as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($this->isGuard($cell)) {
                    $position = [$nr, $nc];
                    break;
                }
            }
        }
        $direction = $map[$position[0]][$position[1]];

        $map = $this->recorreMapa($position, $map, $direction, true);

        if (is_null($map)) {
            echo "BUCLE\n";
            exit(1);
        }

        $result = 0;
        foreach ($this->bloqueosPosibles as $bloqueosPosible) {
            $result++;
        }

        $this->result = (string)$result;
    }

    private function isGuard(string $cell): bool
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

    private function isOutOfMap(array $position, array $map): bool
    {
        if (
            ($position[0] < 0)
            || ($position[1] < 0)
            || ($position[0] >= count($map[0]))
            || ($position[1] >= count($map))
        ) {
            return true;
        }
        return false;
    }

    private function goForward(array $position, string $direction = self::GUARD_UP): array
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

    private function isBlock(array $position, array $map): bool
    {
        if (
            ($map[$position[0]][$position[1]] === self::BLOCK)
            || ($map[$position[0]][$position[1]] === self::NEW_BLOCK)
        ) {
            return true;
        }
        return false;
    }

    private function isBlank(array $position, array $map): bool
    {
        if ($map[$position[0]][$position[1]] === self::BLANK) {
            return true;
        }
        return false;
    }

    private function rotateRight(string $direction = self::GUARD_UP): string
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

    private function dibujaMapa(array $map): void
    {
        foreach ($map as $nl => $row) {
            foreach ($row as $nr => $cell) {
                echo $cell;
            }
            echo "\n";
        }
    }

    private function dibujaRotate(): void
    {
        echo "RIGHT: ";
        foreach ($this->rot_right as $coord) {
            echo $coord[0] . "," . $coord[1] . "#";
        }
        echo "\n";
        echo "DOWN: ";
        foreach ($this->rot_down as $coord) {
            echo $coord[0] . "," . $coord[1] . "#";
        }
        echo "\n";
        echo "LEFT: ";
        foreach ($this->rot_left as $coord) {
            echo $coord[0] . "," . $coord[1] . "#";
        }
        echo "\n";
        echo "UP: ";
        foreach ($this->rot_up as $coord) {
            echo $coord[0] . "," . $coord[1] . "#";
        }
        echo "\n";
    }

    private function posibleLoop(array $position, array $map, string $direction): bool
    {
        if (($this->isOutOfMap($position, $map))
            || ($this->isBlock($position, $map))) {
            return false;
        }
        switch ($direction) {
            case self::GUARD_LEFT:
                $validRotationsR = $this->giveRotations($this->rot_right, 1, $position[1] + 1);
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
                $validRotationsD = $this->giveRotations($this->rot_down, 0, $position[0] + 1);
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
                $validRotationsL = $this->giveRotations($this->rot_left, 1, $position[1] - 1);
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
                $validRotationsU = $this->giveRotations($this->rot_up, 0, $position[0] - 1);
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

    private function giveRotations(array $rotations, int $colrow, mixed $val): array
    {
        $validRotations = [];
        foreach ($rotations as $pos) {
            if ($pos[$colrow] == $val) {
                $validRotations[] = $pos;
            }
        }
        return $validRotations;
    }

    private function saveRotate(array $position, string $direction): void
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
