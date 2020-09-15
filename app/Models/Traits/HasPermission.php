<?php

namespace App\Models\Traits;

use Illuminate\Support\Arr;
use App\Models\Permission;
use App\Models\Role;

trait HasPermissions
{

  public function givePermissionTo(...$permissions)
  {
    $permissions = $this->getAllPermissions(Arr::flatten($permissions));

    if ($permissions === null) {
      return $this;
    }

    $this->permissions()->saveMany($permissions);

    return $this;
  }

  public function giveRoleTo(...$roles)
  {
    $roles = $this->getAllRoles(Arr::flatten($roles));

    if ($roles === null) {
      return $this;
    }

    $this->roles()->saveMany($roles);

    return $this;
  }

  public function hasRole(...$roles)
  {
    foreach ($roles as $role) {
      if ($this->roles->contains('name', $role)) {
        return true;
      }
      return false;
    }
  }

  protected function hasPermission($permission)
  {
    return (bool) $this->permissions->where('name', $permission->name)->count();
  }

  protected function getAllRoles(array $roles)
  {
    return Role::whereIn('name', $roles)->get();
  }

  protected function getAllPermissions(array $permissions)
  {

    return Permission::whereIn('name', $permissions)->get();
  }

  public function hasPermissionTo($permission)
  {
    //has permission through role

    return $this->hasPermissionThroughRole($permission) ||  $this->hasPermission($permission);
  }

  public function hasPermissionThroughRole($permission)
  {
    foreach ($permission->roles as $role) {
      if ($this->roles->contains($role)) {
        return true;
      }
    }
  }

  public function permissions()
  {
    return $this->belongsToMany(Permission::class);  //  permission_user
  }

  public function roles()
  {
    # code...
    return $this->belongsToMany(Role::class); //role_user table
  }
}
