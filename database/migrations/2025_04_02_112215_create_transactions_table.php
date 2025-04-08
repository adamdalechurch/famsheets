<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_group_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('total', 10, 2);
            $table->boolean('is_income')->default(false);
            $table->boolean('recurring')->default(false);
            // $table->foreignId('transaction_schedule_id')->nullable()->constrained('transaction_schedule')->onDelete('cascade');

            // Require foreign key to income_sources when is_income is true
            $table->foreignId('income_source_id')->nullable()->constrained('income_sources')->onDelete('cascade');

            $table->timestamp('transaction_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
