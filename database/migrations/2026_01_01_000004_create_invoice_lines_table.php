<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->restrictOnDelete();
            $table->string('description');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_amount_minor_units');
            $table->char('currency', 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};
