<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Client;



class FillPrimaryEmailType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('email_types')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Primary',
                'name_de' => 'Primär',
                'icon' => 'mdi-email-lock',
                'entity_id' => 0,
                'entity_type' => '*'
            ]
        );

        $users = DB::table('users')->pluck('id');
        foreach ($users as $userId) {
            $email = DB::table('emails')->where('entity_type', User::class)->where('entity_id', $userId)->whereNull('email_type')->first();
            if ($email) {
                DB::table('emails')->where('id', $email->id)->update(['email_type' => 1]);
            }
        }

        $clients = DB::table('clients')->pluck('id');
        foreach ($clients as $clientId) {
            $email = DB::table('emails')->where('entity_type', Client::class)->where('entity_id', $clientId)->whereNull('email_type')->first();
            if ($email) {
                DB::table('emails')->where('id', $email->id)->update(['email_type' => 1]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('emails')->where('email_type', 1)->update('email_type', null);
        DB::table('email_types')->delete(1);
    }
}
