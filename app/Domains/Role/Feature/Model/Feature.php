<?php

declare(strict_types=1);

namespace App\Domains\Role\Feature\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Role\Model\Role as RoleModel;
use App\Domains\Role\RoleFeature\Model\RoleFeature as RoleFeatureModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends ModelAbstract
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'features';

    /**
     * @const string
     */
    public const TABLE = 'features';

    /**
     * Quan hệ: Một Feature thuộc về nhiều Roles thông qua RoleFeatures (nhiều-nhiều).
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(RoleModel::class, 'role_features', 'feature_id', 'role_id');
        // Xóa ->withTimestamps()
    }
}
