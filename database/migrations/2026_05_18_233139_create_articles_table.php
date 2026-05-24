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
    Schema::create('articles', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('slug')->unique();
      $table->longText('content');
      $table->enum('status', ['draft', 'published'])->default('draft');
      $table->date('project_at');
      $table->string('location');
      $table->timestamp('published_at')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('articles');
  }
};
