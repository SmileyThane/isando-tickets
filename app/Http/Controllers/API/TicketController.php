<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\TicketRepository;
use App\TicketPriority;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $ticketRepo;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepo = $ticketRepository;
    }

    public function get(Request $request)
    {
        $tickets = $this->ticketRepo->all($request);
        return self::showResponse(true, $tickets);
    }

    public function priorities()
    {
        return self::showResponse(true, TicketPriority::all());
    }

    public function find($id)
    {
        $ticket = $this->ticketRepo->find($id);
        return self::showResponse(true, $ticket);
    }

    public function create(Request $request)
    {
        $success = false;
        $result = $this->ticketRepo->validate($request);
        if ($result === true) {
            $result = $this->ticketRepo->create($request);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function update(Request $request, $id)
    {
        $success = false;
        $result = $this->ticketRepo->validate($request);
        if ($result === true) {
            $result = $this->ticketRepo->update($request, $id);
            $success = true;
        }
        return self::showResponse($success, $result);
    }

    public function delete($id)
    {
        $result = $this->ticketRepo->delete($id);
        return self::showResponse($result);
    }

    public function attachTeam(Request $request, $id)
    {
        $result = $this->ticketRepo->attachTeam($request, $id);
        return self::showResponse($result);
    }

    public function attachEmployee(Request $request, $id)
    {
        $result = $this->ticketRepo->attachEmployee($request, $id);
        return self::showResponse($result);
    }

    public function attachContact(Request $request, $id)
    {
        $result = $this->ticketRepo->attachContact($request, $id);
        return self::showResponse($result);
    }

    public function addAnswer(Request $request, $id)
    {
        $result = $this->ticketRepo->addAnswer($request, $id);
        return self::showResponse($result);
    }

    public function addNotice(Request $request, $id)
    {
        $result = $this->ticketRepo->addNotice($request, $id);
        return self::showResponse($result);
    }

}
