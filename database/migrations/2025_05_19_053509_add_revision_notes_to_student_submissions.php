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
        Schema::table('student_submissions', function (Blueprint $table) {
            $table->text('revision_notes')->nullable()->after('status');
            $table->timestamp('status_updated_at')->nullable()->after('revision_notes');
            $table->foreignId('status_updated_by')->nullable()->constrained('users')->after('status_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_submissions', function (Blueprint $table) {
            $table->dropForeign(['status_updated_by']);
            $table->dropColumn(['revision_notes', 'status_updated_at', 'status_updated_by']);
        });
    }
};
