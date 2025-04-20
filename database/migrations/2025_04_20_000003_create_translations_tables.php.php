<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('translation_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('translation_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('translation_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('translation_key_tag', function (Blueprint $table) {
            $table->foreignId('translation_key_id')->constrained()->cascadeOnDelete();
            $table->foreignId('translation_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['translation_key_id', 'translation_tag_id']);
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_key_id')->constrained()->cascadeOnDelete();
            $table->foreignId('translation_group_id')->constrained()->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->text('value');
            $table->timestamps();

            $table->foreign('language_code')->references('code')->on('languages');
            $table->unique(['translation_key_id', 'translation_group_id', 'language_code'], 'trans_unique_key_group_lang');

            $table->index(['translation_key_id', 'translation_group_id', 'language_code'], 'trans_index_key_group_lang');
            $table->index(['language_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
        Schema::dropIfExists('translation_key_tag');
        Schema::dropIfExists('translation_tags');
        Schema::dropIfExists('translation_keys');
        Schema::dropIfExists('translation_groups');
        Schema::dropIfExists('languages');
    }
};