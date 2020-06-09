<?php


namespace App\Repository;

use App\Plan;

class PlanRepository
{

    public function find($id)
    {
        return Plan::find($id);
    }

}
