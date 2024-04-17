<?php
namespace App\Repository;

use App\Repository\IRepository\IUserRepository;
use App\Repository\Repository;

class UserRepository extends Repository implements IUserRepository {
    public function get_model(){
        return \App\Models\User::class;
    }
}
