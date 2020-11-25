<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // clean emails table first
        DB::table('emails')->truncate();
        Schema::table('emails', static function (Blueprint $table) {
            $table->unsignedBigInteger('email_type')->nullable()->change();
        });


        // collect emails from users table
        $users = DB::table('users')->select(['id', 'email'])->get();
        foreach ($users as $user) {
            DB::table('emails')->insert([
                'entity_type' => \App\User::class,
                'entity_id' => $user->id,
                'email' => $user->email,
                'email_type' => null
            ]); 
        }

        // collect emails from socials tablle
        $socials = DB::table('socials')->get();
        foreach ($socials as $social) {
          if (filter_var($social->social_link, FILTER_VALIDATE_EMAIL)) {
            DB::table('emails')->insert([
                'entity_type' => $social->entity_type,
                'entity_id' => $social->entity_id,
                'email' => $social->social_link,
                'email_type' => null
            ]); 

            DB::table('socials')->where('id', $social->id)->delete();
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
        $emails = DB::table('emails')->where('entity_type', \App\User::class)->orderBy('entity_id')->orderBy('id')->get();
        $userId = 0;                  
        foreach ($emails as $email) {
            if ($email->entity_id != $userId) {
                $userId = $email->entity_id;
                DB::table('users')->where('id', $email->entity_id)->update([
                    'email' => $email->email
                ]);
                
                DB::table('emails')->where('id', $email->id)->delete(); 
            }
        }
    }
}
