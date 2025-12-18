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
        Schema::create('nat_vps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained('servers')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('vps_id'); // Virtualizor VPS ID
            $table->string('hostname')->nullable();
            $table->text('ssh_username')->nullable(); // encrypted
            $table->text('ssh_password')->nullable(); // encrypted
            $table->integer('ssh_port')->default(22);
            $table->json('cached_specs')->nullable();
            $table->timestamp('specs_cached_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nat_vps');
    }
};
