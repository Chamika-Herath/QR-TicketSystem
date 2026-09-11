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
        Schema::table('events', function (Blueprint $table) {
            $table->json('ticket_types')->nullable()->after('ticket_template_path');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('ticket_type')->default('General Admission')->after('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('ticket_types');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('ticket_type');
        });
    }
};
