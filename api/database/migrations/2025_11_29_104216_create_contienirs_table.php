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
        Schema::create('contienirs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->softDeletes();
            // $table->uuid('event_id');
            // $table->uuid('guest_id');
            $table->timestamps();
            $table->foreignUuid('event_id')->constrained()->references('id')->on('events')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignUuid('guest_id')->constrained()->references('id')->on('guests')
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
        Schema::dropIfExists('contienirs');
    }
};
