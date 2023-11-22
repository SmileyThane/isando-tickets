<?php

namespace App\Http\Middleware;

use App\Exceptions\Customs\CustomException;
use App\Repositories\KbRepository;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class CheckKnowledgeBaseAccess
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(Request): (Response|RedirectResponse) $next
     * @param string $type
     * @return Response|RedirectResponse
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next, string $type)
    {
        throw_unless($request->has('type'), CustomException::class);

        $knowledgeBaseType = resolve(KbRepository::class)->findByAlias($request->get('type'));
        $permissionKey = array_search($type, array_column($knowledgeBaseType->permissions ?? [], 'type'));
        $access = $permissionKey !== false ?
            $request->user()->employee->hasPermissionId($knowledgeBaseType->permissions[$permissionKey]['value']) :
            false;

        throw_unless($access, CustomException::class);

        return $next($request);
    }
}
