<?php


namespace App\Repositories;

use App\Providers\IxarmaServiceProvider;

class IxarmaRepository
{

    /**
     * @var \App\Providers\IxarmaServiceProvider
     */
    protected $service;

    public function initRepo(IxarmaServiceProvider $service)
    {
        $this->service = $service;
    }

    public function login($ixarmaUsername, $ixarmaPassword, $userId = null)
    {
        $userId = $userId ?? Auth::user()->id;
        return $this->service->login($userId, $ixarmaUsername, $ixarmaPassword);
    }

    public function getParticipants($orgId = 1, $userId = null)
    {
        $userId = $userId ?? Auth::user()->id;
        $data = [
            'currentPage' => 0,
            'filterValue' => '',
            'pageSize' => 10000,
            'parentOrgId' => $orgId,
            'recursive' => true,
            'type' => 'PARTICIPANT'
        ];

        return $this->service->request($userId, 'list', $data, 'POST');
    }

    public function getOrganizations($orgId = 1, $userId = null)
    {
        $userId = $userId ?? Auth::user()->id;
        $data = [
            'currentPage' => 0,
            'filterValue' => '',
            'pageSize' => 10000,
            'parentOrgId' => $orgId,
            'recursive' => true,
            'type' => 'ORGANIZATION'
        ];

        return $this->service->request($userId, 'list', $data, 'POST');
        // return $this->service->request($userId, 'tree/' . $parentOrgId);
    }

}
