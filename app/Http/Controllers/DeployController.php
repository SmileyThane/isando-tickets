<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;


class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        $githubPayload = json_decode($request->getContent());

        if ($githubPayload->ref == 'refs/heads/' . config('app.deploy_branch') && $githubPayload->repository->full_name == config('app.deploy_repository')) {
            $root_path = base_path();
            $process = new Process(['./deploy.sh'], $root_path);
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });
        }
    }
}
