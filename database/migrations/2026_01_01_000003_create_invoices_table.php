<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('number')->unique();
            $table->char('currency', 3);
            $table->unsignedBigInteger('subtotal_minor_units');
            $table->string('status', 20)->default('draft');
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
