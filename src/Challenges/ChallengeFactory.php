<?php

namespace App\Challenges;

class ChallengeFactory
{
    /**
     * @param string $year El valor de la variable 'año'.
     * @param string $day El valor de la variable 'día'.
     * @return ChallengeInterface
     * @throws \InvalidArgumentException si no se encuentra la estrategia.
     */
    public function createChallenge(string $year, string $day, string $path): ChallengeInterface
    {
        $challengeClass = "App\\Challenges\\Year{$year}Day{$day}Challenge";

        if (!class_exists($challengeClass)) {
            // Manejo de error si la combinación no existe
            throw new \InvalidArgumentException("No se encontró el reto para el año $year y día $day.");
        }

        return new $challengeClass($path);
    }
}
