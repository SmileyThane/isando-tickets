<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketMergesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_merges', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_ticket_id');
            $table->bigInteger('child_ticket_id');
            $table->bigInteger('merged_by_user_id');
            $table->string('merge_comment')->nullable();
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
        Schema::dropIfExists('ticket_merges');
    }
}
