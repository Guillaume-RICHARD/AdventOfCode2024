<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdventDay1Controller extends Controller
{
    /**
     * Logique de l'exercice du Jour 1
     */
    public function index()
    {
        // Lire le contenu du fichier
        $filePath = storage_path('app/aoc/day1.txt');
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
        } else {
            echo "File does not exist.\n";
        }

        // Extraire les valeurs des listes
        $lines = explode("\n", trim($fileContent));
        $list1 = [];
        $list2 = [];

        foreach ($lines as $line) {
            list($value1, $value2) = explode('   ', trim($line));
            $list1[] = (int)$value1;
            $list2[] = (int)$value2;
        }

        // Calculer la distance totale
        $totalDistance = $this->calculateTotalDistance($list1, $list2);

        // Calculer le score de similarité
        $similarityScore = $this->calculateSimilarityScore($list1, $list2);

        // Afficher les résultats

        return "La distance totale est : " . $totalDistance . "\nLe score de similarité est : " . $similarityScore;
    }

    /**
     * Logique de test du Jour 1
     * @return void
     */
    public function test()
    {
        // Logique pour les tests du jour 1
        // Exemple d'utilisation
        $list1 = [3, 4, 2, 1, 3, 3];
        $list2 = [4, 3, 5, 3, 9, 3];

        $totalDistance = $this->calculateTotalDistance($list1, $list2);

        echo "La distance totale est : " . $totalDistance . "\n";
    }

    /**
     * Fonction de calcul de la distance entre les chiffres de 2 listes
     * @param $list1
     * @param $list2
     * @return float|int
     */
    function calculateTotalDistance($list1, $list2)
    {
        // Trier les deux listes
        sort($list1);
        sort($list2);

        $totalDistance = 0;

        // Calculer la distance pour chaque paire de numéros
        for ($i = 0; $i < count($list1); $i++) {
            $distance = abs($list1[$i] - $list2[$i]);
            $totalDistance += $distance;
        }

        return $totalDistance;
    }

    /**
     * fonction de calcul du score de similarité
     * @param $list1
     * @param $list2
     * @return float|int
     */
    private function calculateSimilarityScore($list1, $list2)
    {
        // Compter les occurrences de chaque numéro dans la liste de droite
        $countList2 = array_count_values($list2);

        $similarityScore = 0;

        // Calculer le score de similarité
        foreach ($list1 as $number) {
            if (isset($countList2[$number])) {
                $similarityScore += $number * $countList2[$number];
            }
        }

        return $similarityScore;
    }
}
