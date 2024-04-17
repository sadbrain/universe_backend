<?php
namespace App\Repository;

use App\Repository\IRepository\IRoleRepository;
use App\Repository\Repository;

class RoleRepository extends Repository implements IRoleRepository {
    public function get_model(){
        return \App\Models\Role::class;
    }
}
