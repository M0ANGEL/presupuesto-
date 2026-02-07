<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->decimal('amount', 15, 2);
             $table->text('description')->nullable();

            $table->enum('from', ['stock', 'personal', 'saving'])->nullable();
            $table->enum('to', ['stock', 'personal', 'saving'])->nullable();

            $table->string('reference_type')->nullable(); // Income, Expense, FixedExpense
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
