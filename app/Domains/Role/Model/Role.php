<?php

declare(strict_types=1);

namespace App\Domains\Role\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\Role\Model\Traits\TypeFormat;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Model\User as UserModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Role\RoleFeature\Model\RoleFeature as RoleFeatureModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Enterprise\Model\Enterprise as EnterpriseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends ModelAbstract
{
    use HasFactory;
    use TypeFormat;

    /**
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ: Mỗi Role liên kết với Enterprise thông qua User và UserRoles
     * Giả sử Role -> UserRoles -> Users -> Enterprises
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enterprise(): BelongsTo
    {
        return $this->hasOneThrough(
            EnterpriseModel::class,
            UserModel::class,
            'id', // Foreign key trên users
            'id', // Foreign key trên enterprises
            'user_id', // Local key trên user_roles (kết nối với users)
            'enterprise_id' // Local key trên users (kết nối với enterprises)
        )->via('users'); // Sử dụng mối quan hệ users để kết nối
    }

    /**
     * Get the role features for the role.
     *
     * @return HasMany
     */
    public function roleFeature(): HasMany
    {
        return $this->hasMany(RoleFeatureModel::class, 'role_id');
    }

    /**
     * Quan hệ: Một Role có nhiều User thông qua `user_roles`
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(UserModel::class, 'user_roles', 'role_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Kiểm tra nếu Role thuộc về một Enterprise cụ thể
     * Giả sử kiểm tra qua mối quan hệ với Enterprise thông qua Users
     *
     * @param int $enterpriseId
     * @return bool
     */
    public function belongsToEnterprise(int $enterpriseId): bool
    {
        // Kiểm tra qua mối quan hệ với Enterprise
        return $this->enterprise && $this->enterprise->id === $enterpriseId;
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(\App\Domains\Permissions\Model\Permission::class, 'role_id');
    }
}