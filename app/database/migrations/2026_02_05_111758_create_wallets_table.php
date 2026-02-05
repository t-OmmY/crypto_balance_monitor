<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('address');
            $table->string('currency', 3);
            $table->bigInteger('last_balance')->default(0);
            $table->timestamp('last_balance_changed_at')->nullable();
            $table->timestamps();

            $table->unique(['address', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
