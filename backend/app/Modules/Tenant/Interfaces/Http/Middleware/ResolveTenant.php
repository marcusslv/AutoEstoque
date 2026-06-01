<?php

namespace App\Modules\Tenant\Interfaces\Http\Middleware;

use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Tenant\Domain\Exceptions\InvalidTenantIdException;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResolveTenant
{
    public function __construct(private TenantContext $tenantContext) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->header('X-Tenant-Id');

        if (! is_string($tenantId) || $tenantId === '') {
            return $this->invalidTenantResponse('The X-Tenant-Id header is required.');
        }

        try {
            $this->tenantContext->set(new TenantId($tenantId));

            return $next($request);
        } catch (InvalidTenantIdException) {
            return $this->invalidTenantResponse('The X-Tenant-Id header must be a valid UUID.');
        } finally {
            $this->tenantContext->clear();
        }
    }

    private function invalidTenantResponse(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
