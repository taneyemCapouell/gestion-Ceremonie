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
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('firstname');
            $table->string('lastname')->nullable(1);
            $table->string('phone', '20');
            $table->string('gender');
            $table->string('email')->unique();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->foreignUuid('table_id')->constrained()->references('id')->on('tables')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignUuid('place_id')->constrained()->references('id')->on('places')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignUuid('event_id')->constrained()->references('id')->on('events')
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
        Schema::dropIfExists('guests');
    }
};
