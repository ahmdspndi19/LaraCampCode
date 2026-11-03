<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menghapus penyimpanan nomor kartu penuh + CVC, menambah snapshot harga
 * (amount) dan waktu pelunasan (paid_at). Data lama dipertahankan:
 * 4 digit terakhir diambil dari card_number lama sebelum kolomnya dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->string('card_last_four', 4)->nullable()->after('camp_id');
            $table->unsignedInteger('amount')->default(0)->after('card_last_four');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
        });

        foreach (DB::table('checkouts')->get() as $row) {
            DB::table('checkouts')->where('id', $row->id)->update([
                'card_last_four' => substr((string) $row->card_number, -4),
                'amount' => (int) DB::table('camps')->where('id', $row->camp_id)->value('price'),
                'paid_at' => $row->payment_status === 'paid' ? $row->updated_at : null,
            ]);
        }

        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn(['card_number', 'cvc']);
        });
    }

    public function down(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->string('card_number')->default('');
            $table->string('cvc', 4)->default('');
        });

        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn(['card_last_four', 'amount', 'paid_at']);
        });
    }
};
