<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatbot_resource_id')->constrained('chatbot_resources')->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->json('embedding')->nullable();
            $table->integer('chunks_count')->default(0);
            $table->integer('tokens_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('chatbot_resource_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_documents');
    }
};
