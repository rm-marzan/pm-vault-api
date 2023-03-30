<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('item_id')->nullable(false);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->json('urls')->nullable();
            $table->timestamps();

            $table->foreign('item_id')
            ->on('items')
            ->references('id')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logins');
    }
}
