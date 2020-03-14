<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity()
 * @Table(name="orders")
 */
class Order
{
    public const  NEW_STATUS = 'new';
    public const  PAID_STATUS = 'paid';

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private ?int  $id;

    /**
     * @Column
     */
    private string  $status = self::NEW_STATUS;

    /**
     * @var Collection|Product[]
     * @ManyToMany(targetEntity=Product::class)
     */
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct($product): void
    {
        $this->products[] = $product;
    }
}
