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
    name: 'txurdi:2024:day03',
    description: 'Day 3 of the Advent code of 2024',
)]
class Day03Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="03";

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

        $this->io->warning('Day '.self::DAY.'..... GOOOOOOOO');

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

    private const FINDING_START = "Finding m";
    private const FINDING_MUL = "Finding mul(";
    private const FINDING_N1 = "Finding number 1";
    private const FINDING_N2 = "Finding number 2";
    private function executeHalf1(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $sum = 0;
        $status = self::FINDING_START;
        $number1 = $number2 = $previousCharacter = '';
        $leido = '';
        $iteracion = array();
        foreach (str_split($data) as $pos=>$character) {
/*****
            $leido .= $character;
            $iteracion[] = [
                $character,
                $status,
                $number1,
                $number2,
                $sum,
                $leido,
            ];
/*****/
            if ($status == self::FINDING_START) {
                $number1 = $number2 = $leido ='';
                if ($character === 'm') {
                    $status = self::FINDING_MUL;
                }
            } else {
                switch ($character) {
                    case 'u':
                        if ($previousCharacter != 'm') $status = self::FINDING_START;
                        break;
                    case 'l':
                        if ($previousCharacter != 'u') $status = self::FINDING_START;
                        break;
                    case '(':
                        $status = self::FINDING_N1;
                        if ($previousCharacter != 'l') $status = self::FINDING_START;
                        break;
                    case ',':
                        $status = self::FINDING_N2;
                        if (!is_numeric($previousCharacter)) $status = self::FINDING_START;
                        break;
                    case ')':
                        if ($status == self::FINDING_N2) {
                            $sum += (int)$number1 * (int)$number2;
                            $this->io->text($number1.'-'.$number2.'-'.$sum);
                        }
                        $status = self::FINDING_START;
                        break;
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                    case '5':
                    case '6':
                    case '7':
                    case '8':
                    case '9':
                    case '0':
                        if ($status == self::FINDING_N1) {
                            $number1 .= $character;
                            if (strlen($number1) > 3) $status = self::FINDING_START;
                        } else if ($status == self::FINDING_N2) {
                            $number2 .= $character;
                            if (strlen($number2) > 3) $status = self::FINDING_START;
                        } else {
                            $status = self::FINDING_START;
                        }
                        break;
                    default:
                        $status = self::FINDING_START;
                }
            }
            $previousCharacter = $character;
        }
        //$this->io->table([], $iteracion);
        $this->io->success('RESULT: '.$sum);


        return 1;
    }

    private const NOT_FINDING = "DON'T";

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $sum = 0;
        $status = self::FINDING_START;
        $number1 = $number2 = $previousCharacter = '';
//        $leido = '';
//        $iteracion = array();
        foreach (str_split($data) as $pos=>$character) {
            /*****
            $leido .= $character;
            $iteracion[] = [
                $pos,
            $character,
            $status,
            $number1,
            $number2,
            $sum,
            $leido,
            ];
            /*****/
            if ($status == self::NOT_FINDING) {
                if ($character != ')') {
                    continue;
                }
                if (
                    ($data[$pos-1] != '(') ||
                    ($data[$pos-2] != 'o') ||
                    ($data[$pos-3] != 'd')
                ) {
                    continue;
                }
                $status = self::FINDING_START;
            }
            if ($character == ')') {
                if (
                    ($data[$pos-1] == '(') &&
                    ($data[$pos-2] == 't') &&
                    ($data[$pos-3] == '\'') &&
                    ($data[$pos-4] == 'n') &&
                    ($data[$pos-5] == 'o') &&
                    ($data[$pos-6] == 'd')
                ) {
                    $status = self::NOT_FINDING;
                    continue;
                }
            }
            if ($status == self::FINDING_START) {
                $number1 = $number2 = $leido ='';
                if ($character === 'm') {
                    $status = self::FINDING_MUL;
                }
            } else {
                switch ($character) {
                    case 'u':
                        if ($previousCharacter != 'm') $status = self::FINDING_START;
                        break;
                    case 'l':
                        if ($previousCharacter != 'u') $status = self::FINDING_START;
                        break;
                    case '(':
                        $status = self::FINDING_N1;
                        if ($previousCharacter != 'l') $status = self::FINDING_START;
                        break;
                    case ',':
                        $status = self::FINDING_N2;
                        if (!is_numeric($previousCharacter)) $status = self::FINDING_START;
                        break;
                    case ')':
                        if ($status == self::FINDING_N2) {
                            $sum += (int)$number1 * (int)$number2;
                            $this->io->text($number1.'-'.$number2.'-'.$sum);
                        }
                        $status = self::FINDING_START;
                        break;
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                    case '5':
                    case '6':
                    case '7':
                    case '8':
                    case '9':
                    case '0':
                        if ($status == self::FINDING_N1) {
                            $number1 .= $character;
                            if (strlen($number1) > 3) $status = self::FINDING_START;
                        } else if ($status == self::FINDING_N2) {
                            $number2 .= $character;
                            if (strlen($number2) > 3) $status = self::FINDING_START;
                        } else {
                            $status = self::FINDING_START;
                        }
                        break;
                    default:
                        $status = self::FINDING_START;
                }
            }
            $previousCharacter = $character;
        }
//        $this->io->table([], $iteracion);
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

}
