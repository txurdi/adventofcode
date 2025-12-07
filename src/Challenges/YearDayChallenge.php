<?php

namespace App\Challenges;

use App\util\fileDataHelper;
use App\util\tableDataHelper;

class YearDayChallenge implements ChallengeInterface
{
    protected string $half = '1';
    protected string $test = '1';
    protected string $dataPath;
    protected string $dataStr = '';
    protected array $data = [];
    protected array $error = [];
    protected string $result = '';
    protected string $format;

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
    public function execute(string $half, ?string $test='1'): string
    {
        $this->half = $half;
        $this->test = $test;

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
        $this->dataPath = $this->projectDir.'/src/Challenges/data/'.$this->year.'/day'.$this->day.'H'.$this->half.'T'.$this->test.'.txt';
        if (!file_exists($this->dataPath)) {
            Throw new \Exception('No puedo cargar los datos porque el archivo no existe: '.$this->dataPath);
        }
        if ($this->debug) {
            echo '**** Cargando datos del archivo: '.$this->dataPath.PHP_EOL;
        }
        switch ($this->format) {
            case fileDataHelper::DATA_FORMAT_LINES:
                $this->data = fileDataHelper::fileToArrayByLine($this->dataPath);
                if ($this->debug) {
                    echo '**INI**'.PHP_EOL;
                    echo tableDataHelper::arrayToString($this->data);
                    echo '**END**'.PHP_EOL;
                }
                break;
            case fileDataHelper::DATA_FORMAT_COLS:
                $this->data = fileDataHelper::fileToArrayByLineAndCol($this->dataPath);
                if ($this->debug) {
                    echo '**INI**'.PHP_EOL;
                    echo tableDataHelper::mapToString($this->data);
                    echo '**END**'.PHP_EOL;
                }
                break;
            case fileDataHelper::DATA_FORMAT_CHARS:
                $this->data = fileDataHelper::fileToArrayByLineAndChar($this->dataPath);
                if ($this->debug) {
                    echo '**INI**'.PHP_EOL;
                    echo tableDataHelper::mapToString($this->data);
                    echo '**END**'.PHP_EOL;
                }
                break;
            case fileDataHelper::DATA_FORMAT_STRING:
            default:
                $this->dataStr = fileDataHelper::fileToString($this->dataPath);
            if ($this->debug) {
                echo '**INI**'.PHP_EOL;
                echo $this->dataStr;
                echo '**END**'.PHP_EOL;
            }
            break;
        }
    }

}
