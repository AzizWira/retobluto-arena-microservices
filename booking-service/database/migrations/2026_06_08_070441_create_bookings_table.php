<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('member_user_id');
            $table->string('member_name', 100);
            $table->string('member_email', 150);

            $table->unsignedBigInteger('field_id');
            $table->string('field_name', 150);
            $table->string('field_type', 100);

            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->unsignedInteger('duration_hours');
            $table->decimal('price_per_hour', 12, 2);
            $table->decimal('total_price', 12, 2);

            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->unsignedBigInteger('canceled_by')->nullable();

            $table->timestamps();

            $table->index('member_id');
            $table->index('member_user_id');
            $table->index('field_id');
            $table->index('booking_date');
            $table->index('status');
            $table->index(['field_id', 'booking_date', 'status']);
            $table->index(['member_id', 'booking_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
