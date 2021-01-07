<?php

namespace Dwoodard\Sketch\Console\Commands;

use Illuminate\Support\Collection;
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

        $sketch = $this->LoadSketchYamlFile($this);

        Artisan::call('migrate');

        collect($sketch->get('Models'))
            ->flatMap(function($columns, $tableName){
                // CHECK FOR TABLE IN DATABASE
                $columns = collect(collect($columns)->get('columns'));
                $hasTable = Schema::hasTable($tableName);

                $fields = collect();

                $columns->each(function ($values, $key) use($fields) {
                    $fields->push("{$key}:$values");
                });










                // if table exists check if all fields from sketch schema are there
                // if they are not add them other wise skip

                //if $tableName doesn't exists in DB create a table and fields and migrate
                !$hasTable
                    ? $this->createTable($tableName, $fields)
                    : $this->createFields($tableName, $columns, $fields);


//                $this->addFieldsMigration();
//                $this->removeFieldsMigration();


            })
        ;
        Artisan::call('migrate');
        return 0;
    }

    /**
     * @param Collection $columns
     * @param $tableName
     * @param Collection $missing_fields
     * @param Collection $keys
     */
    public function checkColumns(Collection $columns, $tableName, Collection $missing_fields, Collection $keys): void
    {
        $columns->each(function ($values, $key) use ($tableName, $missing_fields, $keys) {
            if (!Schema::hasColumn($tableName, $key)) {
                dump("missing");
                $keys->push("{$key}");
                $missing_fields->push("{$key}:$values");
                $this->info("  adding {$key}");
            }
        });
    }



    /**
     * @param $tableName
     * @param Collection $fields
     */
    public function createTable($tableName, Collection $fields): void
    {
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
    }

    public function addFieldsMigration(): void{}
    public function removeFieldsMigration(): void{}

    /**
     * @param $tableName
     * @param Collection $columns
     * @param Collection $fields
     * @return \Exception
     */
    public function createFields($tableName, Collection $columns, Collection $fields)
    {
        $this->warn("Already exists in DB: $tableName");
        $this->info("  Checking for missing fields in DB");

        $keys = collect();
        $missing_fields = collect();

        // check for added fields
        try {
            $this->info('  Check for added fields in schema');
            $this->checkColumns($columns, $tableName, $missing_fields, $keys);

            if ($missing_fields->count() > 0) {
                Artisan::call('make:migration:schema',
                    [
                        'name' => "add_{$keys->join('_')}_from_{$tableName}_table",
                        '--schema' => "{$missing_fields->join(',')}"
                    ],
                );
            } else {
                $this->info("  -  No fields to add");
            }

        } catch (\Exception $e) {
            $e->getMessage();
        }

        // check for removed fields in schema
        try {

            $this->info('  Check for removed fields in schema');
            $this->checkColumns($columns, $tableName, $missing_fields, $keys);

            $db_fields = collect(Schema::getColumnListing($tableName))
                // remove timestamps from example
                ->filter(function ($c) {
                    return !in_array($c, ['created_at', 'updated_at']);
                });


            $diff = $db_fields->diff($columns->keys())->values();
            $schema = collect();
            $diff->each(function ($colName) use ($tableName, $schema) {
                $type = DB::getSchemaBuilder()->getColumnType($tableName, $colName);
                $schema->push("{$colName}:{$type}");
            });

            if ($diff->count() > 0) {
                Artisan::call('make:migration:schema',
                    [
                        'name' => "remove_{$diff->join('_')}_from_{$tableName}_table",
                        '--schema' => $schema->join(',')
                    ],
                );
                # TODO: fix issue with diff - expecting something different here
                $this->info("  - removing {$fields} {$diff->join(' ')}");
            } else {
                $this->info("  -  No fields to remove");
            }


        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
