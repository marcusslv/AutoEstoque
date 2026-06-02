<?php

namespace App\Modules\Identity\Interfaces\Http\Middleware;

use App\Models\User;
use App\Modules\Identity\Application\Contexts\AuthenticatedUserContext;
use App\Modules\Identity\Infrastructure\Persistence\Eloquent\Models\UserAccessTokenModel;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class AuthenticateApiRequest
{
    public function __construct(
        private TenantContext $tenantContext,
        private AuthenticatedUserContext $userContext,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! is_string($plainToken) || trim($plainToken) === '') {
            return $this->unauthorized('Bearer token is required.');
        }

        $token = UserAccessTokenModel::query()
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (! $token instanceof UserAccessTokenModel) {
            return $this->unauthorized('Invalid bearer token.');
        }

        if ($token->expires_at !== null && now()->greaterThan($token->expires_at)) {
            return $this->unauthorized('Bearer token has expired.');
        }

        $user = User::query()->find($token->user_id);

        if (! $user instanceof User || $user->status !== 'active' || $user->tenant_id === null) {
            return $this->unauthorized('Invalid bearer token.');
        }

        $this->tenantContext->set(new TenantId((string) $user->tenant_id));
        $this->userContext->set(
            userId: (string) ($user->public_id ?? $user->id),
            role: (string) $user->role,
        );
        $token->forceFill(['last_used_at' => now()])->save();

        try {
            return $next($request);
        } finally {
            $this->tenantContext->clear();
            $this->userContext->clear();
        }
    }

    private function unauthorized(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], Response::HTTP_UNAUTHORIZED);
    }
}
