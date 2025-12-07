<?php

namespace App\Challenges;

interface ChallengeInterface
{
    /**
     * @param string $paso El valor de la variable 'paso'.
     */
    public function execute(string $paso): string;
    public function getData(): void;
}
