<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaction_schedule', function (Blueprint $table) {
            $table->id();
            $table->enum('frequency', ['daily', 'weekly', 'bi-weekly', 'semi-monthly', 'monthly', 'semi-annually', 'yearly']);
            $table->timestamp('next_due_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_schedule');
    }
};
