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
        Schema::create('extended_translation_changelog_title_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('locale', 16);
            $table->string('title', 50);
            $table->timestamps();

            $table->unique('locale', 'ext_trans_chglog_title_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_translation_changelog_title_translations');
    }
};
