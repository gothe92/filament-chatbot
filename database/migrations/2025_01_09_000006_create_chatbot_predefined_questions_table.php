<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_predefined_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatbot_resource_id')->constrained('chatbot_resources')->cascadeOnDelete();
            $table->text('question');
            $table->text('answer');
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['chatbot_resource_id', 'active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_predefined_questions');
    }
};
