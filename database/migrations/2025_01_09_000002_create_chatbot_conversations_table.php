<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatbot_resource_id')->constrained('chatbot_resources')->cascadeOnDelete();
            $table->string('session_id', 100);
            $table->string('language', 10)->default('hu');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['chatbot_resource_id', 'session_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_conversations');
    }
};
