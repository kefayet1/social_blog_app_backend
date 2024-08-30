<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string("profile_image", 2048)->nullable();
            $table->string("website_url", 100)->nullable();
            $table->string("location", 100)->nullable();
            $table->string("bio", 100)->nullable();
            $table->string("work", 100)->nullable();
            $table->string('education', 100)->nullable();
            $table->foreignId("user_id")->references("id")->on("users")
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
