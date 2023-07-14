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
        Schema::create('datasetbooks', function (Blueprint $table) {
            $table->id();
            $table->string('bestsellers_rank')->nullable();
            $table->foreignId('format_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('book_id')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_url')->nullable();
            $table->string('isbn10')->nullable();
            $table->string('isbn13')->nullable();
            $table->string('lang')->nullable();
            $table->dateTime('publication_date')->nullable();
            $table->float('rating_avg')->nullable();
            $table->integer('rating_count')->nullable();
            $table->string('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dataset');
    }
};
