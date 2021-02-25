<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serviceable', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id')->nullable(false);
            $table->unsignedBigInteger('serviceable_id')->nullable(false);
            $table->string('serviceable_type')->nullable(false);
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
        Schema::dropIfExists('serviceable');
    }
}
