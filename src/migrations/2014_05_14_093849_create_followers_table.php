<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFollowersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('follower_id')->index();
            $table->string('follower_type')->index();
            $table->integer('followed_id')->index();
            $table->string('followed_type')->index();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('followers');
    }

}
