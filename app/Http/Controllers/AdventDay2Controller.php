<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdventDay2Controller extends Controller
{
    /**
     *  Logique de l'exercice du Jour 1
     * @return string
     */
    public function index()
    {
        // Lire le contenu du fichier
        $filePath = storage_path('app/aoc/day2.txt');
        if (!file_exists($filePath)) {
            return "Le fichier pour le jour 2 n'existe pas.\n";
        }

        $fileContent = file_get_contents($filePath);
        $lines = explode("\n", trim($fileContent));

        $safeReportsCount = $this->countSafeReports($lines); // Compter le nombre de rapports sûrs (Exo 1)
        $safeReportsCountDampener = $this->countSafeReportsDampener($lines); // Compter le nombre de rapports sûrs (Exo 2)

        return "Nombre de rapports sûrs : " . $safeReportsCount . "\nNombre de rapports sûrs (avec le problème atténuateur) : " . $safeReportsCountDampener;
    }

    /**
     * Logique de test du Jour 1
     * @return string
     */
    public function test()
    {
        // Exemple de données pour les tests
        $testData = [
            "7 6 4 2 1", // Sûr
            "1 2 7 8 9", // Non sûr
            "9 7 6 2 1", // Non sûr
            "1 3 2 4 5", // Non sûr
            "8 6 4 4 1", // Non sûr
            "1 3 6 7 9"  // Sûr
        ];

        $safeReportsCount = $this->countSafeReports($testData);

        return "Nombre de rapports sûrs dans les tests : " . $safeReportsCount;
    }

    /**
     * méthode de comptage pour l'exercice 1
     * @param array $reports
     * @return int
     */
    private function countSafeReports(array $reports)
    {
        $safeCount = 0;

        foreach ($reports as $report) {
            $levels = array_map('intval', explode(' ', trim($report)));
            if ($this->isSafeReport($levels)) {
                $safeCount++;
            }
        }

        return $safeCount;
    }

    /**
     * méthode de comptage pour l'exercice 2
     * @param array $reports
     * @return int
     */
    private function countSafeReportsDampener(array $reports)
    {
        $safeCount = 0;

        foreach ($reports as $report) {
            $levels = array_map('intval', explode(' ', trim($report)));
            if ($this->isSafeWithProblemDampener($levels)) {
                $safeCount++;
            }
        }

        return $safeCount;
    }

    /**
     * Détermine si le niveau est croissants ou décroissants
     * @param array $levels
     * @return bool
     */
    private function isSafeReport(array $levels)
    {
        // Déterminer si les niveaux sont croissants ou décroissants
        $isIncreasing = true;
        $isDecreasing = true;

        for ($i = 1; $i < count($levels); $i++) {
            $diff = $levels[$i] - $levels[$i - 1];

            // Vérifier si la différence est en dehors de l'intervalle [1, 3]
            if (abs($diff) < 1 || abs($diff) > 3) {
                return false;
            }

            // Vérifier si les niveaux ne sont pas strictement croissants
            if ($diff < 0) {
                $isIncreasing = false;
            }

            // Vérifier si les niveaux ne sont pas strictement décroissants
            if ($diff > 0) {
                $isDecreasing = false;
            }
        }

        // Le rapport est sûr s'il est strictement croissant ou décroissant
        return $isIncreasing || $isDecreasing;
    }

    /**
     * détermine la sureté du rapport
     * @param array $levels
     * @return bool
     */
    private function isSafeWithProblemDampener(array $levels)
    {
        // Si le rapport est déjà sûr sans modification
        if ($this->isSafeReport($levels)) {
            return true;
        }

        // Vérifier toutes les sous-listes possibles en supprimant un seul élément
        for ($i = 0; $i < count($levels); $i++) {
            $modifiedLevels = $levels;
            unset($modifiedLevels[$i]); // Supprime le niveau à l'index $i
            $modifiedLevels = array_values($modifiedLevels); // Réindexe les éléments

            // Vérifier si la sous-liste est sûre
            if ($this->isSafeReport($modifiedLevels)) {
                return true;
            }
        }

        // Si aucune modification ne rend le rapport sûr, il reste non sûr
        return false;
    }

}
