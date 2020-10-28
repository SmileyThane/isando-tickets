<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class LocalizeCustomTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = DB::table('companies')->pluck('id');

        DB::table('company_address_types')->truncate();
        Schema::table('company_address_types', static function (Blueprint $table) {
            $table->dropForeign(['address_type_id']);
            $table->dropColumn('address_type_id');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');

            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type');

            $table->string('name');
            $table->string('name_de')->nullable();
            $table->string('icon');
        });

        foreach ($companies as $company_id)  {
            $types = DB::table('address_types')->select('name', 'icon', DB::raw('\'App\\\Company\' as entity_type'), DB::raw($company_id . ' as entity_id'))->toSql();
            DB::table('company_address_types')->insertUsing(['name', 'icon', 'entity_type', 'entity_id'], $types);
        }

        DB::table('addresses')->update(['address_type' => 0]);

        Schema::drop('address_types');
        Schema::rename('company_address_types', 'address_types');

        DB::table('company_phone_types')->truncate();
        Schema::table('company_phone_types', static function (Blueprint $table) {
            $table->dropForeign(['phone_type_id']);
            $table->dropColumn('phone_type_id');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');

            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type');

            $table->string('name');
            $table->string('name_de')->nullable();
            $table->string('icon');
        });

        foreach ($companies as $company_id)  {
            $types = DB::table('phone_types')->select('name', 'icon', DB::raw('\'App\\\Company\' as entity_type'), DB::raw($company_id . ' as entity_id'))->toSql();
            DB::table('company_phone_types')->insertUsing(['name', 'icon', 'entity_type', 'entity_id'], $types);
        }

        DB::table('phones')->update(['phone_type' => 0]);

        Schema::drop('phone_types');
        Schema::rename('company_phone_types', 'phone_types');


        DB::table('company_social_types')->truncate();
        Schema::table('company_social_types', static function (Blueprint $table) {
            $table->dropForeign(['social_type_id']);
            $table->dropColumn('social_type_id');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');

            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type');

            $table->string('name');
            $table->string('name_de')->nullable();
            $table->string('icon');
        });

        foreach ($companies as $company_id)  {
            $types = DB::table('social_types')->select('name', 'icon', DB::raw('\'App\\\Company\' as entity_type'), DB::raw($company_id . ' as entity_id'))->toSql();
            DB::table('company_social_types')->insertUsing(['name', 'icon', 'entity_type', 'entity_id'], $types);
        }

        DB::table('socials')->update(['social_type' => 0]);

        Schema::drop('social_types');
        Schema::rename('company_social_types', 'social_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // unable to rollback
    }
}
