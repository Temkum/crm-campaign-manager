<?php

use App\Enums\WebsiteTypeEnum;
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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('url')->index();
            $table->string('api_url')->nullable()->index();
            $table->unsignedTinyInteger('type')->index();
            $table->string('auth_type')->default('NONE');
            $table->string('auth_token')->nullable()->index();
            $table->string('auth_user')->nullable()->index();
            $table->string('auth_pass')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
