<?php

namespace App\Controller;

use App\Entity\Product\Product;
use App\Entity\Product\ProductRepositoryInterface;
use App\Exception\IdNotValid;
use App\Exception\ProductNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validation;

class ProductController extends AbstractController
{
    public function __construct(private ProductRepositoryInterface $productRepository, private UuidFactory $uuidFactory){

    }

    #[Route('/products', name: 'index_product',methods: 'GET')]
    public function index(Request $request): JsonResponse
    {

        $products = $this->productRepository->all($request->query->get("searchParam"));

        return new JsonResponse($products,200);
    }

    #[Route('/product/{uuid}', name: 'find_product',methods: 'GET')]
    public function findOne($uuid): JsonResponse
    {
        if(!Uuid::isValid($uuid)){
            throw new IdNotValid();
        }
        $product = $this->productRepository->findById(Uuid::fromString($uuid));

        if(empty($product)){
            throw new ProductNotFoundException();
        }
        return new JsonResponse($product,200);
    }

    #[Route('/product', name: 'create_product',methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        $val = new Constraints\Collection(
            [
                'product_type' => new Constraints\NotNull(),
                'name'=> new Constraints\NotNull(),
                'description'=> new Constraints\NotNull(),
                'weight' => new Constraints\NotNull(),
                'enabled'=> new Constraints\Optional(),
            ]
        );

        //Data validation
        $violations = Validation::createValidator()->validate($request->request->all(), $val);
        if(count($violations)>0){
            return new JsonResponse($violations,401);
        }


        $product = new Product(
            $this->uuidFactory->create(),
            $request->get("product_type"),
            $request->get("name"),
            $request->get("description"),
            $request->get("weight"),
        );

        if(!empty($request->get("enable"))){
            $product->setEnabled($request->get("enable"));
        }

        $this->productRepository->save($product,true);

        return new JsonResponse($product,200);
    }

    #[Route('/product/{uuid}',methods: ['PUT'])]
    public function putProduct($uuid, Request $request){
        $val = new Constraints\Collection(
            [
                'product_type' => new Constraints\Optional(),
                'name'=> new Constraints\Optional(),
                'description'=> new Constraints\Optional(),
                'weight' => new Constraints\Optional(),
                'enabled'=> new Constraints\Optional(),
            ]
        );


        $requestData = (json_decode($request->getContent(),true));
//        dd($requestData);
        $violations = Validation::createValidator()->validate($requestData, $val);

        if(count($violations)>0){
            return new JsonResponse($violations,401);
        }

        $product = $this->productRepository->findById(Uuid::fromString($uuid));

        if(is_null($product)){
            throw new ProductNotFoundException();
        }

        if(isset($requestData["product_type"])){
            $product->setProductType($requestData["product_type"]);
        }
        if(isset($requestData["name"])){
            $product->setName($requestData["name"]);
        }
        if(isset($requestData["description"])){
            $product->setDescription($requestData["description"]);
        }
        if(isset($requestData["weight"]) && is_integer((int) ($requestData["weight"]))){
            $product->setWeight($requestData["weight"]);
        }
        if(isset($requestData["enabled"]) && is_bool($requestData["enabled"])){
            $product->setEnabled($requestData["enabled"]);
        }

        $this->productRepository->save($product,true);

        return new JsonResponse(...["data"=>$product,"status"=>200]);
    }

    #[Route('/product/{uuid}',methods: ['DELETE'])]
    public function deleteProduct($uuid, Request $request){

        $product = $this->productRepository->findById(Uuid::fromString($uuid));
        if(empty($product)){
            throw new ProductNotFoundException();
        }
        $this->productRepository->remove($product);

        return new JsonResponse(...["data"=>"Product deleteted successfully"]);
    }


}
