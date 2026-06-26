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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            // Expense Fields
            $table->string('description'); // Description of the expense
            $table->decimal('amount', 10, 2); // Amount in RM (10 digits total, 2 decimal places)
            $table->date('date'); // Date of the expense
            $table->text('notes')->nullable(); // Optional notes about the expense

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'date']);
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
