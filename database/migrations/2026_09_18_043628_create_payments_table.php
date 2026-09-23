<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['transferencia', 'contra_entrega', 'tarjeta_online']);
            $table->enum('status', ['pendiente', 'verificado', 'pagado', 'fallido', 'reembolsado'])->default('pendiente');
            $table->decimal('amount', 10, 2);
            $table->string('proof_path')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
