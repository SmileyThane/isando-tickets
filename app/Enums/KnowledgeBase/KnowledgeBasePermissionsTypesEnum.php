<?php

namespace App\Enums\KnowledgeBase;

enum KnowledgeBasePermissionsTypesEnum: string
{
    case VIEW = 'view';
    case CREATE = 'create';
    case EDIT = 'edit';
    case DELETE = 'delete';
}
