<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentReportingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $companies = DB::table('users')->select('id')->get();

        $id = 0;

        $companies->first();
        foreach($companies as $c) {
            $names = ['Low', 'Moderate', 'High'];

            foreach($names as $p => $name) {
                DB::table('incident_reporting_impact_potentials')->updateOrInsert(
                    ['id' => $id++],
                    [
                        'company_id' => $c->id,
                        'name' => $name,
                        'name_de' => null,
                        'position' => $p + 1
                    ]
                );
            }
        }

        $companies->first();
        foreach($companies as $c) {
            $names = ['New', 'Mobilization', 'Handling', 'Under review', 'Evaluation', 'Closed', 'Monitoring'];

            foreach($names as $p => $name) {
                DB::table('incident_reporting_process_states')->updateOrInsert(
                    ['id' => $id++],
                    [
                        'company_id' => $c->id,
                        'name' => $name,
                        'name_de' => null,
                        'position' => $p + 1
                    ]
                );
            }
        }

        $companies->first();
        foreach($companies as $c) {
            $names = ['Incident', 'Risk event', 'Near miss'];

            foreach($names as $p => $name) {
                DB::table('incident_reporting_event_types')->updateOrInsert(
                    ['id' => $id++],
                    [
                        'company_id' => $c->id,
                        'name' => $name,
                        'name_de' => null,
                        'position' => $p + 1
                    ]
                );
            }
        }

        $companies->first();
        foreach($companies as $c) {
            $names = ['Low', 'Medium', 'High', 'Critical'];

            foreach($names as $p => $name) {
                DB::table('incident_reporting_focus_priorities')->updateOrInsert(
                    ['id' => $id++],
                    [
                        'company_id' => $c->id,
                        'name' => $name,
                        'name_de' => null,
                        'position' => $p + 1
                    ]
                );
            }
        }

        $companies->first();
        foreach($companies as $c) {
            $names = ['Personnel', 'IT and infrastructure', 'Financial', 'Plant and equipment', 'Premises'];

            foreach($names as $p => $name) {
                DB::table('incident_reporting_resource_types')->updateOrInsert(
                    ['id' => $id++],
                    [
                        'company_id' => $c->id,
                        'name' => $name,
                        'name_de' => null,
                        'position' => $p + 1
                    ]
                );
            }
        }

        $companies->first();
        foreach($companies as $c) {
            $names = ['Task', 'Decision', 'Communication', 'Plant and equipment'];

            foreach($names as $p => $name) {
                DB::table('incident_reporting_action_types')->updateOrInsert(
                    ['id' => $id++],
                    [
                        'company_id' => $c->id,
                        'name' => $name,
                        'name_de' => null,
                        'position' => $p + 1
                    ]
                );
            }
        }

        $companies->first();
        foreach($companies as $c) {
            $names = ['Internal', 'Stakeholders', 'Pubic authorities', 'Employees'];

            foreach($names as $p => $name) {
                DB::table('incident_reporting_stakeholder_types')->updateOrInsert(
                    ['id' => $id++],
                    [
                        'company_id' => $c->id,
                        'name' => $name,
                        'name_de' => null,
                        'position' => $p + 1
                    ]
                );
            }
        }
    }
}
