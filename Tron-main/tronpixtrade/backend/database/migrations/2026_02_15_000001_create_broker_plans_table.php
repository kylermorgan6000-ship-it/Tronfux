<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('broker_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('min_investment', 18, 2);
            $table->decimal('max_investment', 18, 2);
            $table->decimal('roi_percentage', 5, 2);
            $table->integer('duration_days');
            $table->string('status')->default('active'); // active/inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broker_plans');
    }
};