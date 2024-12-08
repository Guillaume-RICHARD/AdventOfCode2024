<?php

namespace App\Http\Controllers;

class AdventDay6Controller extends Controller
{
    public function index()
    {
        // Lire le contenu du fichier (carte de l'entrée)
        $filePath = storage_path('app/aoc/day6.txt');
        if (!file_exists($filePath)) {
            return "Fichier introuvable.";
        }

        $map = array_map('str_split', file($filePath, FILE_IGNORE_NEW_LINES));
        $startPosition = $this->findGuardPosition($map);
        $visitedPositions = $this->simulateGuard($map, $startPosition);

        return "Le garde a visité " . count($visitedPositions) . " positions distinctes avant de quitter la zone.";
    }

    public function test()
    {
        // Exemple de carte
        $map = [
            ['.', '.', '.', '.', '#', '.', '.', '.', '.', '.'],
            ['.', '.', '.', '.', '.', '.', '.', '.', '.', '#'],
            ['.', '.', '.', '.', '.', '.', '.', '.', '.', '.'],
            ['.', '.', '#', '.', '.', '.', '.', '.', '.', '.'],
            ['.', '.', '.', '.', '.', '.', '#', '.', '.', '.'],
            ['.', '.', '.', '.', '.', '.', '.', '.', '.', '.'],
            ['.', '#', '.', '.', '^', '.', '.', '.', '.', '.'],
            ['.', '.', '.', '.', '.', '.', '.', '.', '#', '.'],
            ['#', '.', '.', '.', '.', '.', '.', '.', '.', '.'],
            ['.', '.', '.', '.', '.', '.', '#', '.', '.', '.'],
        ];

        $startPosition = $this->findGuardPosition($map);
        $visitedPositions = $this->simulateGuard($map, $startPosition);

        echo "Le garde a visité " . count($visitedPositions) . " positions distinctes.\n";
    }

    private function findGuardPosition($map)
    {
        foreach ($map as $y => $row) {
            foreach ($row as $x => $cell) {
                if (in_array($cell, ['^', '>', 'v', '<'])) {
                    return ['x' => $x, 'y' => $y, 'direction' => $cell];
                }
            }
        }

        throw new \Exception("Position initiale du garde introuvable.");
    }

    private function simulateGuard($map, $position)
    {
        $directions = [
            '^' => [0, -1],
            '>' => [1, 0],
            'v' => [0, 1],
            '<' => [-1, 0]
        ];
        $turns = [
            '^' => '>',
            '>' => 'v',
            'v' => '<',
            '<' => '^'
        ];
        $visited = [];
        $res = 0;

        while (true) {
            // Marquer la position actuelle comme visitée
            $visited[] = "{$position['x']},{$position['y']}";
            
            // Déterminer la prochaine position
            $dir = $directions[$position['direction']];
            $nextX = $position['x'] + $dir[0];
            $nextY = $position['y'] + $dir[1];

            // Vérifier si la prochaine position est hors de la carte ou un obstacle
            if (
                !isset($map[$nextY][$nextX]) ||$map[$nextY][$nextX] === '#'
            ) {
                // Tourner à droite
                $position['direction'] = $turns[$position['direction']];
            } else {
                // Avancer
                $position['x'] = $nextX;
                $position['y'] = $nextY;
            }

            // Arrêter si le garde sort de la carte
            if (($position['y'] === 0) || ($position['x'] === 0)) {
                // unset($visited[0]);
                break;
            }
        }

        return $visited;
    }
}
