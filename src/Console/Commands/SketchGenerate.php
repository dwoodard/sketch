<?php

namespace Dwoodard\Sketch\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
    public function __construct(Yaml $yaml, Schema $schema, Artisan $artisan, DB $db)
    {
        parent::__construct();
        $this->yaml = $yaml;
        $this->schema = $schema;
        $this->artisan = $artisan;
        $this->db = $db;
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
                $hasTable = Schema::hasTable($tableName);

                $fields = collect();

                $columns->each(function ($values, $key) use($fields) {
                    $fields->push("{$key}:$values");
                });

                try {

                    Artisan::call('migrate');


                    // if table exists check if all fields from sketch schema are there
                    // if they are not add them other wise skip

                    //if $tableName doesn't exists in DB create a table and fields and migrate
                    if (!$hasTable){
                        $this->info("Creating: $tableName");

                        // Create Table with schema
                        Artisan::call('make:migration:schema',
                            [
                                'name' => "create_{$tableName}_table",
                                '--schema' => "{$fields->join(',')}",
                                '--model' => true
                            ],
                        );

                        Artisan::call('migrate');

                    }else{
                        $this->warn("Already exists in DB: $tableName");
                        $this->info("  Checking for missing fields in DB");

                        $keys = collect();
                        $missing_fields = collect();

                        try {
                            // check for added fields
                            $columns->each(function ($values, $key) use($tableName, $missing_fields, $keys){
                                if (!Schema::hasColumn($tableName, $key)){
                                    dump("missing");
                                    $keys->push("{$key}");
                                    $missing_fields->push("{$key}:$values");
                                    $this->info("  adding {$key}");
                                }
                            });

                            if($missing_fields->count() > 0){
                                Artisan::call('make:migration:schema',
                                    [
                                        'name' => "add_{$keys->join('_')}_from_{$tableName}_table",
                                        '--schema' => "{$missing_fields->join(',')}"
                                    ],
                                );
                            }else{
                                $this->info("  -  No fields to add");
                            }

                        }catch (\Exception $e){
                            $e->getMessage();
                        }

                        try {
                            // check for removed fields in schema
                            $this->info('  Check for removed fields in schema');
                            $columns->each(function ($values, $key) use($tableName, $missing_fields, $keys){
                                if (!Schema::hasColumn($tableName, $key)){
                                    dump("missing");
                                    $keys->push("{$key}");
                                    $missing_fields->push("{$key}:$values");
                                    $this->info("  adding {$key}");
                                }
                            });

                            $db_fields = collect(Schema::getColumnListing($tableName))
                                // remove timestamps from example
                                ->filter(function($c){
                                    return !in_array($c, ['created_at','updated_at']) ;
                                });



                            $diff = $db_fields->diff($columns->keys())->values();
                            $schema = collect();
                            $diff->each(function($colName) use($tableName, $schema){
                                $type = DB::getSchemaBuilder()->getColumnType($tableName, $colName);
                                $schema->push("{$colName}:{$type}");
                            });

                            if($diff->count() > 0){
                                Artisan::call('make:migration:schema',
                                    [
                                        'name' => "remove_{$diff->join('_')}_from_{$tableName}_table",
                                        '--schema' => $schema->join(',')
                                    ],
                                );
                                $this->info("  - removing {$fields} {$diff->join(' ')}");
                            }


                        }catch (\Exception $e)
                        {
                            dump($e->getMessage());
                        }


                    }
                }
                catch (\Symfony\Component\ErrorHandler\Error $e){
                    dump($e->getMessage());
                }
            })
        ;

        return 0;
    }
}
