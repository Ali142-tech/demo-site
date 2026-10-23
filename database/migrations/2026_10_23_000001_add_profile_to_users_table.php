<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('password');
            $table->string('account_type')->default('individual')->after('role');
            $table->string('organization')->nullable()->after('account_type');
            $table->string('phone')->nullable()->after('organization');
            $table->string('city')->nullable()->after('phone');
            $table->text('address')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'account_type', 'organization', 'phone', 'city', 'address']);
        });
    }
};