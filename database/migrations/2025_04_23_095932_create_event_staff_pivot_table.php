<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_staff_pivot', function (Blueprint $table) {
            // $table->id(); もし単一の主キーが必要な場合
            // $table->timestamps(); 通常ピボットテーブルには不要
            $table->primary(['event_id', 'user_id']); // event_id と user_id の組み合わせでユニークにする (複合主キーの代わり)


            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onDelete('cascade'); // イベントが削除されたら関連も削除

            // usersテーブルのidを参照する外部キー
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // ユーザーが削除されたら関連も削除


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_staff_pivot');
    }
};
