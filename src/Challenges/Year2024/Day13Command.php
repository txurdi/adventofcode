<?php

namespace App\Command\y2024;

use App\Util\SolveHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function PHPUnit\Framework\isEmpty;

#[AsCommand(
    name: 'txurdi:2024:day13',
    description: 'Day 13 of the Advent code of 2024',
)]
class Day13Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="13";

    public function __construct()
    {
        parent::__construct();
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
        $conditions = [];
        $condition = [];
        foreach ($data as $line) {
            if ($line[0] === 'Button') {
                $condition['x'][] = substr($line[2],2);
                $condition['y'][] = substr($line[3],2);
            }
            if ($line[0] === 'Prize:') {
                $condition['prizeX'] = substr($line[1],2);
                $condition['prizeY'] = substr($line[2],2);
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
//            $sumaTokens += $this->precioTokens($condition);
        }

        $this->io->success('RESULT: '.$sumaTokens);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        // PRuebas falladas:
//        108528956728654
//        108703305904839

        $data = $this->loadData();
        $conditions = [];
        $condition = [];
        foreach ($data as $line) {
            if ($line[0] === 'Button') {
                $condition['x'][] = substr($line[2],2);
                $condition['y'][] = substr($line[3],2);
            }
            if ($line[0] === 'Prize:') {
                $condition['prizeX'] = (int)substr($line[1],2) + 10000000000000;
                $condition['prizeY'] = (int)substr($line[2],2) + 10000000000000;
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

        $this->io->success('RESULT: '.$sumaTokens);

        return 1;
    }

    private function loadData(): array
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLineAndCol($filePath);
    }

    private function precioTokens(mixed $condition) : int
    {
        $combinacionesValidas = [];
        $combinacionValida = [];
        $combinacionesValidasEnX = $this->dameCombinaciones($condition['prizeX'], $condition['x']);
//        var_dump($combinacionesValidas);
        foreach ($combinacionesValidasEnX as $combinacion) {
            $combiY = $combinacion[0]*$condition['y'][0] + $combinacion[1]*$condition['y'][1];
            if ($combiY == $condition['prizeY']) {
                $combinacionesValidas[]  = $combinacion;
            }
        }
        $tokens = 0;
        foreach ($combinacionesValidas as $combinacion) {
            $tokensTmp = $combinacion[0]*3 + $combinacion[1];
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

    private function dameCombinaciones(int $prize, array $valores) :array
    {
//        var_dump($prize,$valores);
        $posiblePrize = 0;
        $combinacionesValidas = [];
        $maxI = (int)($prize/$valores[0])+1;
        for ($i=0; $i<$maxI; $i++) {
            $restante = $prize - ($valores[0]*$i);
//            echo "$i ## PRIZE: $prize - ($valores[0]*$i) - $restante\n";
            $maxJ = (int)($restante/$valores[1]);
            $posiblePrize = $valores[0]*$i + $valores[1]*$maxJ;
//            echo "    $maxJ ## $valores[0] ($i) + $valores[1] ($maxJ) = $posiblePrize\n";
            if ($posiblePrize == $prize) {
//                echo "===================== $i  $maxJ ## $valores[0] ($i) + $valores[1] ($maxJ) = $prize\n";
                $combinacionesValidas[] = [$i, $maxJ];
            }
        }
        return $combinacionesValidas;
    }

    private function precioTokensFormula(array $condition) : int
    {
        $ax = $condition['x'][0];
        $bx = $condition['x'][1];
        $ay = $condition['y'][0];
        $by = $condition['y'][1];
        $prizeX = $condition['prizeX'];
        $prizeY = $condition['prizeY'];
//        $b = ($prizeY - (($ay*$prizeX) / $ax)) / ($by - (($bx*$ay) / $ax));
        $b = (($prizeY*$ax) - ($ay*$prizeX)) / (($by*$ax) - ($bx*$ay));
        if ($this->esEntero($b)) {
            $a = (($prizeX - $bx*$b) / $ax);
            if ($a = $this->esEntero($a)) {
//                var_dump($a,$b);
                $tokens = 3*$a + $b;
                echo "VALIDA: $a , $b\n";
                return $tokens;
            }
        }
        return 0;
    }

    private function esEntero(float|int &$numero) : int
    {
        if (is_int($numero)) {
            return (int)$numero;
        }
//        $dif = abs((float)$numero - (int)$numero);
//        if (
//            ($dif > 0.001)
//            && ($dif < 9.99)
//        ) {
//            return 0;
//        }
//        return (int)round($numero,2);
//        if (round($numero,3) == (int)$numero) {
//            return (int)round($numero,2);
//        }
//        return false;
//        var_dump('---');
//        var_dump($numero,(int)$numero,round($numero,2));
        $res = explode(".", strval($numero));
        if (!isset($res[1])) {
//            var_dump($res);
            return (int)$res[0];
        }
        if (
            (substr($res[1], 0, 2) == "99")
            || (substr($res[1], 0, 3) == "989")
        ) {
            echo "------------------------------------------------------CASIIIIII9     $numero\n";
//            exit();
            return (int)$res[0]+1;
        }
        if (
            (substr($res[1], 0, 2) == "00")
            || (substr($res[1], 0, 3) == "010")
        ) {
            echo "------------------------------------------------------CASIIIIII0     $numero\n";
//            exit();
            return (int)$res[0];
        }
        echo "------------------------------------------------------NO     $numero\n";
//        var_dump($res);
        return 0;
    }

}
