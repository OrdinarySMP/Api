<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Permission::create(['name' => 'ticket.read-own']);
        Permission::create(['name' => 'ticketTranscript.read-own']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where(['name' => 'ticket.read-own'])->delete();
        Permission::where(['name' => 'ticketTranscript.read-own'])->delete();
    }
};
