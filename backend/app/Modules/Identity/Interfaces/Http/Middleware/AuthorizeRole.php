<?php

namespace App\Modules\Identity\Interfaces\Http\Middleware;

use App\Modules\Identity\Application\Contexts\AuthenticatedUserContext;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class AuthorizeRole
{
    public function __construct(private AuthenticatedUserContext $userContext) {}

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $this->userContext->hasAnyRole(...$roles)) {
            return $this->forbidden();
        }

        return $next($request);
    }

    private function forbidden(): JsonResponse
    {
        return response()->json([
            'message' => 'This action is not allowed for your profile.',
        ], Response::HTTP_FORBIDDEN);
    }
}
