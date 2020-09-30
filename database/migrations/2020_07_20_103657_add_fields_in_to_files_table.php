<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddFieldsInToFilesTable
 */
class AddFieldsInToFilesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('files', function (Blueprint $table) {
            $table->string('title')->after('width')->nullable();
            $table->string('alt')->after('title')->nullable();
            $table->string('link')->after('alt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('alt');
            $table->dropColumn('link');
        });
    }
}
