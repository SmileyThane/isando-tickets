<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMerge extends Model
{
    use SoftDeletes;

    protected $fillable = ['parent_ticket_id', 'child_ticket_id', 'merged_by_user_id', 'merge_comment'];

    public function parentTicketData(): HasOne
    {
        return $this->hasOne(Ticket::class, 'id', 'parent_ticket_id')
            ->select('id', 'name', 'from_entity_id', 'from_entity_type', 'to_entity_id', 'to_entity_type', 'updated_at')->withTrashed();
    }

    public function childTicketData(): HasOne
    {
        return $this->hasOne(Ticket::class, 'id', 'child_ticket_id')
            ->select('id', 'name', 'from_entity_id', 'from_entity_type', 'to_entity_id', 'to_entity_type', 'updated_at')->withTrashed();
    }
}
