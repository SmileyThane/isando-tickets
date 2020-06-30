<?php

namespace App\Http\Controllers;

use App\Repository\EmailReceiverRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    protected $emailReceiverRepo;

    /**
     * Create a new controller instance.
     *
     * @param EmailReceiverRepository $emailReceiverRepo
     */
    public function __construct(EmailReceiverRepository $emailReceiverRepo)
    {
//        $this->middleware('auth');
        $this->emailReceiverRepo = $emailReceiverRepo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        return view('app');
    }

    public function receiveMail(Request $request, $type = 'answer')
    {
        $this->emailReceiverRepo->receiveMail($type);
    }
}
