<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdventDay8Controller extends Controller
{
    public function index()
    {
        // Lire le contenu du fichier
        $filePath = storage_path('app/aoc/day8.txt');
        if (!file_exists($filePath)) {
            return "File does not exist.\n";
        }

        $fileContent = file_get_contents($filePath);
        $lines = explode("\n", trim($fileContent));

        // Appel de la méthode pour calculer le nombre d'antinodes
        $antinodeCount = $this->calculateAntinodes($lines);

        return "Le nombre d'antinodes uniques est : " . $antinodeCount . "\n";
    }

    public function test()
    {
        // Tableau de test avec 14 antinœuds uniques
        $lines = [
            "......#....#",
            "...#....0...",
            "....#0....#.",
            "..#....0....",
            "....0....#..",
            ".#....A.....",
            "...#........",
            "#......#....",
            "........A...",
            ".........A..",
            "..........#.",
            "..........#.",
        ];

        // Appel de la méthode pour tester
        $antinodeCount = $this->calculateAntinodes($lines);

        echo "Le nombre d'antinodes uniques est : " . $antinodeCount . "\n";
    }

    private function calculateAntinodes($lines)
    {
        $antennas = [];

        // Convertir le tableau de lignes en un tableau d'antennes
        foreach ($lines as $y => $line) {
            for ($x = 0; $x < strlen($line); $x++) {
                $char = $line[$x];
                if ($char !== '.') {
                    // Ajouter l'antenne avec ses coordonnées et sa fréquence
                    $antennas[] = ['x' => $x, 'y' => $y, 'frequency' => $char];
                }
            }
        }

        $antinodes = [];

        // Calculer les antinodes en fonction des antennes
        foreach ($antennas as $i => $antenna1) {
            foreach ($antennas as $j => $antenna2) {
                // Ignorer les paires avec la même antenne ou des antennes de fréquence différente
                if ($i === $j || $antenna1['frequency'] !== $antenna2['frequency']) {
                    continue;
                }

                // Vérifier si l'une des antennes est deux fois plus éloignée que l'autre
                $dx = abs($antenna1['x'] - $antenna2['x']);
                $dy = abs($antenna1['y'] - $antenna2['y']);
                if ($dx === 0 && $dy > 0 && $dy % 2 === 0) {
                    $antinodes[] = ['x' => $antenna1['x'], 'y' => $antenna1['y'] + $dy / 2];
                    $antinodes[] = ['x' => $antenna2['x'], 'y' => $antenna2['y'] - $dy / 2];
                } elseif ($dy === 0 && $dx > 0 && $dx % 2 === 0) {
                    $antinodes[] = ['x' => $antenna1['x'] + $dx / 2, 'y' => $antenna1['y']];
                    $antinodes[] = ['x' => $antenna2['x'] - $dx / 2, 'y' => $antenna2['y']];
                }
            }
        }

        // Retirer les doublons d'antinodes
        $uniqueAntinodes = array_map("unserialize", array_unique(array_map("serialize", $antinodes)));

        return count($uniqueAntinodes);
    }
}
