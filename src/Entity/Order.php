<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $created_at;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updated_at;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer;

    #[ORM\OneToMany(mappedBy: 'purchase', targetEntity: OrderProduct::class)]
    private $products;

    #[ORM\Column(type: 'string', length: 255, options: ['default' => self::STATUS_PREPARATION])]
    private string $status = self::STATUS_PREPARATION;

    /**
     * ==========================================================================
     * ============================ CONSTANTS ===================================
     * ==========================================================================
     */

    // Gestion des différents états d'une commande
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_PREPARATION = 'in_preparation';
    public const STATUS_DELIVERED = 'delivered';

    public const STATUSES = [self::STATUS_CANCELED, self::STATUS_PREPARATION, self::STATUS_DELIVERED];

    /**
     * ==========================================================================
     * ============================ CONSTRUCTOR =================================
     * ==========================================================================
     */

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }


    /**
     * ==========================================================================
     * ============================ ACCESSORS ===================================
     * ==========================================================================
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(OrderProduct $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setPurchase($this);
        }

        return $this;
    }

    public function removeProduct(OrderProduct $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getPurchase() === $this) {
                $product->setPurchase(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * ==========================================================================
     * ============================   METHODS   =================================
     * ==========================================================================
     */

    /**
     * Calcul du total d'une commande.
     * @return float
     */
    public function getTotalAmount(): float
    {
        // Initialisation
        $amount = 0;

        // Parcours la liste des produits reliés à cette commande
        foreach ($this->getProducts() as $row) {
            $amount += $row->getProduct()->getPrice() * $row->getQuantity();
        }

        return $amount;
    }
}
