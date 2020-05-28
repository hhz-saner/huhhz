<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(config('admin.database.users_table'), function (Blueprint $table) {
            $table->string('open_id')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn(config('admin.database.users_table'), 'open_id')) {
            Schema::table(config('admin.database.users_table'), function (Blueprint $table) {
                $table->dropColumn('open_id');
            });
        }
    }
}
