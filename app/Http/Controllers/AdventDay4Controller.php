<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdventDay4Controller extends Controller
{
    //
    public function index()
    {
        // Lire le contenu du fichier
        $filePath = storage_path('app/aoc/day4.txt');
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
        } else {
            return "Le fichier day4.txt n'existe pas.\n";
        }


        $grid = $this->parseGrid($fileContent); // Charger la grille de mots
        $word = "XMAS"; // Mot à rechercher
        $occurrences = $this->findAllOccurrences($grid, $word); // Trouver toutes les occurrences

        // Trouver toutes les occurrences de X-MAS
        $occurrences2 = $this->findAllXMASShapes($grid);

        // Résultat
        return "Le mot '{$word}' apparaît un total de : " . count($occurrences) . " fois.\n" . "Un X-MAS apparaît un total de : " . count($occurrences2) . " fois.\n";
    }

    public function test()
    {
        // Exemple de grille pour tester
        $grid = [
            ['M', 'M', 'M', 'S', 'X', 'X', 'M', 'A', 'S', 'M'],
            ['M', 'S', 'A', 'M', 'X', 'M', 'S', 'M', 'S', 'A'],
            ['A', 'M', 'X', 'S', 'X', 'M', 'A', 'A', 'M', 'M'],
            ['M', 'S', 'A', 'M', 'A', 'S', 'M', 'S', 'M', 'X'],
            ['X', 'M', 'A', 'S', 'A', 'M', 'X', 'A', 'M', 'M'],
            ['X', 'X', 'A', 'M', 'M', 'X', 'X', 'A', 'M', 'A'],
            ['S', 'M', 'S', 'M', 'S', 'A', 'S', 'X', 'S', 'S'],
            ['S', 'A', 'X', 'A', 'M', 'A', 'S', 'A', 'A', 'A'],
            ['M', 'A', 'M', 'M', 'M', 'X', 'M', 'M', 'M', 'M'],
            ['M', 'X', 'M', 'X', 'A', 'X', 'M', 'A', 'S', 'X'],
        ];

        // Exemple de grille pour tester
        $grid2 = [
            ['.', 'M', '.', 'S', '.', '.', '.', '.', '.', '.'],
            ['.', '.', 'A', '.', '.', 'M', 'S', 'M', 'S', '.'],
            ['.', 'M', '.', 'S', '.', 'M', 'A', 'A', '.', '.'],
            ['.', '.', 'A', '.', 'A', 'S', 'M', 'S', 'M', '.'],
            ['.', 'M', '.', 'S', '.', 'M', '.', '.', '.', '.'],
            ['.', '.', '.', '.', '.', '.', '.', '.', '.', '.'],
            ['S', '.', 'S', '.', 'S', '.', 'S', '.', 'S', '.'],
            ['.', 'A', '.', 'A', '.', 'A', '.', 'A', '.', '.'],
            ['M', '.', 'M', '.', 'M', '.', 'M', '.', 'M', '.'],
            ['.', '.', '.', '.', '.', '.', '.', '.', '.', '.'],
        ];

        $word = "XMAS";
        $occurrences = $this->findAllOccurrences($grid, $word);
        $occurrences2 = $this->findAllXMASShapes($grid2);

        return "Le mot '{$word}' apparaît un total de : " . count($occurrences) . " fois.\n"."Un X-MAS apparaît un total de : " . count($occurrences2) . " fois.\n";
    }

    private function parseGrid($fileContent)
    {
        $lines = explode("\n", trim($fileContent));
        $grid = [];
        foreach ($lines as $line) {
            $grid[] = str_split(trim($line));
        }
        return $grid;
    }

    private function findAllOccurrences($grid, $word)
    {
        $directions = [
            [0, 1],   // Droite
            [1, 0],   // Bas
            [1, 1],   // Diagonale Bas-Droite
            [1, -1],  // Diagonale Bas-Gauche
            [0, -1],  // Gauche
            [-1, 0],  // Haut
            [-1, -1], // Diagonale Haut-Gauche
            [-1, 1],  // Diagonale Haut-Droite
        ];

        $occurrences = [];
        $rows = count($grid);
        $cols = count($grid[0]);

        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                foreach ($directions as $direction) {
                    if ($this->checkWord($grid, $word, $i, $j, $direction)) {
                        $occurrences[] = [$i, $j];
                    }
                }
            }
        }

        return $occurrences;
    }

    private function checkWord($grid, $word, $row, $col, $direction)
    {
        $rows = count($grid);
        $cols = count($grid[0]);
        $len = strlen($word);

        for ($k = 0; $k < $len; $k++) {
            $newRow = $row + $k * $direction[0];
            $newCol = $col + $k * $direction[1];

            if ($newRow < 0 || $newRow >= $rows || $newCol < 0 || $newCol >= $cols) {
                return false;
            }

            if ($grid[$newRow][$newCol] !== $word[$k]) {
                return false;
            }
        }

        return true;
    }

    private function findAllXMASShapes($grid)
    {
        $rows = count($grid);
        $cols = count($grid[0]);
        $occurrences = [];
        $occ = 0;

        for ($i = 1; $i < $rows - 1; $i++) {
            for ($j = 1; $j < $cols - 1; $j++) {
                $occ++;
                if ($this->isXMASShape($grid, $i, $j)) {
                    $occurrences[] = [$i, $j];
                }
            }
        }

        return $occurrences;
    }

    private function isXMASShape($grid, $row, $col)
    {
        // Vérifier les positions d'un X-MAS :
        $conditions = [
            $grid[$row][$col] === 'A',        // A au centre
            $grid[$row - 1][$col - 1] === 'M' && $grid[$row + 1][$col + 1] === 'S', // M au-dessus à gauche / S en dessous à droite
            $grid[$row + 1][$col + 1] === 'M' && $grid[$row - 1][$col - 1] === 'S', // M en dessous à droite / S au-dessus à gauche
            $grid[$row - 1][$col + 1] === 'M' && $grid[$row + 1][$col - 1] === 'S', // M au-dessus à droite / S en dessous à gauche
            $grid[$row + 1][$col - 1] === 'M' && $grid[$row - 1][$col + 1] === 'S', // M au-dessous à droite / S en dessus à gauche
        ];

        // Compter le nombre d'éléments à true
        $countTrue = count(array_filter($conditions));
        if ($countTrue === 3) {
            return true;
        }

        return false;
    }
}
