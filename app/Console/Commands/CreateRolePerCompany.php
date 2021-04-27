<?php

namespace App\Console\Commands;

use App\Company;
use App\Repositories\RoleRepository;
use App\Role;
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
        return 0;
    }
}
