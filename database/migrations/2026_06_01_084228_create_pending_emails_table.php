<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->string('recipient_email');
            $table->string('recipient_name')->default('Recipient');
            $table->string('recipient_location')->nullable();
            $table->string('recipient_type')->default('crm'); // crm | external
            $table->string('status')->default('pending');     // pending | sent | failed
            $table->integer('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_emails');
    }
};
