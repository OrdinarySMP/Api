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
        Schema::create('ticket_panels', function (Blueprint $table) {
            $table->id();
            $table->string('titel', 100);
            $table->string('message', 1000);
            $table->string('embed_color', 7);
            $table->string('channel_id', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_panels');
    }
};
