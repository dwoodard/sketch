<?php

namespace Dwoodard\Sketch\Console\Commands;

use Illuminate\Filesystem\Filesystem;

class SketchInit extends BaseSketchCommand
{
    public const BASE_PATH = 'sketch';
    public const SCHEMA_NAME = 'schema.yml';
    public const CONFIG_NAME = 'config.php';
    public $src;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sketch:init {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create [Models, Migrations, Factories, Seeds, Routes] from schema.yml';



    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->src = dirname(dirname(__DIR__));
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        parent::handle();


        $this->info("Sketch Init:");
        $this->newline();


//        dump($this->option('force') && !file_exists((base_path(self::BASE_PATH))));
//        dump($this->option('force'));


        $this->createSketchDirectory();
        $this->createSketchSchema();
        $this->createSketchConfig();

        return 0;
    }

    public function createSketchDirectory()
    {
        if (!file_exists((base_path(self::BASE_PATH)))) {
            mkdir(base_path(self::BASE_PATH));
            $this->info("Created Directory:");
        } else {
            $this->info("Directory Exsisted:");
        }
        $this->line(base_path(self::BASE_PATH));
        $this->newline();
    }

    public function createSketchSchema()
    {
        $stubschemapath = $this->src . '/stubs/' . self::SCHEMA_NAME;
        $schemapath = base_path() . '/' . self::BASE_PATH . '/' . self::SCHEMA_NAME;

        if (!file_exists($schemapath) || $this->option('force')) {
            $this->info("Creating Schema: " . $schemapath );
            copy($stubschemapath, $schemapath);
        }else{
            $this->info("Schema Exsisted: " . $schemapath );
        }

    }

    public function createSketchConfig()
    {
        $stubpath = $this->src . '/stubs/' . self::CONFIG_NAME;
        $configpath = base_path() . '/config/sketch.php';

        if (!file_exists($configpath) || $this->option('force')) {
            $this->info("Creating: " . $configpath );
            copy($stubpath, $configpath);
        }else{
            $this->info("Config Exsisted: " . $configpath );
        }
    }

}
