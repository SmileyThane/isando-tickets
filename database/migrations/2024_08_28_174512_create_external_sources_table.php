<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('external_sources', function (Blueprint $table) {
            $table->id();
            $table->string('domain');
            $table->string('domain_prefix');
            $table->string('uri')->default('/');
            $table->string('name');
            $table->string('auth_name');
            $table->string('auth_method')->default('basic');
            $table->json('auth_headers')->nullable();
            $table->string('password')->nullable();

            $table->integer('billing_price');
            $table->integer('billing_currency');
            $table->integer('billing_type');
            $table->boolean('is_billing_auto_renewal');
            $table->dateTime('last_billed_at');

            $table->unsignedBigInteger('external_source_type_id')->nullable();
            $table->foreign('external_source_type_id')
                ->references('id')
                ->on('external_source_types')
                ->nullOnDelete();

            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('external_sources');
    }
};
