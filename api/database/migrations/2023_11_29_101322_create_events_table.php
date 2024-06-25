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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('location');
            $table->date('date_start');
            $table->string('status')->default('En attente');
            $table->string('city');
            $table->integer('number_of_space');
            $table->integer('rest_of_space');
            $table->string('number_of_table');
            $table->string('rest_of_table');
            $table->string('neighborhood');
            $table->time('time');
            $table->softDeletes();
            // $table->uuid('owner_id');
            // $table->uuid('event_type_id');
            $table->timestamps();
            $table->foreignUuid('owner_id')->constrained()->references('id')->on('owners')
                ->onUpdate('cascade')
                ->onDelete('cascade')
            ;
            $table->foreignUuid('event_type_id')->constrained()->references('id')->on('event_types')
                ->onUpdate('cascade')
                ->onDelete('cascade')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
