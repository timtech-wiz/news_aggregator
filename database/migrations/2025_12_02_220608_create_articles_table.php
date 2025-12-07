<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('source_api', 50)->index();
            $table->string('source_name', 100);
            $table->string('author', 255)->nullable();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('url', 500)->unique();
            $table->text('url_to_image')->nullable();
            $table->text('content')->nullable();
            $table->timestamp('published_at')->index();
            $table->string('category', 50)->nullable()->index();
            $table->string('language', 10)->default('en')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['source_api', 'published_at']);
            $table->index(['category', 'published_at']);
        });

        DB::statement('ALTER TABLE articles ADD FULLTEXT search(title, description, content)');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
