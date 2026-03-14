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
    name: 'txurdi:2024:day05',
    description: 'Day 05 of the Advent code of 2024',
)]
class Day05Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="05";
    private array $rules;

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
        $result = 0;

        $updates = [];
        $searchingFor = 'rules';
        foreach ($data as $line) {
            if ($line == "\n") {
                $searchingFor = 'updates';
                continue;
            }
            if ($searchingFor == 'rules') {
                $this->rules[] = explode('|',(trim($line)));
            } else {
                $updates[] = explode(',',(trim($line)));
            }
        }

        foreach ($updates as $update) {
            if ($this->updateIsCorrect($update)) {
                $result += $this->getTheMiddlePageNumber($update);
            }
        }

        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $result = 0;

        $updates = [];
        $searchingFor = 'rules';
        foreach ($data as $line) {
            if ($line == "\n") {
                $searchingFor = 'updates';
                continue;
            }
            if ($searchingFor == 'rules') {
                $this->rules[] = explode('|',(trim($line)));
            } else {
                $updates[] = explode(',',(trim($line)));
            }
        }

        foreach ($updates as $update) {
            $fixed = $this->incorrectUpdateFixed2($update);
            if (is_array($fixed)) {
                var_dump($update,$fixed);
                $result += $this->getTheMiddlePageNumber($fixed);
            }
        }

        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function loadData(): array
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLine($filePath);
    }

    private function updateIsCorrect(array $update) :bool
    {
//        $this->io->title('Update:');
//        var_dump($update);
        foreach ($update as $pos=>$number) {
//            $this->io->warning('Number: '.$number.'# In position: '.$pos);
            $rules = $this->rulesToApply($number);
//            var_dump($rules);
            foreach ($rules['before'] as $before) {
                $found = array_search($before, $update);
//                $this->io->text("Result for before('.$before.'): ".$found);
                if ($found !== false) {
                    if ($pos < $found) {
//                        $this->io->warning('Before: ' . $before);
//                        var_dump($update);
                        return false;
                    }
                }
            }
            foreach ($rules['after'] as $after) {
                $found = array_search($after, $update);
//                var_dump($found);
                if ($found !== false) {
                    if ($pos > $found) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function incorrectUpdateFixed2(array $update) :array|bool
    {
        $changed = false;
        $pos = 0;
        while ($pos < count($update)) {
            $number = $update[$pos];
            $numbersMustBeBefore = $this->numbersMustBeBefore($number);
            $lastPosition = $pos;
            foreach ($numbersMustBeBefore as $before) {
                $newPosition = array_search($before, $update);
                if ($newPosition !== false) {
                    if ($lastPosition < $newPosition) {
                        $lastPosition = $newPosition;
                    }
                }
            }
            if ($lastPosition !== $pos) {
                $update[$pos] = $update[$lastPosition];
                $update[$lastPosition] = $number;
                $changed = true;
            } else {
                $pos++;
            }
        }
        if ($changed) {
            return $update;
        }
        return false;
    }

    private function incorrectUpdateFixed(array $update) :array|bool
    {
//        $this->io->title('Update:');
//        var_dump($update);
        $changed = false;
        $fixing = $update;
        foreach ($fixing as $pos=> $number) {
//            $this->io->warning('Number: '.$number.'# In position: '.$fixingPos);
            $rules = $this->rulesToApply($number);
//            var_dump($rules);
            $fixingPos = $pos;
            foreach ($rules['before'] as $before) {
                $found = array_search($before, $update);
                $this->io->text("Result for [".$fixingPos."]".$number." before(".$before."): ".$found);
                if ($found !== false) {
                    if ($fixingPos < $found) {
                        $this->io->text("BEFORE: Changing [".$fixingPos."]".$number." with [".$found."]".$before);
//                        $this->io->warning('Before: ' . $before);
//                        var_dump($update);
                        $fixing[$fixingPos] = $before;
                        $fixing[$found] = $number;
                        $fixingPos = $found;
                        $changed = true;
                    }
                }
            }
            foreach ($rules['after'] as $after) {
                $found = array_search($after, $update);
                $this->io->text("Result for [".$fixingPos."]".$number." after(".$after."): ".$found);
                if ($found !== false) {
                    if ($fixingPos > $found) {
                        $this->io->text("AFTER: Changing [".$fixingPos."]".$number." with [".$found."]".$after);
                        $fixing[$fixingPos] = $after;
                        $fixing[$found] = $number;
                        $fixingPos = $found;
                        $changed = true;
                    }
                }
            }
        }
        if ($changed) return $fixing;
        return false;
    }

    private function rulesToApply(int $number) :array
    {
        $rules = ['after'=>[], 'before'=>[]];
        foreach ($this->rules as $rule) {
            if ($number == $rule[0]) {
                $rules['after'][] = $rule[1];
            }
            if ($number == $rule[1]) {
                $rules['before'][] = $rule[0];
            }
        }
        return $rules;
    }

    private function getTheMiddlePageNumber(array $update) :int
    {
        $numero = $update[intdiv(count($update),2)];
//        var_dump($update,$numero);
        return $numero;
    }

    private function numbersMustBeBefore(int $number) : array
    {
        $numbersMustBeBefore = [];
        foreach ($this->rules as $rule) {
            if ($rule[1] == $number) {
                $numbersMustBeBefore[] = $rule[0];
            }
        }
        return $numbersMustBeBefore;
    }

}
