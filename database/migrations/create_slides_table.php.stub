<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlidesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_video');
            $table->string('video_path')->nullable();
            $table->string('slide_text')->nullable();
            $table->string('action_text')->nullable();
            $table->string('action_link')->nullable();
            $table->string('text_colour')->nullable();
            $table->boolean('published')->default(0);
            $table->unsignedInteger('position')->default(999);
            $table->nullableTimestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
