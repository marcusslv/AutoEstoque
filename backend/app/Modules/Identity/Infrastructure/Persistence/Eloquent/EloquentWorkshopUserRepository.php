<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Contracts\WorkshopUserRepository;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\CreateWorkshopUserInput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\UpdateWorkshopUserInput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\WorkshopUserOutput;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class EloquentWorkshopUserRepository implements WorkshopUserRepository
{
    public function listByTenant(TenantId $tenantId): array
    {
        return User::query()
            ->where('tenant_id', $tenantId->value)
            ->orderBy('name')
            ->get()
            ->map(fn (User $user): WorkshopUserOutput => $this->toOutput($user))
            ->all();
    }

    public function findById(TenantId $tenantId, string $userId): ?WorkshopUserOutput
    {
        $user = User::query()
            ->where('tenant_id', $tenantId->value)
            ->whereKey($userId)
            ->first();

        return $user instanceof User ? $this->toOutput($user) : null;
    }

    public function existsByEmail(string $email): bool
    {
        return User::query()
            ->where('email', mb_strtolower(trim($email)))
            ->exists();
    }

    public function activeUsersCount(TenantId $tenantId): int
    {
        return User::query()
            ->where('tenant_id', $tenantId->value)
            ->where('status', 'active')
            ->count();
    }

    public function create(CreateWorkshopUserInput $input): WorkshopUserOutput
    {
        $user = User::query()->create([
            'tenant_id' => $input->tenantId,
            'public_id' => (string) Str::uuid(),
            'name' => trim($input->name),
            'email' => mb_strtolower(trim($input->email)),
            'password' => Hash::make($input->password),
            'status' => $input->status,
            'role' => $input->role,
        ]);

        return $this->toOutput($user);
    }

    public function update(UpdateWorkshopUserInput $input): WorkshopUserOutput
    {
        $user = User::query()
            ->where('tenant_id', $input->tenantId)
            ->whereKey($input->userId)
            ->firstOrFail();

        $user->update([
            'name' => trim($input->name),
            'status' => $input->status,
            'role' => $input->role,
        ]);

        return $this->toOutput($user->refresh());
    }

    public function deactivate(TenantId $tenantId, string $userId): WorkshopUserOutput
    {
        $user = User::query()
            ->where('tenant_id', $tenantId->value)
            ->whereKey($userId)
            ->firstOrFail();

        $user->update(['status' => 'inactive']);

        return $this->toOutput($user->refresh());
    }

    private function toOutput(User $user): WorkshopUserOutput
    {
        return new WorkshopUserOutput(
            id: (string) $user->id,
            tenantId: (string) $user->tenant_id,
            name: (string) $user->name,
            email: (string) $user->email,
            status: (string) $user->status,
            role: (string) $user->role,
        );
    }
}
