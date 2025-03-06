<?php

use App\Models\TicketButton;
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
        Schema::create('ticket_button_ping_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TicketButton::class);
            $table->string('role_id', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_button_ping_roles');
    }
};
