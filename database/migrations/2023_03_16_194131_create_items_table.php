<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable(false);
            $table->uuid('folder_id')->nullable(true);
            $table->uuid('organization_id')->nullable(true);
            $table->string('name')->nullable();
            $table->integer('type')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('favorite')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
            ->on('users')
            ->references('id')
            ->onDelete('cascade');

            $table->foreign('folder_id')
            ->on('folders')
            ->references('id')
            ->onDelete('cascade');

            $table->foreign('organization_id')
            ->on('organizations')
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
        Schema::dropIfExists('items');
    }
}
