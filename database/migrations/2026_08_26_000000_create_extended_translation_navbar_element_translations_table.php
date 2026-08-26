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
        Schema::create('extended_translation_navbar_element_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('navbar_element_id');
            $table->string('locale', 16);
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['navbar_element_id', 'locale'], 'ext_trans_navbar_element_locale_unique');
            $table->foreign('navbar_element_id', 'ext_trans_navbar_element_id_foreign')
                ->references('id')
                ->on('navbar_elements')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_translation_navbar_element_translations');
    }
};
