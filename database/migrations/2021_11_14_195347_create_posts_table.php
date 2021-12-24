<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('desc');
            $table->json('tags')->nullable();
            $table->string('banner')->nullable();
            $table->string('display_image')->nullable();
            $table->foreignId('categories_id')->constrained('categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->integer('views')->nullable();
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
        Schema::dropIfExists('posts');
    }
}
