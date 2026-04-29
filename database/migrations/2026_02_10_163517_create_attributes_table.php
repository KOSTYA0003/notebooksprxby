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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()
                ->constrained('attributes')->onDelete('set null');
            $table->string('name')->unique();
            $table->string('filter_type')->default('list');
            $table->boolean('is_visible')->default(true);
            $table->integer('pos_photo')->default(0); // To the right of the photo
            $table->integer('pos_sidebar')->default(0); // In the sidebar
            $table->integer('pos_modal')->default(0); // In the modal
            $table->foreignId('attribute_group_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
