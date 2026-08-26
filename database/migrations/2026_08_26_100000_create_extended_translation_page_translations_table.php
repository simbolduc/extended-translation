<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extended_translation_page_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('page_id');
            $table->string('locale', 16);
            $table->string('title');
            $table->string('description');
            $table->text('content');
            $table->timestamps();

            $table->unique(['page_id', 'locale']);
            $table->foreign('page_id')->references('id')->on('pages')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_translation_page_translations');
    }
};
