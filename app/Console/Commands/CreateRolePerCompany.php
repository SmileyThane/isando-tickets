<?php

namespace App\Console\Commands;

use App\Company;
use App\CompanyUser;
use App\ModelHasRole;
use App\Repositories\RoleRepository;
use App\Role;
use App\User;
use Illuminate\Console\Command;

class CreateRolePerCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate roles for all companies';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (Role::where('company_id', null)->get() as $role) {
            $this->info('Generated role: ' . $role->name);
            foreach (Company::all() as $company) {
                $this->info('-- for company: ' . $company->name);
                (new RoleRepository())->replicate($role->id, $company->id);
            }
        }

        foreach (User::all() as $user) {
            if ($user->employee) {
                $roles = $user->employee->roles;
                foreach ($roles as $role) {
                    if ($role->company_id === null) {
                        $newRole = Role::query()->where([
                            'name' => $role->name,
                            'company_id' => $user->employee->company_id
                        ])->first();

                        $modelHasRole = ModelHasRole::where([
                            'model_id' => $user->employee->id,
                            'model_type' => CompanyUser::class,
                            'role_id' => $role->id
                        ])->delete();
                        $this->info('Company user: [' . $user->employee->id . '] - ' . $user->name);
                        $this->info(' -- changed role id from: ' . $role->id . ' to ' . $newRole->id);
                        ModelHasRole::query()->create([
                            'model_id' => $user->employee->id,
                            'model_type' => CompanyUser::class,
                            'role_id' => $newRole->id
                        ]);
                    }
                }
            }
        }
        return 0;
    }
}
