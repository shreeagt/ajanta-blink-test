<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHierarchyColumnsToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('dm_name')->nullable()->after('hq');
            $table->string('rsm_name')->nullable()->after('dm_name');
            $table->string('state')->nullable()->after('rsm_name');
            $table->string('role')->default('SO')->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['dm_name', 'rsm_name', 'state', 'role']);
        });
    }
}
