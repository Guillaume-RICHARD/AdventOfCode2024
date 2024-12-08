<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdventDay7Controller extends Controller
{
    public function index()
    {
        // Lire le fichier contenant les équations
        $filePath = storage_path('app/aoc/day7.txt');
        if (!file_exists($filePath)) {
            return "Le fichier day7.txt n'existe pas.\n";
        }

        $fileContent = file_get_contents($filePath);

        // Analyser les lignes
        $lines = explode("\n", trim($fileContent));
        $part1Result = 0;
        $part2Result = 0;

        foreach ($lines as $line) {
            [$targetValue, $numbers] = explode(':', $line);
            $targetValue = (int)trim($targetValue);
            $numbers = array_map('intval', explode(' ', trim($numbers)));

            // Vérifier pour la partie 1
            if ($this->canBeSolvedPart1($targetValue, $numbers)) {
                $part1Result += $targetValue;
            }

            // Vérifier pour la partie 2 (inclut l'opérateur de concaténation)
            if ($this->canBeSolvedPart2($targetValue, $numbers)) {
                $part2Result += $targetValue;
            }
        }

        return "Résultat total de la partie 1 : {$part1Result}\n" .
            "Résultat total de la partie 2 : {$part2Result}\n";
    }

    public function test()
    {
        // Exemple de test avec des données codées en dur
        $testInput = [
            "190: 10 19",
            "3267: 81 40 27",
            "83: 17 5",
            "156: 15 6",
            "7290: 6 8 6 15",
            "192: 17 8 14",
        ];

        $part1Result = 0;
        $part2Result = 0;

        foreach ($testInput as $line) {
            [$targetValue, $numbers] = explode(':', $line);
            $targetValue = (int)trim($targetValue);
            $numbers = array_map('intval', explode(' ', trim($numbers)));

            // Vérifier pour la partie 1
            if ($this->canBeSolvedPart1($targetValue, $numbers)) {
                $part1Result += $targetValue;
            }

            // Vérifier pour la partie 2 (inclut l'opérateur de concaténation)
            if ($this->canBeSolvedPart2($targetValue, $numbers)) {
                $part2Result += $targetValue;
            }
        }

        echo "Résultat total de la partie 1 : {$part1Result}\n";
        echo "Résultat total de la partie 2 : {$part2Result}\n";
    }

    private function canBeSolvedPart1(int $targetValue, array $numbers): bool
    {
        // Vérifie uniquement avec les opérateurs "+" et "*"
        return $this->evaluateOperators($numbers, $targetValue, 0, $numbers[0], ['+', '*']);
    }

    private function canBeSolvedPart2(int $targetValue, array $numbers): bool
    {
        // Vérifie avec les opérateurs "+", "*", et "||"
        return $this->evaluateOperators($numbers, $targetValue, 0, $numbers[0], ['+', '*', '||']);
    }

    private function evaluateOperators(array $numbers, int $targetValue, int $index, $currentValue, array $operators): bool
    {
        // Si nous avons traité tous les nombres
        if ($index == count($numbers) - 1) {
            return $currentValue == $targetValue;
        }

        $nextValue = $numbers[$index + 1];

        // Tester les opérateurs disponibles
        foreach ($operators as $operator) {
            if ($operator === '+') {
                if ($this->evaluateOperators($numbers, $targetValue, $index + 1, $currentValue + $nextValue, $operators)) {
                    return true;
                }
            } elseif ($operator === '*') {
                if ($this->evaluateOperators($numbers, $targetValue, $index + 1, $currentValue * $nextValue, $operators)) {
                    return true;
                }
            } elseif ($operator === '||') {
                $concatenatedValue = (int)($currentValue . $nextValue);
                if ($this->evaluateOperators($numbers, $targetValue, $index + 1, $concatenatedValue, $operators)) {
                    return true;
                }
            }
        }

        return false;
    }
}
