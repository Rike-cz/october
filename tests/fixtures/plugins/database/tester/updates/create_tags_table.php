<?php namespace Database\Tester\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTagsTable extends Migration
{

    public function up()
    {
        Schema::create('database_tester_tags', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
        });

        Schema::create('database_tester_taggables', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('tag_id')->unsigned()->index();
            $table->morphs('taggable');
            $table->string('comment')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('database_tester_taggables');
        Schema::dropIfExists('database_tester_tags');
    }
}
