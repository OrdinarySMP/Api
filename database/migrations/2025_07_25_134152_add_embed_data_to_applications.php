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
        Schema::table('applications', function (Blueprint $table) {
            $table->string('embed_channel_id', 20)->nullable();
            $table->string('embed_title', 100)->nullable();
            $table->string('embed_description', 1000)->nullable();
            $table->string('embed_color', 7)->nullable();
            $table->string('embed_button_text', 50)->nullable();
            $table->integer('embed_button_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('embed_channel_id');
            $table->dropColumn('embed_title');
            $table->dropColumn('embed_description');
            $table->dropColumn('embed_color');
            $table->dropColumn('embed_button_text');
            $table->dropColumn('embed_button_color');
        });
    }
};
