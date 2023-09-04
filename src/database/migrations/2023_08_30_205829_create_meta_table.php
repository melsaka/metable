<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaTable extends Migration
{
    protected $tableName;
    
    protected $customTables;

    public function __construct()
    {
        $this->tableName = config('metable.tables.default', 'meta');
        $this->customTables = config('metable.tables.custom', []);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->defaultTableSchema();

        $this->customTablesSchemas();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropDefaultTable();

        $this->dropCustomTables();
    }

    public function createTableSchema($tableName = null)
    {
        $tableName = $tableName ?: $this->tableName; 

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->unsigned();
            $table->string('parent_type');
            $table->string('key');
            $table->text('value');
            $table->string('type')->default('string');

            $table->index(['parent_type', 'parent_id']);
            $table->index(['parent_type', 'parent_id', 'key']);
            $table->unique(['parent_type', 'parent_id', 'key']);
            $table->timestamps();
        });
    }

    public function defaultTableSchema()
    {
        $this->createTableSchema($this->tableName);
    }
    
    public function customTablesSchemas()
    {
        foreach ($this->customTables as $tableName) {
            $this->createTableSchema($tableName);
        }
    }

    public function dropDefaultTable()
    {
        Schema::dropIfExists($this->tableName);
    }


    public function dropCustomTables()
    {
        foreach ($this->customTables as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }

}
