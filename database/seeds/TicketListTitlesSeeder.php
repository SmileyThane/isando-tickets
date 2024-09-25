<?php

namespace Database\Seeders;

use App\Ticket;
use Illuminate\Database\Seeder;

class TicketListTitlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tickets = Ticket::all();

        foreach ($tickets as $ticket) {
            $ticket->contact_full_name = $ticket->contact?->userData?->full_name;
            $ticket->assigned_person_full_name = $ticket->assignedPerson?->userData?->full_name;
            $ticket->from_entity_name = $ticket->from?->name;
            $ticket->save();
        }
    }
}
