<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefineAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', static function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries');
        });

        $addresses = DB::table('addresses')->get();
        foreach ($addresses as $address) {
            if (strpos($address->address_line_3, ',')) {
                list($city, $countryName) = explode(',', $address->address_line_3);
                $country = Countries::where('name', trim($countryName))->orWhere('name_de', trim($countryName) )->first();
                $countryId = $country ? $country->id : 217; // Switzerland
            } else {
                $city = $address->address_line_3;
                $countryId = 217; // Switzerland
            }


            DB::table('addresses')->where('id', $address->id)->update([
                'street' => trim($address->address),
                'postal_code' => trim($address->address_line_2),
                'city' => trim($city),
                'country_id' => $countryId
            ]);
        }

        Schema::table('addresses', static function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('address_line_2');
            $table->dropColumn('address_line_3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', static function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
        });

        $addresses = DB::table('addresses')->get();
        foreach ($addresses as $address) {
            $country = Countries::find($address->country_id);
            DB::table('addresses')->where('id', $address->id)->update([
                'address' => $address->street,
                'address_line_2' => $address->postal_code,
                'address_line_3' => $address->city . ', ' . $country->name
            ]);
        }

        Schema::table('addresses', static function (Blueprint $table) {

            $table->dropForeign(['country_id']);
            $table->dropColumn('street');
            $table->dropColumn('city');
            $table->dropColumn('postal_code');
            $table->dropColumn('country_id');
        });
    }
}
