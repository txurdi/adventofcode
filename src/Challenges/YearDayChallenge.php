<?php

namespace App\Challenges;

use App\util\fileDataHelper;
use App\util\tableDataHelper;

class YearDayChallenge implements ChallengeInterface
{
    private int $half = 0;
    private string $dataPath;
    private array $data = [];
    private array $error = [];
    private string $result = '';

    public function __construct(
        private readonly int $year,
        private readonly int $day,
        private readonly string $projectDir,
        private readonly bool $debug
    )
    {

    }

    private function executePart1(): void {    }
    private function executePart2(): void {    }
    private function executeTestPart(): void {    }

    public function execute(string $half): string
    {
        $this->half = $half;

        try {
            $this->getData();
        } catch (\Throwable $th) {
            Throw new \Exception('No puedo cargar los datos porque:' . $th->getMessage());
        }

        try {
            switch ($half) {
                case '1':
                    $this->executePart1();
                    break;
                case '2':
                    $this->executePart2();
                    break;
                case 'test':
                    $this->executeTestPart();
                    break;
            }
        } catch (\Throwable $th) {
            $this->error[] = $th->getMessage();
        }

        if (!empty($this->error)) {
            $errorString = '';
            foreach ($this->error as $k => $e) {
                $errorString .= $k . ': ' . $e . PHP_EOL;
            }
            Throw new \Exception($errorString);
        }
        return $this->result;
    }

    public function getData(): void
    {
        $this->dataPath = $this->projectDir.'/src/Challenges/data/'.$this->year.'/day'.$this->day.'-'.$this->half.'.txt';
        if (!file_exists($this->dataPath)) {
            Throw new \Exception('No puedo cargar los datos porque el archivo no existe: '.$this->dataPath);
        }
        if ($this->debug) {
            echo '**** Cargando datos del archivo: '.$this->dataPath.PHP_EOL;
        }
        $this->data = fileDataHelper::fileToArrayByLineAndCol($this->dataPath);
        if ($this->debug) {
            echo '**INI**'.PHP_EOL;
            echo tableDataHelper::mapToString($this->data);
            echo '**END**'.PHP_EOL;
        }
    }

}
