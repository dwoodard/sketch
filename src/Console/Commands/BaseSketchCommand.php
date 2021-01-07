<?php

namespace Dwoodard\Sketch\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

abstract class BaseSketchCommand extends Command
{
    /**
     * The common header for all Larawiz commands.
     *
     * @var string
     */
    protected const COMMAND_HEADER = "Laravel Sketch";

    /**
     * Execute the console command.
     *
     * @return mixed|void
     */
    public function handle()
    {

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function LoadSketchYamlFile($console): \Illuminate\Support\Collection
    {
        try {
            $sketch = collect(Yaml::parseFile(base_path('sketch/schema.yml')));
        } catch (ParseException $e) {
            $console->info("ParseException: " . $e->getMessage());
            if ($console->confirm('Do you want to create sketch/schema.yml?')) {
                Artisan::call('sketch:init');
            }
            die();
        } catch (ErrorException $e) {
        };
        return $sketch;
    }
}
