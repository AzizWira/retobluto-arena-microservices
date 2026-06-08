<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('status');
            $table->index('name');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
