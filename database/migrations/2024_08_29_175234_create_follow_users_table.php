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
        Schema::create('follow_users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_follow')->default(false);
            $table->foreignId("user_id")->references('id')->on('users')
                    ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("following_user_id")->references('id')->on('users')
            ->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_users');
    }
};
