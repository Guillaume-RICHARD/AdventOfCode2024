<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdventDay5Controller extends Controller
{
    public function index()
    {
        // Lire les règles et les mises à jour depuis le fichier
        $filePath = storage_path('app/aoc/day5.txt');
        if (!file_exists($filePath)) {
            return "Le fichier des données pour le jour 5 est introuvable.\n";
        }

        $fileContent = file_get_contents($filePath);
        list($rulesSection, $updatesSection) = explode("\n\n", trim($fileContent));

        // Parse les règles
        $rules = $this->parseRules(explode("\n", $rulesSection));

        // Parse les mises à jour
        $updates = array_map(fn($line) => array_map('intval', explode(',', trim($line))), explode("\n", trim($updatesSection)));

        // Filtrer les mises à jour correctes
        $validUpdates = [];
        foreach ($updates as $update) {
            if ($this->isValidUpdate($update, $rules)) {
                $validUpdates[] = $update;
            }
        }

        // Calculer la somme des pages centrales des mises à jour valides
        $sumMiddlePages = array_reduce($validUpdates, function ($sum, $update) {
            $middlePage = $update[floor(count($update) / 2)];
            return $sum + $middlePage;
        }, 0);

        // Calculer la somme des pages centrales corrigées
        $sumCorrectedMiddlePages = $this->fixAndSumIncorrectUpdates($rules, $updates);

        return "La somme des numéros de pages centrales des mises à jour valides est : " . $sumMiddlePages . "\nLa somme des numéros de pages centrales des mises à jour corrigées est : " . $sumCorrectedMiddlePages;
    }

    public function test()
    {
        // Exemple de données pour les tests
        $rules = [
            [47, 53],
            [97, 13],
            [97, 61],
            [97, 47],
            [75, 29],
            [61, 13],
            [75, 53],
            [29, 13],
        ];

        $updates = [
            [75, 47, 61, 53, 29],
            [97, 61, 53, 29, 13],
            [75, 29, 13],
            [75, 97, 47, 61, 53],
            [61, 13, 29],
            [97, 13, 75, 29, 47],
        ];

        $validUpdates = [];
        foreach ($updates as $update) {
            if ($this->isValidUpdate($update, $rules)) {
                $validUpdates[] = $update;
            }
        }

        $sumMiddlePages = array_reduce($validUpdates, function ($sum, $update) {
            $middlePage = $update[floor(count($update) / 2)];
            return $sum + $middlePage;
        }, 0);

        echo "Résultat du test : La somme des pages centrales valides est " . $sumMiddlePages . "\n";
    }

    private function parseRules($lines)
    {
        $rules = [];
        foreach ($lines as $line) {
            list($pageX, $pageY) = explode('|', trim($line));
            $rules[] = [(int)$pageX, (int)$pageY];
        }
        return $rules;
    }

    private function isValidUpdate($update, $rules)
    {
        // Créer une carte des positions des pages
        $positions = array_flip($update);

        // Vérifier chaque règle
        foreach ($rules as [$pageX, $pageY]) {
            if (isset($positions[$pageX]) && isset($positions[$pageY])) {
                if ($positions[$pageX] > $positions[$pageY]) {
                    return false; // Une règle est violée
                }
            }
        }

        return true; // Toutes les règles respectées
    }

    public function fixAndSumIncorrectUpdates($rules, $updates)
    {
        $incorrectUpdates = [];
        $correctedUpdates = [];

        // Identifier les mises à jour incorrectes
        foreach ($updates as $update) {
            if (!$this->isValidUpdate($update, $rules)) {
                $incorrectUpdates[] = $update;
            }
        }

        // Corriger les mises à jour incorrectes
        foreach ($incorrectUpdates as $update) {
            $correctedUpdate = $this->correctUpdate($update, $rules);
            $correctedUpdates[] = $correctedUpdate;
        }

        // Calculer la somme des pages centrales des mises à jour corrigées
        $sumMiddlePages = array_reduce($correctedUpdates, function ($sum, $update) {
            $middlePage = $update[floor(count($update) / 2)];
            return $sum + $middlePage;
        }, 0);

        return $sumMiddlePages;
    }

    private function correctUpdate($update, $rules)
    {
        // Générer une carte des dépendances
        $dependencies = [];
        foreach ($rules as [$pageX, $pageY]) {
            if (in_array($pageX, $update) && in_array($pageY, $update)) {
                $dependencies[$pageY][] = $pageX;
            }
        }

        // Trier les pages en respectant les dépendances
        $sortedUpdate = [];
        $remainingPages = $update;

        while (!empty($remainingPages)) {
            foreach ($remainingPages as $key => $page) {
                if (!isset($dependencies[$page]) || empty(array_diff($dependencies[$page], $sortedUpdate))) {
                    $sortedUpdate[] = $page;
                    unset($remainingPages[$key]);
                }
            }
        }

        return $sortedUpdate;
    }

}
