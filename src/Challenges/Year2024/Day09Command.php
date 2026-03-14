<?php

namespace App\Command\y2024;

use App\Util\SolveHelper;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'txurdi:2024:day09',
    description: 'Day 09 of the Advent code of 2024',
)]
class Day09Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="09";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $posiciones
     * @return void
     */
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
//            echo "$posicion[num]$posicion[hueco]\n";
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
        $diskMap = str_split(trim($data));

        $block = [];
        $free = false;
        $print = 0;
        foreach ($diskMap as $pos => $value) {
            for ($i = 1; $i <= $value; $i++) {
                $block[] = ($free)? '.' : "$print";
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
                while  ($block[count($block) - 1] == '.') {
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

        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $diskMap = str_split(trim($data));

        $posiciones = [];
        for ($i = 0; $i <= count($diskMap)/2; $i++) {
            $posiciones['n_'.$i] = [
                'num' => $i,
                'cantidad' => $diskMap[$i*2],
                'hueco' => (isset($diskMap[($i*2)+1]))? $diskMap[($i*2)+1] : 0,
            ];
        }
//        $this->dibujaCantidadHueco($posiciones);
//        var_dump($posiciones);

        $nuevasPosiciones = $posiciones;
        $posicionesReverse = array_reverse($posiciones);
        foreach ($posicionesReverse as $pos => $posicion) {
            $nuevasPosiciones = $this->reduce($pos, $nuevasPosiciones);
//            $this->io->text($pos);
//            var_dump($nuevasPosiciones);
//            $this->dibujaCantidadHueco($nuevasPosiciones);
        }

//        var_dump($nuevasPosiciones);
        $block = $this->dibujaCantidadHueco($nuevasPosiciones);

        $result = 0;
        foreach ($block as $pos => $value) {
            $result += ($pos * $value);
        }

        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function reduce(string $num, array $posiciones): array
    {
        $mover = $posiciones[$num];
//        var_dump($mover);
        $pos = 1;
        foreach ($posiciones as $posPosible=>$posiblePosicion) {
//            var_dump($posPosible);
            if ($posiciones[$num]['num'] == $posiblePosicion['num']) {
                return $posiciones;
            }
            if ($posiblePosicion['hueco'] >= $mover['cantidad']) {
//                var_dump("COMABIO a!!!!!");
//                var_dump($posiblePosicion);
                // creamos una nueva posicion
                $nuevo = ['mov_'.$mover['num'] => [
                    'cantidad' => $mover['cantidad'],
                    'num' => $mover['num'],
                    'hueco' => $posiblePosicion['hueco'] - $mover['cantidad'],
                ]];
                // vaciar el que muevo
                $posiciones[$num]['hueco'] = $mover['cantidad']+$mover['hueco'];
                $posiciones[$num]['cantidad'] = 0;
                // quitamos hueco de la nueva posicion
                $posiciones[$posPosible]['hueco'] = 0;
//                var_dump("NUEVO $num!!!!!");
//                var_dump($posiciones[$num]);
//                var_dump("NUEVO $posPosible!!!!!");
//                var_dump($posiciones[$posPosible]);
//                var_dump($nuevo);
                // añadimos otra posicion nueva seguido
                array_splice($posiciones, $pos, 0, $nuevo);
                return $posiciones;
            }
            $pos++;
        }
//        var_dump($posiciones);
//        exit();
        return $posiciones;
    }

    private function compact(array $posiciones): array
    {
        $this->dibujaCantidadValor($posiciones);
        if (count($posiciones) <= 2) return $posiciones;
        $ultima = array_pop($posiciones);

        /// me duermo....


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

    private function loadData(): string
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToString($filePath);
    }

    private function dibujaDetailed(array $detailed)
    {
        foreach ($detailed as $pos => $value) {
            echo '('.$value['tam'].')'.$value['value']."-";
        }
        echo "\n";
    }

    private function dibujaBloques(array $block)
    {
        foreach ($block as $pos => $value) {
            echo '('.$pos.')'.$value."-";
        }
        echo "\n";
    }

    private function meterLoAntesPosible(int $selectedVal, int $selectedTam, array $detailed) :array
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
                if ( ($combi['value'] == '.')
                && ($combi['tam'] >= $selectedTam)) {
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

    private function intentarCompactarSinFragmentar(array $detailed, int $positionToMove)
    {
        $objectToMove = $this->getObjetWithPos($detailed, $positionToMove);
        $tam = $objectToMove['tam'];
        foreach ($detailed as $value) {
            $pos = $value['pos'];
            if ($pos=='-') continue;
            if ($pos >= $positionToMove) {
                break;
            }
            if ($this->change($detailed, $pos, $positionToMove)) {
                break;
            }
        }
        return $detailed;
    }

    private function change(array &$detailed, int $posWhere, int $posWhat) :array|bool
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

    private function getObjetWithPos(array $detailed, int $position)
    {
        foreach ($detailed as $value) {
            if ($value['pos'] == $position) {
                return $value;
            }
        }
        return null;
    }

    private function dibujaSeguido(array $sequence)
    {
        foreach ($sequence as $value) {
            echo $value;
        }
        echo "\n";
    }

    private function meterEnHueco(array $posiciones, int $pos, array $ultima)
    {
//        $posicion = $posiciones[$pos];
//        if ($posicion['hueco'] == $ultima['hueco']) {
//            $posiciones[$pos] = $ultima;
//        } else ($posicion['hueco'] == $ultima['hueco']) {
//            $posicion['cantidad'] = $posicion['cantidad']-$ultima['cantidad'];
//        } else return false;
    }

}
