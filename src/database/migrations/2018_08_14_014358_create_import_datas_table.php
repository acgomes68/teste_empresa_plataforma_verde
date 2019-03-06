<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_datas', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('name', 50);
            $table->boolean('free_shipping')->default(false);
            $table->string('description', 255)->nullable();
            $table->float('price', 7, 2)->unsigned();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('import_file_id')->nullable();
            $table->foreign('import_file_id')->references('id')->on('import_files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_datas');
    }
}
