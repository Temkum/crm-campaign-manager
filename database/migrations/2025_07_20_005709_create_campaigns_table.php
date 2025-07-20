<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->index();
            $table->foreignId('operator_id')
                  ->constrained('operators')
                  ->onDelete('cascade')
                  ->name('campaigns_operator_id_foreign')
                  ->index();
            $table->foreignId('market_id')
                  ->constrained('markets')
                  ->onDelete('cascade')
                  ->name('campaigns_market_id_foreign')
                  ->index();
            $table->timestamp('start_at')->nullable()->index();
            $table->timestamp('end_at')->nullable()->index();
            $table->enum('status', ['active', 'inactive', 'paused', 'pending'])->default('inactive')->index();
            $table->unsignedInteger('priority')->default(0)->index();
            $table->unsignedInteger('duration')->default(0)->index();
            $table->unsignedInteger('rotation_delay')->default(0)->index();
            $table->string('dom_selector', 255)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};