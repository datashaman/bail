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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('content_type')->default('text/plain');
            $table->vector('embedding', 1536); // Dimensionality; 1536 for OpenAI's model
            $table->string('hash');
            $table->jsonb('meta')->nullable();
            $table->timestamps();
        });

        // This is a Postgres-specific index that allows us to do fast nearest-neighbor searches
        // when there are a lot of high-dimensional embeddings in the database.
        DB::statement('CREATE INDEX embeddings_index ON documents USING ivfflat (embedding vector_cosine_ops) WITH (lists = 100)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
