<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tournaments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('name');
            $table->string('img')->nullable();
            $table->text('desc')->nullable();
            $table->string('twitch')->nullable();
            $table->string('game');
            $table->string('type');
            $table->json('wins')->nullable();
            $table->json('teams')->nullable();
            $table->string('status')->enum(['create', 'pending', 'open', 'balance', 'process', 'end', 'arhive']);
            $table->json('history')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('tournaments_players', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tournament_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('nickname');
            $table->json('roles');
            $table->string('mmr');
            $table->string('priority');
            $table->string('team')->nullable();
            $table->string('profileId');
            $table->string('accountId');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tournament_id')->references('id')->on('tournaments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
