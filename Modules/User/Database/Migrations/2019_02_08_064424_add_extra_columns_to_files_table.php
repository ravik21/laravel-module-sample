<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraColumnsToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->tinyInteger('is_folder')->default(0)->after('id');
            $table->string('extension')->nullable()->after('path');
            $table->integer('folder_id')->unsigned()->after('extension');
            $table->integer('width')->default(0)->after('size');
            $table->integer('height')->default(0)->after('width');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn(['is_folder', 'extension', 'folder_id', 'width', 'height']);
        });
    }
}
