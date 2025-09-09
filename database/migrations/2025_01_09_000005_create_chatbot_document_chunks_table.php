<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_document_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('chatbot_documents')->cascadeOnDelete();
            $table->text('content');
            $table->json('embedding')->nullable();
            $table->integer('chunk_index');
            $table->integer('tokens_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['document_id', 'chunk_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_document_chunks');
    }
};