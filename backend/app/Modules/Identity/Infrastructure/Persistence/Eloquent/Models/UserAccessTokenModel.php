<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class UserAccessTokenModel extends Model
{
    use HasUuids;

    protected $table = 'user_access_tokens';

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'token_hash',
        'last_used_at',
        'expires_at',
    ];
}
