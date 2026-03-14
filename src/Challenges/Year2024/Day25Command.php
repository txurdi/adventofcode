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
    name: 'txurdi:2024:day25',
    description: 'Day 25 of the Advent code of 2024',
)]
class Day25Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="25";
    private ?string $test=null;

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
//        var_dump($data);
        $keys = [];
        $locks = [];
        $tipo = 0;
        foreach ($data as $nl=>$row) {
            if (count($row)==0) {
                if ($tipo==1) {
                    $keys[] = $tmp;
                } else if ($tipo==2) {
                    $locks[] = $tmp;
                }
                $tipo = 0;
                continue;
            }
            if (($tipo==0) && ($row[0]=='#')) {
                $tipo = 1;
                $tmp = [1,1,1,1,1];
                continue;
            }
            if (($tipo==0) && ($row[0]=='.')) {
                $tipo = 2;
                $tmp = [0,0,0,0,0];
                continue;
            }
            foreach ($row as $nr=>$cell) {
                if ($cell=='#') {
                    $tmp[$nr] += 1;
                }
            }
        }
        if ($tipo==1) {
            $keys[] = $tmp;
        } else if ($tipo==2) {
            $locks[] = $tmp;
        }
        $this->io->title('KEYs:'.count($keys));
        foreach ($keys as $key) {
            SolveHelper::dibujaSeguido($key);
        }
        $this->io->title('LOCKs:'.count($locks));
        foreach ($locks as $lock) {
            SolveHelper::dibujaSeguido($lock);
        }
        $startedAt = microtime(true);

        $fits = 0;
        foreach ($keys as $key) {
            foreach ($locks as $lock) {
                if ($this->keyLockPair($key, $lock)) {
                    $fits++;
                };
            }
        }

        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('RESULT: '.$fits);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $startedAt = microtime(true);

        $result = '?';

        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function loadData(): array
    {
        if (!isset($this->test)) {
            $filePath = __DIR__ . "/../../Data/2024/day" . self::DAY . ".txr";
        } else {
            $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test".$this->test.".txr";
        }
        $this->io->warning('Loading file: '.$filePath);
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLineAndChar($filePath);
    }

    private function keyLockPair(mixed $key, mixed $lock)
    {
        for ($i=0; $i < 5; $i++) {
            if ($lock[$i]+$key[$i]>7) {
                return false;
            }
        }
        return true;
    }

}
