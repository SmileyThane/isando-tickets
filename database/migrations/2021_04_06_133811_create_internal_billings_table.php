<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_billings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type');
            $table->unsignedInteger('cost')->nullable();
            $table->bigInteger('currency_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internal_billings');
    }
}
