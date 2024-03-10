<?php
namespace App\Repository;

use App\Repository\IRepository\IRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements IRepository{
    protected $_model;
    public function __construct(){
        $this -> set_model();
    }
        /**
     * get model
     * @return string
     */
    public function get_model(){
        
    }
    /**
     * Set model
     */
    public function set_model()
    {
        $this->_model = app()->make(
            $this->get_model()
        );
    }
    public function get_all(?string $filter = null){
        $query = $this->_model::query();
        if (!is_null($filter)) {
            $query->whereRaw($filter);
        }
        
        return $query;
    }

    public function get(string $filter){
        $query = $this -> _model::query();
        $query->whereRaw($filter);

        return $query->first();
    }

    public function add($entity){
        return $this -> _model::create($entity->toArray());
    }

    public function update($entity){
        $entity -> update($entity->toArray());
        return $entity;
    }

    public function delete($entity){
        $entity->delete();
    }
}
