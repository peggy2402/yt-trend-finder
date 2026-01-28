<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Loại gói hiện tại (mặc định là free)
            $table->string('plan_type')->default('free');

            // Ngày hết hạn gói (null = vĩnh viễn hoặc chưa kích hoạt)
            // Đã có vip_expires_at trong code cũ, ta tái sử dụng hoặc thêm mới
            // $table->timestamp('plan_expires_at')->nullable();

            // Số lượt đã dùng trong ngày (Reset mỗi 00:00)
            $table->integer('daily_usage_count')->default(0);

            // Ngày ghi nhận usage lần cuối để reset
            $table->date('last_usage_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan_type', 'daily_usage_count', 'last_usage_date']);
        });
    }
};
