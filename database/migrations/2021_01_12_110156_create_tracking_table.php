<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

class CreateTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('product_id')->nullable();
            $table->text('description')->nullable(false);
            $table->dateTimeTz('date_from')->nullable(false)->default(DB::raw('NOW()'));
            $table->dateTimeTz('date_to')->nullable();
            $table->enum('status', ['started', 'paused', 'stopped'])->nullable(false);
            $table->boolean('billable')->default(false);
            $table->boolean('billed')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking');
    }
}
