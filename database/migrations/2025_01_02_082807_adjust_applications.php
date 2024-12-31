<?php

use App\Models\Application;
use App\Models\ApplicationSubmission;
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
        Schema::rename('applications', 'application_submissions');

        Schema::table('application_submissions', function (Blueprint $table) {
            $table->string('message_id', 20)->nullable();
            $table->string('channel_id', 20)->nullable();
            $table->string('handled_by', 20)->nullable();
            $table->foreignIdFor(Application::class)->nullable();
        });

        Schema::table('application_questions', function (Blueprint $table) {
            $table->foreignIdFor(Application::class);
            $table->softDeletes();
        });

        Schema::table('application_responses', function (Blueprint $table) {
            $table->foreignIdFor(Application::class);
            $table->softDeletes();
        });

        Schema::table('application_question_answers', function (Blueprint $table) {
            $table->dropColumn(['application_id']);
            $table->foreignIdFor(ApplicationSubmission::class);
            $table->text('answer')->change();
            $table->text('attachments')->nullable();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id', 20);
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->string('log_channel', 20);
            $table->text('accept_message');
            $table->text('deny_message');
            $table->text('confirmation_message');
            $table->text('completion_message');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('application_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Application::class);
            $table->string('role_id', 20);
            $table->integer('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_submissions', function (Blueprint $table) {
            $table->dropColumn(['message_id', 'channel_id', 'handled_by', 'application_id']);
        });

        Schema::table('application_question_answers', function (Blueprint $table) {
            $table->dropColumn(['application_submission_id']);
            $table->foreignIdFor(Application::class);
            $table->string('answer')->change();
        });

        Schema::table('application_questions', function (Blueprint $table) {
            $table->dropColumn(['application_id']);
            $table->dropSoftDeletes();
        });

        Schema::table('application_responses', function (Blueprint $table) {
            $table->dropColumn(['application_id']);
            $table->dropSoftDeletes();
        });

        Schema::dropIfExists('applications');

        Schema::dropIfExists('application_roles');

        Schema::rename('application_submissions', 'applications');
    }
};
