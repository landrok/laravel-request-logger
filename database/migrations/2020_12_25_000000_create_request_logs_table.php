<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tablename = config('requestlogger.tablename');
        $connection = config('requestlogger.connection');

        Schema::connection($connection)->create($tablename, function (Blueprint $table) {
            $table->bigIncrements('id');

            // User
            $table->string('session_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('ip')->nullable();
            $table->string('route')->nullable();
            $table->text('route_params')->nullable();

            // Performances__
            $table->decimal('duration', 16, 8)->nullable();
            $table->unsignedInteger('mem_alloc')->nullable();

            // HTTP
            $table->string('method')->nullable();
            $table->string('status_code')->nullable();
            $table->string('url')->nullable();
            $table->string('referer')->nullable();
            $table->string('referer_host')->nullable();
            $table->text('request_headers')->nullable();
            $table->text('response_headers')->nullable();

            // Device
            $table->string('device')->nullable();
            $table->string('os')->nullable();
            $table->string('os_version')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();

            $table->boolean('is_desktop')->nullable();
            $table->boolean('is_tablet')->nullable();
            $table->boolean('is_mobile')->nullable();
            $table->boolean('is_phone')->nullable();
            $table->boolean('is_robot')->nullable();
            $table->string('robot_name')->nullable();
            $table->string('user_agent')->nullable();

            // Miscellaneous
            $table->text('meta')->nullable();
            $table->dateTime('created_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tablename  = config('requestlogger.tablename');
        $connection = config('requestlogger.connection');

        Schema::connection($connection)->dropIfExists($tablename);
    }
}
