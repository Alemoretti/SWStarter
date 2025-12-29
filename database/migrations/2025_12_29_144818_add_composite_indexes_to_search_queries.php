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
        Schema::table('search_queries', function (Blueprint $table) {
            $table->index(['type', 'created_at'], 'search_queries_type_created_at_index');
            $table->index(['query', 'type'], 'search_queries_query_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search_queries', function (Blueprint $table) {
            $table->dropIndex('search_queries_type_created_at_index');
            $table->dropIndex('search_queries_query_type_index');
        });
    }
};
