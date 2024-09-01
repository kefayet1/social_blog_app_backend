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
        Schema::create('table_notifications', function (Blueprint $table) {
            $table->id();
            $table->string("text", 300)->nullable();
            $table->boolean("is_seen")->default(false);
            $table->enum("type", ['post', 'tag_post', 'following', 'comment']);
            $table->foreignId("user_id")->references("id")->on("users")
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId("actor_id")->references("id")->on("users")
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("tag_id")->nullable()->references("id")->on("tags")
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("post_id")->nullable()->references("id")->on("posts")
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_notifications');
    }
};
