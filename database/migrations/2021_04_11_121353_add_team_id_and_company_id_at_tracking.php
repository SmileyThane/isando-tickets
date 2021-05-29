<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdAndCompanyIdAtTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking', static function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('company_id')->nullable()->after('team_id');
        });

        foreach(\App\Tracking::all() as $tracking) {
            $user = \App\User::find($tracking->user_id);
            $company = $user->employee->companyData;
            $team = \App\Team::whereHas('employees', function ($query) use ($user) {
                return $query->where('company_user_id', '=', $user->employee->id);
            })->first();
            $tracking->company_id = $company->id;
            $tracking->team_id = $team ? $team->id : null;
            $tracking->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking', static function (Blueprint $table) {
            $table->dropColumn('team_id');
            $table->dropColumn('company_id');
        });
    }
}
