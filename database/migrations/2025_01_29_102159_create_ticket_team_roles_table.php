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
        Schema::create('ticket_team_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TicketTeam::class);
            $table->string('role_id', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_team_roles');
    }
};
