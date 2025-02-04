<?php

use App\Models\TicketTeam;
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
        Schema::create('ticket_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TicketTeam::class);
            $table->string('text', 50);
            $table->string('color', 7);
            $table->string('initial_message', 1000);
            $table->string('emoji');
            $table->string('naming_scheme');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_buttons');
    }
};
