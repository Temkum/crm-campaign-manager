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
        Schema::table('campaign_triggers', function (Blueprint $table) {
            // Remove old campaign_id foreign key if it exists
            $table->dropForeign(['campaign_id']);
            $table->dropColumn('campaign_id');

            // Add new foreign key to campaign_trigger_groups
            $table->foreignId('campaign_trigger_group_id')->constrained()->onDelete('cascade');

            // Add new fields
            $table->text('description')->nullable()->after('value');
            $table->integer('order_index')->default(0)->after('description');

            // Update type enum to match the new trigger types
            $table->dropColumn('type');
        });

        Schema::table('campaign_triggers', function (Blueprint $table) {
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
            ])->after('campaign_trigger_group_id');

            // Update operator enum to match the new operators
            $table->dropColumn('operator');
        });

        Schema::table('campaign_triggers', function (Blueprint $table) {
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
            ])->after('type');

            $table->index(['campaign_trigger_group_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_triggers', function (Blueprint $table) {
            // Drop new foreign key
            $table->dropForeign(['campaign_trigger_group_id']);
            $table->dropColumn(['campaign_trigger_group_id', 'description', 'order_index']);

            // Restore old campaign_id foreign key
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');

            // Restore old enum values (you may need to adjust these based on your original schema)
            $table->dropColumn(['type', 'operator']);
        });

        Schema::table('campaign_triggers', function (Blueprint $table) {
            $table->enum('type', ['time', 'scroll', 'click', 'exit_intent', 'page_load'])->after('campaign_id');
            $table->enum('operator', ['equals', 'greater_than', 'less_than', 'contains'])->after('type');
        });
    }
};
