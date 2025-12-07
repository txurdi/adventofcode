<?php

namespace App\Challenges;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class Year2025Day1Challenge implements ChallengeInterface
{
    private $paso;
    private $data;
    private array $error = [];
    private string $result = '';
    public function __construct(
        private readonly string $projectDir,
    )
    {

    }

    private function executePart1(): void
    {


    }

    public function execute(string $paso): string
    {
        $this->paso = $paso;

        try {
            $this->getData();
        } catch (\Throwable $th) {
            Throw new \Exception('No puedo cargar los datos porque:' . $th->getMessage());
        }

        try {
            switch ($paso) {
                case '1':
                    $this->executePart1();
                    break;
//                case '2':
//                    $this->result = $this->executePart2();
//                    break;
//                case 'test':
//                    $this->result = $this->executeTestPart();
//                    break;
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
        $this->dataPath = '';
        $this->data = [];
        $this->aqsefop
        var_dump($this->projectDir);
//        $filePath = __DIR__ . "/../../Data/2024/day" . self::DAY . ".txr";
//        } else {
//            $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test".$this->test.".txr";
//        }
//        $this->io->warning('Loading file: '.$filePath);
////        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
////        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";
//
//        return SolveHelper::fileToArrayByLineAndChar($filePath);
    }

}
