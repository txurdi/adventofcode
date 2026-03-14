<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day12Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_CHARS;

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

    protected function executePart1(): void
    {
        $map = $this->data;
        $startedAt = microtime(true);

        $mapeado = $map;
        $sections = [];
        foreach ($map as $nl => $line) {
            foreach ($line as $nr => $letter) {
                if ($mapeado[$nl][$nr] == '.') {
                    continue;
                }
                $letterNumber = 0;
                if (isset($sections[$letter])) {
                    $letterNumber = count($sections[$letter]);
                }
                $contiguas = $this->dameContiguasQueCoincidan($nl, $nr, $letter, $mapeado);
                foreach ($contiguas as $nl2 => $line2) {
                    foreach ($line2 as $nr2 => $letter2) {
                        $sections[$letter][$letterNumber][$nl2][$nr2] = $letter;
                    }
                }
            }
        }

        $price = 0;
        echo "CALCULOS:\n";
        foreach ($sections as $letter => $sectionZones) {
            foreach ($sectionZones as $sectionName => $points) {
                $area = $this->calculateArea($points);
                $perimeter = $this->calculatePerimeter($points);
                $sectionPrice = $area * $perimeter;
                echo "$letter$sectionName -> ($area)x($perimeter)=$sectionPrice\n";
                $price += $sectionPrice;
            }
        }

        $this->result = (string)$price;
    }

    protected function executePart2(): void
    {
        $map = $this->data;
        $startedAt = microtime(true);

        $mapeado = $map;
        $sections = [];
        foreach ($map as $nl => $line) {
            foreach ($line as $nr => $letter) {
                if ($mapeado[$nl][$nr] == '.') {
                    continue;
                }
                $letterNumber = 0;
                if (isset($sections[$letter])) {
                    $letterNumber = count($sections[$letter]);
                }
                $contiguas = $this->dameContiguasQueCoincidan($nl, $nr, $letter, $mapeado);
                foreach ($contiguas as $nl2 => $line2) {
                    foreach ($line2 as $nr2 => $letter2) {
                        $sections[$letter][$letterNumber][$nl2][$nr2] = $letter;
                    }
                }
            }
        }
        $this->dibujaPuntosLetras($sections);
        $price = 0;
        echo "CALCULOS:\n";
        foreach ($sections as $letter => $sectionZones) {
            foreach ($sectionZones as $sectionName => $points) {
                $area = $this->calculateArea($points);
                $perimeter = $this->calculatePerimeterEspecial($points);
                $sectionPrice = $area * $perimeter;
                echo "$letter$sectionName -> ($area)x($perimeter)=$sectionPrice\n";
                $price += $sectionPrice;
            }
        }

        $this->result = (string)$price;
    }

    private function searchContigous(int $line, int $row, array $sections): int
    {
        foreach ($sections as $sectionNumber => $points) {
            if (isset($points[$line + 1][$row])) {
                return $sectionNumber;
            }
            if (isset($points[$line - 1][$row])) {
                return $sectionNumber;
            }
            if (isset($points[$line][$row + 1])) {
                return $sectionNumber;
            }
            if (isset($points[$line][$row - 1])) {
                return $sectionNumber;
            }
        }
        return count($sections);
    }

    private function calculateArea(array $points): int
    {
        $sum = 0;
        foreach ($points as $nl => $lines) {
            $sum += count($lines);
        }
        return $sum;
    }

    private function calculatePerimeter(array $points): int
    {
        $perimeter = 0;
        foreach ($points as $nl => $lines) {
            foreach ($lines as $nr => $cell) {
                $line = $nl;
                $row = $nr;
                if (!isset($points[$line - 1][$row])) {
                    $perimeter += 1;
                }
                if (!isset($points[$line + 1][$row])) {
                    $perimeter += 1;
                }
                if (!isset($points[$line][$row - 1])) {
                    $perimeter += 1;
                }
                if (!isset($points[$line][$row + 1])) {
                    $perimeter += 1;
                }
            }
        }
        return $perimeter;
    }

    private function dameContiguasQueCoincidan(int|string $nl, int|string $nr, mixed $letter, array &$map): array
    {
        $contiguas[$nl][$nr] = $letter;
        if ((isset($map[$nl + 1][$nr])) && ($map[$nl + 1][$nr] == $letter)) {
            $map[$nl + 1][$nr] = '.';
            $masContiguas = $this->dameContiguasQueCoincidan($nl + 1, $nr, $letter, $map);
            $contiguas = $this->mergear($contiguas, $masContiguas);
        }
        if ((isset($map[$nl - 1][$nr])) && ($map[$nl - 1][$nr] == $letter)) {
            $map[$nl - 1][$nr] = '.';
            $masContiguas = $this->dameContiguasQueCoincidan($nl - 1, $nr, $letter, $map);
            $contiguas = $this->mergear($contiguas, $masContiguas);
        }
        if ((isset($map[$nl][$nr + 1])) && ($map[$nl][$nr + 1] == $letter)) {
            $map[$nl][$nr + 1] = '.';
            $masContiguas = $this->dameContiguasQueCoincidan($nl, $nr + 1, $letter, $map);
            $contiguas = $this->mergear($contiguas, $masContiguas);
        }
        if ((isset($map[$nl][$nr - 1])) && ($map[$nl][$nr - 1] == $letter)) {
            $map[$nl][$nr - 1] = '.';
            $masContiguas = $this->dameContiguasQueCoincidan($nl, $nr - 1, $letter, $map);
            $contiguas = $this->mergear($contiguas, $masContiguas);
        }
        return $contiguas;
    }

    private function mergear(array $contiguas, array $masContiguas): array
    {
        foreach ($masContiguas as $nl => $lines) {
            foreach ($lines as $nr => $mcl) {
                $contiguas[$nl][$nr] = $contiguas;
            }
        }
        return $contiguas;
    }

    private function calculatePerimeterEspecial(mixed $points): int
    {
        $perimeter = 0;
        $perimetral = [];
        foreach ($points as $nl => $lines) {
            foreach ($lines as $nr => $cell) {
                $line = $nl;
                $row = $nr;
                if (!isset($points[$line - 1][$row])) {
                    $perimetral['^'][$line][$row] = 1;
                    $perimeter += 1;
                }
                if (!isset($points[$line + 1][$row])) {
                    $perimetral['v'][$line][$row] = 1;
                    $perimeter += 1;
                }
                if (!isset($points[$line][$row - 1])) {
                    $perimetral['<'][$row][$line] = 1;
                    $perimeter += 1;
                }
                if (!isset($points[$line][$row + 1])) {
                    $perimetral['>'][$row][$line] = 1;
                    $perimeter += 1;
                }
            }
        }
        return $this->contarLadosCompletos($perimetral);
    }

    private function contarLadosCompletos(array $perimetral): int
    {
        var_dump($perimetral);
        $perimeter = 0;
        foreach ($perimetral as $tipo => $puntos) {
            ksort($puntos);
            $cont[$tipo] = 0;
            foreach ($puntos as $nl => $lines) {
                ksort($lines);
                $first[$tipo] = true;
                foreach ($lines as $nr => $cell) {
                    if ($tipo == '>') {
                        echo "$tipo-$nl$nr\n";
                        var_dump((isset($ladoanterior[$tipo]) ? $ladoanterior[$tipo] : '-'), $cont[$tipo]);
                    }
                    if ($first[$tipo]) {
                        $first[$tipo] = false;
                        $inicio[$tipo] = true;
                        $cont[$tipo]++;
                        $ladoanterior[$tipo] = $nr - 1;
                    }
                    if ($ladoanterior[$tipo] != $nr - 1) {
                        $cont[$tipo]++;
                    }
                    $ladoanterior[$tipo] = $nr;
                }
            }
            $perimeter += $cont[$tipo];
        }
        var_dump($cont);
        return $perimeter;
    }
}
