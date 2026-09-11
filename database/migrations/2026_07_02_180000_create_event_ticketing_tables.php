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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('venue');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->integer('capacity');
            $table->string('ticket_template_path')->nullable();
            $table->string('status')->default('draft'); // draft | published | closed
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->json('custom_fields')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'email']);
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendee_id')->constrained('attendees')->onDelete('cascade');
            $table->string('token')->unique();
            $table->string('qr_image_path')->nullable();
            $table->string('ticket_image_path')->nullable();
            $table->string('status')->default('issued'); // issued | revoked
            $table->timestamps();
        });

        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->onDelete('cascade');
            $table->foreignId('scanned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('scanned_at')->useCurrent();
            $table->string('device_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_ins');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('attendees');
        Schema::dropIfExists('events');
    }
};
