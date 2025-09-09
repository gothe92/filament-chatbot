<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_resources', function (Blueprint $table) {
            $table->id();
            $table->morphs('resourceable');
            $table->foreignId('chatbot_setting_id')->nullable()->constrained('chatbot_settings')->nullOnDelete();
            $table->string('rag_mode')->default('documents_and_ai');
            $table->boolean('active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['resourceable_type', 'resourceable_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_resources');
    }
};
