<?php

namespace Database\Seeders;

use App\Enums\KnowledgeBase\KnowledgeBasePermissionsTypesEnum;
use App\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KBTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('knowledge_base_types')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Knowledge base',
                'name_de' => 'Wissensdatenbank',
                'alias' => 'knowledge_base',
                'permissions' => [
                    [
                        'type' => KnowledgeBasePermissionsTypesEnum::VIEW,
                        'value' => Permission::KB_VIEW_ACCESS,
                    ],
                    [
                        'type' => KnowledgeBasePermissionsTypesEnum::CREATE,
                        'value'=> Permission::KB_CREATE_ACCESS,
                    ],
                    [
                        'type' => KnowledgeBasePermissionsTypesEnum::EDIT,
                        'value' => Permission::KB_EDIT_ACCESS,
                    ],
                    [
                        'type' => KnowledgeBasePermissionsTypesEnum::DELETE,
                        'value' => Permission::KB_DELETE_ACCESS,
                    ],
                ],
            ],
        );
        DB::table('knowledge_base_types')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'Risk repository',
                'name_de' => 'Risikospeicher',
                'alias' => 'risk_repository',
                'permissions' => [
                    [
                        'type' => KnowledgeBasePermissionsTypesEnum::VIEW,
                        'value' => Permission::KB_VIEW_ACCESS,
                    ],
                    [
                        'type' => KnowledgeBasePermissionsTypesEnum::CREATE,
                        'value' => Permission::KB_CREATE_ACCESS,
                    ],
                    [
                        'type' => KnowledgeBasePermissionsTypesEnum::EDIT,
                        'value' => Permission::KB_EDIT_ACCESS,
                    ],
                    [
                        'type' => KnowledgeBasePermissionsTypesEnum::DELETE,
                        'value' => Permission::KB_DELETE_ACCESS,
                    ],
                ],
            ]
        );
    }
}
