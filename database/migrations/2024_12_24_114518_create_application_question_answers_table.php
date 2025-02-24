<?php

use App\Models\Application;
use App\Models\ApplicationQuestion;
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
        Schema::create('application_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ApplicationQuestion::class);
            $table->foreignIdFor(Application::class);
            $table->string('answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_question_answers');
    }
};
