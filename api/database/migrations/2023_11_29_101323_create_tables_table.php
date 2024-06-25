<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('capacity');
            $table->boolean('status')->default(1 );
            $table->string('guests')->nullable();
            $table->integer('rest_of_place');
            $table->string('qr_code_path')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreignUuid('categorie_id')->constrained()->references('id')->on('categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignUuid('event_id')->constrained()->references('id')->on('events')
                ->onUpdate('cascade')
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
        Schema::dropIfExists('tables');
    }
};
