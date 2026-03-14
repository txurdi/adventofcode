<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day9Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_STRING;

    public function dibujaCantidadHueco(array $posiciones): array
    {
        $block = [];
        foreach ($posiciones as $pos => $posicion) {
            for ($i = 1; $i <= $posicion['cantidad']; $i++) {
                echo $posicion['num'];
                $block[] = $posicion['num'];
            }
            for ($i = 1; $i <= $posicion['hueco']; $i++) {
                echo '.';
                $block[] = 0;
            }
        }
        echo "\n";
        return $block;
    }

    public function dibujaCantidadValor(array $posiciones): void
    {
        foreach ($posiciones as $pos => $posicion) {
            for ($i = 1; $i <= $posicion['cantidad']; $i++) {
                echo $posicion['valor'];
            }
        }
    }

    protected function executePart1(): void
    {
        $data = $this->dataStr;
        $diskMap = str_split(trim($data));

        $block = [];
        $free = false;
        $print = 0;
        foreach ($diskMap as $pos => $value) {
            for ($i = 1; $i <= $value; $i++) {
                $block[] = ($free) ? '.' : "$print";
            }
            if ($free) {
                $print++;
            }
            $free = !$free;
        }

        $pos = 0;
        $this->dibujaBloques($block);
        while (isset($block[$pos])) {
            if ($block[$pos] == '.') {
                while ($block[count($block) - 1] == '.') {
                    unset($block[count($block) - 1]);
                }
                $block[$pos] = $block[count($block) - 1];
                unset($block[count($block) - 1]);
            }
            $pos++;
        }

        $result = 0;
        foreach ($block as $pos => $value) {
            $result += ($pos * $value);
        }

        $this->result = (string)$result;
    }

    protected function executePart2(): void
    {
        $data = $this->dataStr;
        $diskMap = str_split(trim($data));

        $posiciones = [];
        for ($i = 0; $i <= count($diskMap) / 2; $i++) {
            $posiciones['n_' . $i] = [
                'num' => $i,
                'cantidad' => $diskMap[$i * 2],
                'hueco' => (isset($diskMap[($i * 2) + 1])) ? $diskMap[($i * 2) + 1] : 0,
            ];
        }

        $nuevasPosiciones = $posiciones;
        $posicionesReverse = array_reverse($posiciones);
        foreach ($posicionesReverse as $pos => $posicion) {
            $nuevasPosiciones = $this->reduce($pos, $nuevasPosiciones);
        }

        $block = $this->dibujaCantidadHueco($nuevasPosiciones);

        $result = 0;
        foreach ($block as $pos => $value) {
            $result += ($pos * $value);
        }

        $this->result = (string)$result;
    }

    private function reduce(string $num, array $posiciones): array
    {
        $mover = $posiciones[$num];
        $pos = 1;
        foreach ($posiciones as $posPosible => $posiblePosicion) {
            if ($posiciones[$num]['num'] == $posiblePosicion['num']) {
                return $posiciones;
            }
            if ($posiblePosicion['hueco'] >= $mover['cantidad']) {
                // creamos una nueva posicion
                $nuevo = ['mov_' . $mover['num'] => [
                    'cantidad' => $mover['cantidad'],
                    'num' => $mover['num'],
                    'hueco' => $posiblePosicion['hueco'] - $mover['cantidad'],
                ]];
                // vaciar el que muevo
                $posiciones[$num]['hueco'] = $mover['cantidad'] + $mover['hueco'];
                $posiciones[$num]['cantidad'] = 0;
                // quitamos hueco de la nueva posicion
                $posiciones[$posPosible]['hueco'] = 0;
                // añadimos otra posicion nueva seguido
                array_splice($posiciones, $pos, 0, $nuevo);
                return $posiciones;
            }
            $pos++;
        }
        return $posiciones;
    }

    private function compact(array $posiciones): array
    {
        $this->dibujaCantidadValor($posiciones);
        if (count($posiciones) <= 2) return $posiciones;
        $ultima = array_pop($posiciones);

        if ($ultima['valor'] != '.') {
            $encontrado = false;
            foreach ($posiciones as $pos => $value) {
                if ($value['cantidad'] >= $ultima['cantidad']) {
                    $posiciones = $this->meterEnHueco($posiciones, $pos, $ultima);
                    $encontrado = true;
                }
            }
        }
        $posiciones = $this->compact($posiciones);
        if (!$encontrado) {
            $posiciones[] = $ultima;
        }
        return $posiciones;
    }

    private function dibujaDetailed(array $detailed): void
    {
        foreach ($detailed as $pos => $value) {
            echo '(' . $value['tam'] . ')' . $value['value'] . "-";
        }
        echo "\n";
    }

    private function dibujaBloques(array $block): void
    {
        foreach ($block as $pos => $value) {
            echo '(' . $pos . ')' . $value . "-";
        }
        echo "\n";
    }

    private function meterLoAntesPosible(int $selectedVal, int $selectedTam, array $detailed): array
    {
        $result = [];
        $metido = false;
        foreach ($detailed as $pos => $combi) {
            if (($combi['value'] == $selectedVal) && ($combi['tam'] == $selectedTam)) {
                if ($metido) {
                    $result[] = [
                        'value' => '.',
                        'tam' => $combi['tam']
                    ];
                } else {
                    $result[] = $combi;
                    $metido = true;
                }
            } else if ($metido) {
                $result[] = $combi;
            } else {
                if (
                    ($combi['value'] == '.') &&
                    ($combi['tam'] >= $selectedTam)
                ) {
                    $result[] = [
                        'value' => $selectedVal,
                        'tam' => $selectedTam
                    ];
                    if ($combi['tam'] > $selectedTam) {
                        $result[] = [
                            'value' => '.',
                            'tam' => $combi['tam'] - $selectedTam
                        ];
                    }
                    $metido = true;
                } else {
                    $result[] = $combi;
                }
            }
        }
        return $result;
    }

    private function intentarCompactarSinFragmentar(array $detailed, int $positionToMove): array
    {
        $objectToMove = $this->getObjetWithPos($detailed, $positionToMove);
        $tam = $objectToMove['tam'];
        foreach ($detailed as $value) {
            $pos = $value['pos'];
            if ($pos == '-') continue;
            if ($pos >= $positionToMove) {
                break;
            }
            if ($this->change($detailed, $pos, $positionToMove)) {
                break;
            }
        }
        return $detailed;
    }

    private function change(array &$detailed, int $posWhere, int $posWhat): array|bool
    {
        if ($detailed[$posWhat]['tam'] == $detailed[$posWhere]['tam']) {
            $tmp = $detailed[$posWhere]['value'];
            $detailed[$posWhere]['value'] = $detailed[$posWhat]['value'];
            $detailed[$posWhat]['value'] = $tmp;
            return true;
        } elseif ($detailed[$posWhat]['tam'] < $detailed[$posWhere]['tam']) {
            $newBlock = [
                [
                    'tam' => $detailed[$posWhat]['tam'],
                    'value' => $detailed[$posWhat]['value']
                ],
                [
                    'tam' => $detailed[$posWhere]['tam'] - $detailed[$posWhat]['tam'],
                    'value' => '.'
                ]
            ];
            array_splice($detailed, $posWhere, 0, $newBlock);
            return true;
        }

        return false;
    }

    private function getObjetWithPos(array $detailed, int $position): mixed
    {
        foreach ($detailed as $value) {
            if ($value['pos'] == $position) {
                return $value;
            }
        }
        return null;
    }

    private function dibujaSeguido(array $sequence): void
    {
        foreach ($sequence as $value) {
            echo $value;
        }
        echo "\n";
    }

    private function meterEnHueco(array $posiciones, int $pos, array $ultima): mixed
    {
        // Not fully implemented in original
    }
}
