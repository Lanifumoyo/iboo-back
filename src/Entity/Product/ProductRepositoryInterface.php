<?php

namespace App\Entity\Product;

use Symfony\Component\Uid\Uuid;

interface ProductRepositoryInterface
{
    public function all($param = null):array;
    public function save(Product $entity, bool $flush = false): void;
    public function findById(Uuid $id):Product | null;
    public function remove(Product $entity, bool $flush = false): void;
}