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
        Schema::create('ticket_configs', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id', 20)->unique();
            $table->string('category_id', 20);
            $table->string('transcript_channel_id', 20);
            $table->string('create_channel_id', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_configs');
    }
};
