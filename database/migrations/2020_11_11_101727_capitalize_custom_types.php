<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CapitalizeCustomTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = DB::table('address_types')->select(['id', 'name', 'name_de'])->get();
        foreach ($types as $type) {
            DB::table('address_types')->where('id', $type->id)->update([
                'name' => ucwords($type->name),
                'name_de' => ucwords($type->name_de)
            ]);
        }

        $types = DB::table('phone_types')->select(['id', 'name', 'name_de'])->get();
        foreach ($types as $type) {
            DB::table('phone_types')->where('id', $type->id)->update([
                'name' => ucwords($type->name),
                'name_de' => ucwords($type->name_de)
            ]);
        }

        $types = DB::table('social_types')->select(['id', 'name', 'name_de'])->get();
        foreach ($types as $type) {
            DB::table('social_types')->where('id', $type->id)->update([
                'name' => ucwords($type->name),
                'name_de' => ucwords($type->name_de)
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // nothing to revert
    }
}
