<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdventDay3Controller extends Controller
{
    /**
     *  Logique de l'exercice du Jour 3
     * @return string
     */
    public function index()
    {
        // Lire le contenu du fichier
        $filePath = storage_path('app/aoc/day3.txt');
        $filePath2 = storage_path('app/aoc/day3-2.txt');
        if (!file_exists($filePath) || !file_exists($filePath2)) {
            return "Le fichier pour le jour 3 n'existe pas.\n";
        }

        $fileContent = file_get_contents($filePath);
        // Scanner les instructions valides `mul`
        $totalSum = $this->processMemory($fileContent);

        $fileContent2 = file_get_contents($filePath2);
        $totalSum2 = $this->processConditionalMemory($fileContent);

        // Retourner le résultat
        return "La somme totale des multiplications valides est : " . $totalSum. "\nLa somme totale des multiplications activées est : " . $totalSum2;
    }

    /**
     * Logique de test du Jour 3
     * @return string
     */
    public function test()
    {
        // Exemple de mémoire corrompue
        $corruptedMemory = "xmul(2,4)%&mul[3,7]!@^do_not_mul(5,5)+mul(32,64]then(mul(11,8)mul(8,5))";
        $corruptedConditionalMemory = "xmul(2,4)&mul[3,7]!^don't()_mul(5,5)+mul(32,64](mul(11,8)undo()?mul(8,5))";
    
        // Tester avec la mémoire corrompue
        $totalSum = $this->processMemory($corruptedMemory);
        $totalConditionalSum = $this->processConditionalMemory($corruptedConditionalMemory);

        return "La somme totale des multiplications valides est : " . $totalSum . "\nLa somme totale des multiplications activées est : " . $totalConditionalSum;
    }

    private function processMemory($memory)
    {
        // Rechercher les instructions valides `mul(X,Y)` avec une regex
        preg_match_all('/mul\((\d+),(\d+)\)/', $memory, $matches);

        $totalSum = 0;

        // Parcourir les résultats et calculer la somme
        foreach ($matches[1] as $key => $x) {
            $y = $matches[2][$key];
            $totalSum += $x * $y;
        }

        return $totalSum;
    }

    private function processConditionalMemory($memory)
    {
        // Rechercher toutes les instructions pertinentes
        preg_match_all('/(do\(\)|don\'t\(\)|mul\((\d+),(\d+)\))/', $memory, $matches);

        $totalSum = 0;
        $isEnabled = true; // Par défaut, les multiplications sont activées

        foreach ($matches[0] as $instruction) {
            if ($instruction === 'do()') {
                $isEnabled = true; // Réactiver les multiplications
            } elseif ($instruction === 'don\'t()') {
                $isEnabled = false; // Désactiver les multiplications
            } elseif (preg_match('/mul\((\d+),(\d+)\)/', $instruction, $mulMatch)) {
                if ($isEnabled) {
                    $x = (int)$mulMatch[1];
                    $y = (int)$mulMatch[2];
                    $totalSum += $x * $y;
                }
            }
        }

        return $totalSum;
    }
}
