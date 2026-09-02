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
        Schema::create('extended_translation_shop_category_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_category_id');
            $table->string('locale', 16);
            $table->string('name', 50);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['shop_category_id', 'locale'], 'ext_trans_shop_cat_locale_unique');
        });

        Schema::create('extended_translation_shop_package_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_package_id');
            $table->string('locale', 16);
            $table->string('name', 50);
            $table->string('short_description', 255);
            $table->text('description');
            $table->timestamps();

            $table->unique(['shop_package_id', 'locale'], 'ext_trans_shop_pkg_locale_unique');
        });

        Schema::create('extended_translation_shop_offer_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_offer_id');
            $table->string('locale', 16);
            $table->string('name', 50);
            $table->timestamps();

            $table->unique(['shop_offer_id', 'locale'], 'ext_trans_shop_offer_locale_unique');
        });

        Schema::create('extended_translation_shop_variable_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_variable_id');
            $table->string('locale', 16);
            $table->string('description', 200);
            $table->text('options')->nullable();
            $table->timestamps();

            $table->unique(['shop_variable_id', 'locale'], 'ext_trans_shop_var_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_translation_shop_category_translations');
        Schema::dropIfExists('extended_translation_shop_package_translations');
        Schema::dropIfExists('extended_translation_shop_offer_translations');
        Schema::dropIfExists('extended_translation_shop_variable_translations');
    }
};
