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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('x')->nullable()->after('youtube');
            $table->longText('source_url')->nullable()->after('x');
            $table->date('next_followup_date')->nullable()->after('follow_up_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('x');
            $table->dropColumn('source_url');
            $table->dropColumn('next_followup_date');
        });
    }
};
