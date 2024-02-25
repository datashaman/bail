<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        PostgresGrammar::macro('typeTsvector', function () {
            return "tsvector";
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->addColumn('tsvector', 'tsv');
            $table->vector('embedding', 1536); // Dimensionality; 1536 for OpenAI's model
            $table->string('hash')->unique();
            $table->jsonb('meta')->nullable();
            $table->timestamps();
        });

        // This allows us to do fast nearest-neighbor searches when
        // there are a lot of high-dimensional embeddings in the database.
        DB::statement('CREATE INDEX embeddings_index ON documents USING ivfflat (embedding vector_cosine_ops) WITH (lists = 100)');

        // This allows us to do fast full-text searches.
        // DB::statement('ALTER TABLE documents ADD COLUMN tsv tsvector');
        DB::statement('CREATE INDEX documents_tsv_index ON documents USING GIN(tsv)');

        DB::statement("
            CREATE TRIGGER tsvectorupdate
            BEFORE INSERT OR UPDATE ON documents
            FOR EACH ROW EXECUTE FUNCTION tsvector_update_trigger(tsv, 'pg_catalog.english', content)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER tsvectorupdate ON documents');

        Schema::dropIfExists('documents');
    }
};
