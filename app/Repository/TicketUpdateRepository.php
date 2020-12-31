<?php


namespace App\Repository;

use App\CompanyUser;
use App\Product;
use App\Team;
use App\TicketCategory;
use App\TicketHistory;
use App\TicketPriority;
use App\TicketType;
use Illuminate\Support\Facades\Auth;

class TicketUpdateRepository
{
    public function makeHistoryDescription($action, $item = null, $shouldBeTranslated = false, $translationGroup = null)
    {
        return json_encode([
            'action' => $action,
            'item' => $item,
            'should_be_translated' => $shouldBeTranslated,
            'translationGroup' => $translationGroup
        ]);
    }

    public function addHistoryItem($ticketId, $companyUserId = null, $description = null): bool
    {
        $ticketHistory = new TicketHistory();
        $ticketHistory->company_user_id = $companyUserId ?? Auth::user()->employee->id;
        $ticketHistory->ticket_id = $ticketId;
        $ticketHistory->description = $description;
        $ticketHistory->save();
        return true;
    }

    public function setCompanyUserId($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $fullName = CompanyUser::find($value)->userData->full_name;
            $historyItem = $this->makeHistoryDescription('employee_attached', $fullName);
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setContactCompanyUserId($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $fullName = CompanyUser::find($value)->userData->full_name;
            $historyItem = $this->makeHistoryDescription('contact_attached', $fullName);
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setTeamId($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $teamName = Team::find($value)->name;
            $historyItem = $this->makeHistoryDescription('team_attached', $teamName);
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setDueDate($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $historyItem = $this->makeHistoryDescription('due_date_updated', $value);
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setPriorityId($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $name = TicketPriority::find($value)->name;
            $historyItem = $this->makeHistoryDescription('priority_updated', $name, true, 'ticket_priorities');
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setProductId($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $name = Product::find($value)->name;
            $historyItem = $this->makeHistoryDescription('product_updated', $name);
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setAccessDetails($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $historyItem = $this->makeHistoryDescription('access_details_updated', $value);
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setConnectionDetails($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $historyItem = $this->makeHistoryDescription('conn_details_updated', $value);
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setCategoryId($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $name = TicketCategory::find($value)->name;
            $historyItem = $this->makeHistoryDescription('category_updated', $name, true, 'ticket_categories');
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setFrom($oldEntity, $entity, $oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value || $oldEntity !== $entity) {
            $name = $entity::find($value)->name;
            $historyItem = $this->makeHistoryDescription('company_updated', $name);
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

    public function setTicketTypeId($oldValue, $value, $ticketId, $companyUserId = null)
    {
        if ($oldValue !== $value) {
            $name = TicketType::find($value)->name;
            $historyItem = $this->makeHistoryDescription('ticket_type_updated', $name, true, 'ticket_types');
            $this->addHistoryItem($ticketId, $companyUserId, $historyItem);
        }
        return $value;
    }

}
