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
        Schema::create('campaign_triggers', function (Blueprint $table) {
            $table->id();

            // New foreign key to campaign_trigger_groups
            $table->foreignId('campaign_trigger_group_id')->constrained()->onDelete('cascade');

            // Updated enum for type
            $table->enum('type', [
                'url',
                'referrer',
                'device',
                'country',
                'pageViews',
                'timeOnSite',
                'timeOnPage',
                'scroll',
                'exitIntent',
                'newVisitor',
                'dayOfWeek',
                'hour'
            ])->index();

            $table->string('value')->nullable()->index();

            // Updated enum for operator
            $table->enum('operator', [
                'equals',
                'contains',
                'starts_with',
                'ends_with',
                'regex',
                'gte',
                'lte',
                'between',
                'in',
                'not_in'
            ])->index();

            $table->text('description')->nullable()->index();
            $table->integer('order_index')->default(0)->index();

            $table->softDeletes();
            $table->timestamps();

            // Compound index for performance
            $table->index(['campaign_trigger_group_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_triggers');
    }
};
