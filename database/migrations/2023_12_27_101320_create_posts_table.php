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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->comment('作者 ID');
            $table->string('title')->comment('标题');
            $table->string('slug');
            $table->text('content')->comment('内容');
            $table->unsignedInteger('category_id')->comment('分类 ID');
            $table->string('image')->nullable()->comment('封面地址');
            $table->string('status')->comment('状态');
            $table->dateTime('published_at')->nullable()->comment('发布时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
