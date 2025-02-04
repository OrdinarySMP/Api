<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    private $operations = [
        'create',
        'read',
        'update',
        'delete',
    ];

    private $models = [
        'ticketConfig',
        'ticket',
        'ticketPanel',
        'ticketTeam',
        'ticketTranscript',
        'ticketButton',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->models as $model) {
            foreach ($this->operations as $operation) {
                Permission::create(['name' => $model.'.'.$operation]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->operations as $operation) {
            foreach ($this->models as $model) {
                Permission::where(['name' => $model.'.'.$operation])->delete();
            }
        }
    }
};
