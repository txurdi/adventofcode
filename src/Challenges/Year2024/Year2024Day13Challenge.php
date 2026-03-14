<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day13Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_COLS;

    protected function executePart1(): void
    {
        $data = $this->data;
        $conditions = [];
        $condition = [];
        foreach ($data as $line) {
            if ($line[0] === 'Button') {
                $condition['x'][] = substr($line[2], 2);
                $condition['y'][] = substr($line[3], 2);
            }
            if ($line[0] === 'Prize:') {
                $condition['prizeX'] = substr($line[1], 2);
                $condition['prizeY'] = substr($line[2], 2);
            }
            if ($line[0] === '') {
                $conditions[] = $condition;
                $condition = [];
            }
        }
        $conditions[] = $condition;

        $sumaTokens = 0;
        foreach ($conditions as $condition) {
            $sumaTokens += $this->precioTokensFormula($condition);
        }

        $this->result = (string)$sumaTokens;
    }

    protected function executePart2(): void
    {
        $data = $this->data;
        $conditions = [];
        $condition = [];
        foreach ($data as $line) {
            if ($line[0] === 'Button') {
                $condition['x'][] = substr($line[2], 2);
                $condition['y'][] = substr($line[3], 2);
            }
            if ($line[0] === 'Prize:') {
                $condition['prizeX'] = (int)substr($line[1], 2) + 10000000000000;
                $condition['prizeY'] = (int)substr($line[2], 2) + 10000000000000;
            }
            if ($line[0] === '') {
                $conditions[] = $condition;
                $condition = [];
            }
        }
        $conditions[] = $condition;

        $sumaTokens = 0;
        foreach ($conditions as $condition) {
            $sumaTokens += $this->precioTokensFormula($condition);
        }

        $this->result = (string)$sumaTokens;
    }

    private function precioTokens(mixed $condition): int
    {
        $combinacionesValidas = [];
        $combinacionValida = [];
        $combinacionesValidasEnX = $this->dameCombinaciones($condition['prizeX'], $condition['x']);
        foreach ($combinacionesValidasEnX as $combinacion) {
            $combiY = $combinacion[0] * $condition['y'][0] + $combinacion[1] * $condition['y'][1];
            if ($combiY == $condition['prizeY']) {
                $combinacionesValidas[] = $combinacion;
            }
        }
        $tokens = 0;
        foreach ($combinacionesValidas as $combinacion) {
            $tokensTmp = $combinacion[0] * 3 + $combinacion[1];
            if (($tokens == 0) || ($tokensTmp < $tokens)) {
                $tokens = $tokensTmp;
                $combinacionValida = $combinacion;
            }
        }
        if ($tokens != 0) {
            echo "VALIDA: $combinacion[0] , $combinacion[1]\n";
        }
        return $tokens;
    }

    private function dameCombinaciones(int $prize, array $valores): array
    {
        $posiblePrize = 0;
        $combinacionesValidas = [];
        $maxI = (int)($prize / $valores[0]) + 1;
        for ($i = 0; $i < $maxI; $i++) {
            $restante = $prize - ($valores[0] * $i);
            $maxJ = (int)($restante / $valores[1]);
            $posiblePrize = $valores[0] * $i + $valores[1] * $maxJ;
            if ($posiblePrize == $prize) {
                $combinacionesValidas[] = [$i, $maxJ];
            }
        }
        return $combinacionesValidas;
    }

    private function precioTokensFormula(array $condition): int
    {
        $ax = $condition['x'][0];
        $bx = $condition['x'][1];
        $ay = $condition['y'][0];
        $by = $condition['y'][1];
        $prizeX = $condition['prizeX'];
        $prizeY = $condition['prizeY'];
        $b = (($prizeY * $ax) - ($ay * $prizeX)) / (($by * $ax) - ($bx * $ay));
        if ($this->esEntero($b)) {
            $a = (($prizeX - $bx * $b) / $ax);
            if ($a = $this->esEntero($a)) {
                $tokens = 3 * $a + $b;
                echo "VALIDA: $a , $b\n";
                return $tokens;
            }
        }
        return 0;
    }

    private function esEntero(float|int &$numero): int
    {
        if (is_int($numero)) {
            return (int)$numero;
        }
        $res = explode(".", strval($numero));
        if (!isset($res[1])) {
            return (int)$res[0];
        }
        if (
            (substr($res[1], 0, 2) == "99")
            || (substr($res[1], 0, 3) == "989")
        ) {
            echo "------------------------------------------------------CASIIIIII9     $numero\n";
            return (int)$res[0] + 1;
        }
        if (
            (substr($res[1], 0, 2) == "00")
            || (substr($res[1], 0, 3) == "010")
        ) {
            echo "------------------------------------------------------CASIIIIII0     $numero\n";
            return (int)$res[0];
        }
        echo "------------------------------------------------------NO     $numero\n";
        return 0;
    }
}
