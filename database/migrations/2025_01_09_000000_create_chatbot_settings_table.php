<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->string('personality')->default('helpful');
            $table->string('tone')->default('professional');
            $table->string('expertise_level')->default('intermediate');
            $table->integer('response_length')->default(200);
            $table->decimal('temperature', 3, 2)->default(0.7);
            $table->json('forbidden_topics')->nullable();
            $table->json('avoided_expressions')->nullable();
            $table->text('behavioral_rules')->nullable();
            $table->text('custom_instructions')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['active', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_settings');
    }
};