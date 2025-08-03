<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('campaign_deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, queued, completed, failed, partial
            $table->timestamp('deployed_at');
            $table->json('metadata')->nullable(); // Store deployment details, results, errors
            $table->timestamps();

            $table->index(['campaign_id', 'deployed_at']);
            $table->index(['status', 'deployed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaign_deployments');
    }
};
