<?php declare(strict_types=1);

namespace App\Domains\User\Permissions\Model;

use App\Domains\User\Role\Model\Role;  // Thêm dòng này
use App\Domains\User\Permissions\Model\Action;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CoreApp\Model\ModelAbstract;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends ModelAbstract
{
    use HasFactory;
    // use SoftDeletes;
    protected $table = 'permissions';

    // protected $dates = ['deleted_at'];
    protected $fillable = [
        'role_id',
        'action_id',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'value' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Domains\User\Model\User::class, 'user_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function action()
    {
        return $this->belongsTo(Action::class, 'action_id');
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    // Thêm accessor để lấy name từ role
    public function getNameAttribute(): ?string
    {
        return $this->role ? $this->role->name : null;
    }
}

