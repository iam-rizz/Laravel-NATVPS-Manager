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
        Schema::create('domain_forwardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nat_vps_id')->constrained('nat_vps')->onDelete('cascade');
            $table->integer('virtualizor_record_id')->nullable();
            $table->string('domain');
            $table->enum('protocol', ['http', 'https'])->default('http');
            $table->integer('source_port');
            $table->integer('destination_port');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_forwardings');
    }
};
