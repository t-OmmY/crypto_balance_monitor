<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 */
abstract class BaseUuidModel extends Model
{
    use HasUuids;
}
