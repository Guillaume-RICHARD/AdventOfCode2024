<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunAdventOfCode extends Command
{
    protected $signature = 'aoc:run {day} {--test}';
    protected $description = 'Run Advent of Code exercises and tests';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $day = $this->argument('day');
        $test = $this->option('test');

        $this->info("Running Advent of Code for day {$day}");

        if ($test) {
            $this->callTestController($day);
        } else {
            $this->callExerciseController($day);
        }
    }

    protected function callExerciseController($day)
    {
        $controller = 'App\Http\Controllers\AdventDay'.$day.'Controller';
        if (class_exists($controller)) {
            $this->info(app($controller)->index());
        } else {
            $this->error("Controller for day {$day} not found.");
        }
    }

    protected function callTestController($day)
    {
        $controller = 'App\Http\Controllers\AdventDay'.$day.'Controller';
        if (class_exists($controller)) {
            $this->info(app($controller)->test());
        } else {
            $this->error("Controller for day {$day} not found.");
        }
    }
}