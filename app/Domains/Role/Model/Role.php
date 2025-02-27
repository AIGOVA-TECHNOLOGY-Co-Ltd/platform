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
use App\Domains\Role\Feature\Model\Feature as FeatureModel; // Sử dụng namespace từ file của bạn

class Role extends ModelAbstract
{
    use HasFactory;
    use TypeFormat;

    /**
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'alias', // Bao gồm alias
    ];

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
     * Quan hệ: Một Role có nhiều Role Features.
     *
     * @return HasMany
     */
    public function roleFeatures(): HasMany
    {
        return $this->hasMany(RoleFeatureModel::class, 'role_id');
    }

    /**
     * Quan hệ: Một Role có nhiều Features thông qua RoleFeatures (nhiều-nhiều).
     *
     * @return BelongsToMany
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(FeatureModel::class, 'role_features', 'role_id', 'feature_id');
        // Xóa ->withTimestamps()
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
     * Get the role features for the role.
     *
     * @return HasMany
     */
    public function roleFeature(): HasMany
    {
        return $this->hasMany(RoleFeatureModel::class, 'role_id');
    }

    /**
     * Get the permissions for the role.
     *
     * @return HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(\App\Domains\Permissions\Model\Permission::class, 'role_id');
    }
}
