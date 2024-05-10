<?php
namespace App\Repository;

use App\Repository\IRepository\ICompanyRepository ;
use App\Repository\Repository;

class CompanyRepository  extends Repository implements ICompanyRepository  {
    public function get_model(){
        return \App\Models\Company::class;
    }
}
