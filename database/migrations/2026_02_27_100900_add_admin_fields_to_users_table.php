<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_id')->nullable()->after('id');
            $table->string('role')->nullable()->after('role_id');
            $table->string('emp_no')->unique()->nullable()->after('name');
            $table->date('emp_dob')->nullable()->after('emp_no');
            $table->string('emp_function')->nullable()->after('emp_dob');
            $table->string('designation')->nullable()->after('emp_function');
            $table->string('headquarter')->nullable()->after('designation');
            $table->date('emp_doj')->nullable()->after('headquarter');
            $table->string('mobile')->nullable()->after('emp_doj');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
