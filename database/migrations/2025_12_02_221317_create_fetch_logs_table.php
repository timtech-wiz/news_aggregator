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
        Schema::create('fetch_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source_api', 50)->index();
            $table->integer('articles_fetched')->default(0);
            $table->integer('articles_saved')->default(0);
            $table->integer('duplicates')->default(0);
            $table->string('status', 20)->index();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fetch_logs');
    }
};
