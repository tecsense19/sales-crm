<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smtp_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // e.g. "Brevo", "SMTP2GO", "Twilio SendGrid"
            $table->string('driver')->default('smtp');      // smtp | sendgrid | mailgun
            $table->string('host')->nullable();
            $table->integer('port')->default(587);
            $table->string('encryption')->default('tls');
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->string('from_email');
            $table->string('from_name');
            $table->text('api_key')->nullable();          // For API-based providers (Twilio/SendGrid)
            $table->integer('daily_limit')->default(300);   // Max emails per day
            $table->integer('sent_today')->default(0);      // Counter reset daily
            $table->date('limit_reset_date')->nullable();   // Track which day counter was last reset
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1);        // 1 = highest (try first)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smtp_providers');
    }
};
