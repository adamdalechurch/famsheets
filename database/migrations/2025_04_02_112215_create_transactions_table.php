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
            $table->boolean('is_income')->default(false);
            $table->boolean('recurring')->default(false);
            $table->decimal('amount', 15, 2);
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');

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
