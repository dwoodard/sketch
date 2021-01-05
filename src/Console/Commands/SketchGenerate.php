<?php

namespace Dwoodard\Sketch\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class SketchGenerate extends BaseSketchCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sketch:generate';

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
    public function __construct(Yaml $yaml, Schema $schema, Artisan $artisan)
    {
        parent::__construct();
//        $this->yaml = $yaml;
//        $this->schema = $schema;
//        $this->artisan = $artisan;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        parent::handle();

        $sketch = collect(Yaml::parseFile(base_path('sketch/schema.yml')));

        collect($sketch->get('Models'))
//            ->dump('Models with tables')
            ->flatMap(function($columns, $tableName){
                // CHECK FOR TABLE IN DATABASE
                $columns = collect(collect($columns)->get('columns'));
                $this->info($tableName . Schema::hasTable($tableName));

                try {
                    // Create Table with schema
                    Artisan::call('make:migration:schema', ['name' => "create{$tableName}_table", '--create' => $tableName]);

                    // if table created, check if fields added to it



                    //migrate
                    Artisan::call('migrate');

                }
                catch (\Exception $e){
                    // if "class already exists." check other field to add
                    if(str_contains( $e->getMessage(), 'class already exists' )){

                        $addFields = collect();

                        foreach ($columns->keys() as $field){
                            if (!Schema::hasColumn($tableName, $field)) {
                                $addFields->push($field);
                            }
                        }

                        Artisan::call('make:migration', ['name' => "add_{$field}_to_{$tableName}", '--table' => $tableName]);

                    }
                }
            })
        ;


        return 0;
    }
}
