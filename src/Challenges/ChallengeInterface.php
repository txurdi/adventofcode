<?php

namespace App\Challenges;

interface ChallengeInterface
{
    /**
     * @param string $half El valor de la variable 'paso'.
     */
    public function execute(string $half): string;
    public function getData(): void;
}
