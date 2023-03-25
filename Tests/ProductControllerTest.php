<?php
namespace App\Tests;

use App\Controller\ProductController;
use App\Entity\Product\Product;
use App\Entity\Product\ProductRepositoryInterface;
use App\Exception\IdNotValid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

class ProductControllerTest extends WebTestCase
{
    public function testIndexMethodWithSearchParameter(): void
    {
        $request = $this->createMock(Request::class);
        $request->query = $this->createMock(ParameterBag::class);
        $request->query->expects($this->once())
            ->method('get')
            ->with('searchParam')
            ->willReturn('some search parameter');

        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        $productRepository->expects($this->once())
            ->method('all')
            ->with('some search parameter')
            ->willReturn([]);

        $controller = new ProductController($productRepository, new UuidFactory());

        $response = $controller->index($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testFindOneMethodWithValidUuid(): void
    {
        $uuid = (string) Uuid::v4();
        $product = new Product(Uuid::fromString($uuid), 'type', 'name', 'description', 1.0);

        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        $productRepository->expects($this->once())
            ->method('findById')
            ->with(Uuid::fromString($uuid))
            ->willReturn($product);

        $controller = new ProductController($productRepository, new UuidFactory());

        $response = $controller->findOne($uuid);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode($product), $response->getContent());
    }

    public function testFindOneMethodWithInvalidUuid(): void
    {
        $uuid = 'invalid_uuid';

        $productRepository = $this->createMock(ProductRepositoryInterface::class);

        $controller = new ProductController($productRepository, new UuidFactory());

        $this->expectException(IdNotValid::class);
        $controller->findOne($uuid);
    }

}

