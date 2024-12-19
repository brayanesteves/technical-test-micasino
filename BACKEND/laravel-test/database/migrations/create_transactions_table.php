<?php
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        public function up()
        {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->string('payment_system'); // easy_money | super_walletz
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3);
                $table->string('status')->default('pending'); // pending, success, failed
                $table->string('transaction_id')->nullable();
                $table->timestamps();
            });
    
            Schema::create('request_logs', function (Blueprint $table) {
                $table->id();
                $table->string('endpoint');
                $table->text('request_body');
                $table->text('response_body')->nullable();
                $table->timestamps();
            });
        }
    
        public function down()
        {
            Schema::dropIfExists('transactions');
            Schema::dropIfExists('request_logs');
        }
};