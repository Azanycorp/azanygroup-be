<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inboxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('job_title')->nullable();
            $table->string('email');
            $table->string('company_name')->nullable();
            $table->string('category');
            $table->string('status')->default('new');
            $table->string('role')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('topic')->nullable();
            $table->string('reason')->nullable();
            $table->string('social_media_url')->nullable();
            $table->string('site_url')->nullable();
            $table->string('resume_url')->nullable();
            $table->longText('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inboxes');
    }
};
