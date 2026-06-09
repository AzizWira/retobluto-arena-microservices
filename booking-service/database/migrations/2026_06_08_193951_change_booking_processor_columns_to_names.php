<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY approved_by VARCHAR(150) NULL");
        DB::statement("ALTER TABLE bookings MODIFY rejected_by VARCHAR(150) NULL");
        DB::statement("ALTER TABLE bookings MODIFY canceled_by VARCHAR(150) NULL");

        DB::statement("UPDATE bookings SET approved_by = 'Admin' WHERE approved_by REGEXP '^[0-9]+$'");
        DB::statement("UPDATE bookings SET rejected_by = 'Admin' WHERE rejected_by REGEXP '^[0-9]+$'");
        DB::statement("UPDATE bookings SET canceled_by = 'Member' WHERE canceled_by REGEXP '^[0-9]+$'");
    }

    public function down(): void
    {
        DB::statement("UPDATE bookings SET approved_by = NULL");
        DB::statement("UPDATE bookings SET rejected_by = NULL");
        DB::statement("UPDATE bookings SET canceled_by = NULL");

        DB::statement("ALTER TABLE bookings MODIFY approved_by BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE bookings MODIFY rejected_by BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE bookings MODIFY canceled_by BIGINT UNSIGNED NULL");
    }
};
