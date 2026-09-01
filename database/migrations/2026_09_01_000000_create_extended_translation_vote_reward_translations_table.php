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
        Schema::create('extended_translation_vote_reward_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vote_reward_id');
            $table->string('locale', 16);
            $table->string('name', 50);
            $table->timestamps();

            $table->unique(['vote_reward_id', 'locale'], 'ext_trans_vote_reward_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_translation_vote_reward_translations');
    }
};
