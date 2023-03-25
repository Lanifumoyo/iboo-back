<?php

namespace App\Entity\Product;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(type: "string", unique: true, nullable: false)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private $id;

    #[ORM\Column(length: 255)]
    private string $product_type;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type:"text")]
    private string $description;

    #[ORM\Column(length: 255)]
    private string $weight;

    #[ORM\Column]
    private bool $enabled;

    /**
     * @param $UUID
     * @param string|null $product_type
     * @param string|null $name
     * @param string|null $description
     * @param string|null $weight
     */
    public function __construct($id, ?string $product_type, ?string $name, ?string $description, ?string $weight)
    {
        $this->id = $id;
        $this->product_type = $product_type;
        $this->name = $name;
        $this->description = $description;
        $this->weight = $weight;

        //we are going to supose that for default the attr enabled it's true
        $this->enabled = true;
    }


    public function getId()
    {
        return $this->id;
    }

//    public function setUUID(UuidType $UUID)
//    {
//        $this->UUID = $UUID;
//
//        return $this;
//    }

    public function getProductType(): ?string
    {
        return $this->product_type;
    }

    public function setProductType(string $product_type): self
    {
        $this->product_type = $product_type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function JsonSerialize(): mixed
    {
        return [
            "id" => $this->getId(),
            "product_type" => $this->getProductType(),
            "name" => $this->getName(),
            "description" => $this->getDescription(),
            "weight" => $this->getWeight(),
            "enabled" => $this->isEnabled()
        ];
    }
}
