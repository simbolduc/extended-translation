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
        Schema::create('extended_translation_wiki_category_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('wiki_category_id');
            $table->string('locale', 16);
            $table->string('name', 50);
            $table->timestamps();

            $table->unique(['wiki_category_id', 'locale'], 'ext_trans_wiki_cat_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_translation_wiki_category_translations');
    }
};
