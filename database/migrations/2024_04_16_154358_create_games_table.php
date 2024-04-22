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

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('status', ['queue', 'playing', 'finished'])->default('queue');
            $table->unsignedBigInteger('player1_id');
            $table->unsignedBigInteger('player2_id')->nullable();
            $table->unsignedBigInteger('winner_id')->nullable();

            $table->foreign('player1_id')->references('id')->on('users');
            $table->foreign('player2_id')->references('id')->on('users');
            $table->foreign('winner_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
};
