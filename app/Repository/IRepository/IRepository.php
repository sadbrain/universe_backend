<?php
namespace App\Repository\IRepository;

interface IRepository{
    public function get_all(?string $filter = null): array;
    public function get(string $filter);
    public function add($entity);
    public function update($entity);
    public function delete($entity);
}
