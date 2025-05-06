<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
        });

        // Update existing registrations to link them to courses based on course_level
        DB::table('registrations')
            ->join('courses', 'registrations.course_level', '=', 'courses.level')
            ->update(['registrations.course_id' => DB::raw('courses.id')]);

        // Make course_id required
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }
};
