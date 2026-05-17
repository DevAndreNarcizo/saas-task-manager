<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->unique('stripe_customer_id', 'organizations_stripe_customer_id_unique');
            $table->unique('stripe_subscription_id', 'organizations_stripe_subscription_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropUnique('organizations_stripe_customer_id_unique');
            $table->dropUnique('organizations_stripe_subscription_id_unique');
        });
    }
};
