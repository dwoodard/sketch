<?php

namespace Dwoodard\Sketch\Console\Commands;

use Illuminate\Console\Command;

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
}
