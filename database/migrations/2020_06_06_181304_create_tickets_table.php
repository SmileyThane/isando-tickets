<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from_entity_id');
            $table->string('from_entity_type');
            $table->bigInteger('to_entity_id');
            $table->string('to_entity_type');
            $table->bigInteger('from_company_user_id')->nullable();
            $table->bigInteger('contact_company_user_id')->nullable();
            $table->bigInteger('to_company_user_id')->nullable();
            $table->bigInteger('to_team_id')->nullable();
            $table->bigInteger('to_product_id')->nullable();
            $table->bigInteger('priority_id')->default(1);
            $table->bigInteger('status_id')->default(1);
            $table->dateTime('due_date')->nullable();
            $table->string('name', 400);
            $table->text('description')->nullable();
            $table->text('connection_details')->nullable();
            $table->text('access_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
