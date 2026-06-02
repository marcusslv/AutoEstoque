<?php

namespace App\Modules\Identity\Interfaces\Http\Presenters;

use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\ListWorkshopUsersOutput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\WorkshopUserOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class WorkshopUserPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        if ($output instanceof ListWorkshopUsersOutput) {
            return response()->json([
                'data' => array_map(
                    fn (WorkshopUserOutput $user): array => $this->user($user),
                    $output->users,
                ),
                'meta' => [
                    'total' => $output->total(),
                ],
            ]);
        }

        assert($output instanceof WorkshopUserOutput);

        return response()->json([
            'data' => $this->user($output),
        ], Response::HTTP_CREATED);
    }

    /**
     * @return array<string, mixed>
     */
    private function user(WorkshopUserOutput $user): array
    {
        return [
            'id' => $user->id,
            'tenant_id' => $user->tenantId,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'role' => $user->role,
        ];
    }
}
