<?php

namespace Database\Seeders;

use App\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            DB::table('activity_types')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'name' => 'Call out'
                ]
            );
            DB::table('activity_types')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'name' => 'Call in'
                ]
            );
            DB::table('activity_types')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'name' => 'Email out'
                ]
            );
            DB::table('activity_types')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'name' => 'Email in'
                ]
            );
            DB::table('activity_types')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'name' => 'Note'
                ]
            );
            DB::table('activity_types')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'name' => 'Offer'
                ]
            );
            DB::table('activity_types')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'name' => 'Deal'
                ]
            );
            DB::table('activity_types')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'name' => 'Other'
                ]
            );
        }

    }
}
