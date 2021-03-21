<?php

namespace App\Console\Commands;

use App\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class AddPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom_permission {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for custom permission creating';

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
    public function handle(): void
    {

        $permissionId = Permission::max('id') + 1;
        $constantNameOriginal = $this->argument('name');
        $constantName = strtoupper($this->argument('name'));
        $newConstant = "\tpublic const $constantName = $permissionId;";

        $findString = '//custom permissions';
        $replaceModelString = $findString . "\n" . $newConstant;

        $replaceSeederString = $findString . "\n" . "
        DB::table('permissions')->updateOrInsert(
            ['id' => $permissionId],
            [
                'name' => '$constantNameOriginal',
                'guard_name' => 'web'
            ]
        );";

        $modelContent = File::get(app_path('Permission.php'));
        $seederContent = File::get(database_path('seeds/PermissionSeeder.php'));

        if (strpos($modelContent, $constantName) === false) {
            $newModelContent = str_replace($findString, $replaceModelString, $modelContent);
            File::put(app_path('Permission.php'), $newModelContent);

            $newSeederContent = str_replace($findString, $replaceSeederString, $seederContent);
            File::put(database_path('seeds/PermissionSeeder.php'), $newSeederContent);

            Artisan::call('db:seed', [
                '--class' => 'PermissionSeeder'
            ]);

            $this->info('Database successfully updated!');

            $this->info('New permission added successfully!');
        } else {
            $this->error('This permission already exists! Try again with new name.');
        }
    }
}
