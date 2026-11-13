<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
#[ORM\UniqueConstraint(name: 'UNIQ_PRODUCT_SKU', fields: ['sku'])]
#[UniqueEntity(fields: ['sku'], message: 'Another StrideX product already uses this SKU code.')]
class Product
{
    public const CATEGORIES = [
        'Running' => 'Running',
        'Street' => 'Street',
        'Performance' => 'Performance',
        'Lifestyle' => 'Lifestyle',
        'Heritage' => 'Heritage',
        'Everyday' => 'Everyday',
        'Electronics' => 'Electronics',
        'Accessories' => 'Accessories',
        'Furniture' => 'Furniture',
        'Office Supplies' => 'Office Supplies',
        'Hardware & Tools' => 'Hardware & Tools',
        'Other' => 'Other',
    ];

    public const GENDERS = [
        'Unisex' => 'unisex',
        'Men' => 'men',
        'Women' => 'women',
    ];

    public const BADGES = [
        'None' => '',
        'NEW DROP' => 'NEW DROP',
        'BESTSELLER' => 'BESTSELLER',
        'LIMITED' => 'LIMITED',
        'LIGHTWEIGHT' => 'LIGHTWEIGHT',
    ];

    public const BACKGROUNDS = [
        'Obsidian Black (bg-black)' => 'bg-black',
        'Crimson Red (bg-red)' => 'bg-red',
        'Slate Gray (bg-gray)' => 'bg-gray',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank(message: 'Please enter a product name.')]
    #[Assert\Length(
        min: 2,
        max: 160,
        minMessage: 'Product name must be at least {{ limit }} characters.',
        maxMessage: 'Product name cannot exceed {{ limit }} characters.'
    )]
    private string $name = '';

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'Please enter a product SKU.')]
    #[Assert\Length(
        min: 2,
        max: 64,
        minMessage: 'SKU must be at least {{ limit }} characters.',
        maxMessage: 'SKU cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9\-_]+$/',
        message: 'SKU may only contain letters, numbers, hyphens, and underscores.'
    )]
    private string $sku = '';

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $color = 'Monochrome';

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Please select a category.')]
    private string $category = 'Running';

    #[ORM\Column(length: 20)]
    private string $gender = 'unisex';

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $badge = 'NEW DROP';

    #[ORM\Column(length: 30)]
    private string $bg = 'bg-black';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = 'image/urban-collection.png';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Please enter a unit price.')]
    #[Assert\PositiveOrZero(message: 'Price must be zero or a positive amount.')]
    private string $price = '0.00';

    #[ORM\Column(name: 'quantity')]
    #[Assert\NotNull(message: 'Please enter the stock quantity.')]
    #[Assert\PositiveOrZero(message: 'Stock quantity cannot be negative.')]
    private int $stockQuantity = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2000, maxMessage: 'Description cannot exceed {{ limit }} characters.')]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'created_by_id', nullable: false, onDelete: 'CASCADE')]
    private ?User $createdBy = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = trim($name);

        return $this;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = strtoupper(trim($sku));

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = null !== $color ? trim($color) : null;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = trim($category);

        return $this;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = trim($gender);

        return $this;
    }

    public function getBadge(): ?string
    {
        return $this->badge;
    }

    public function setBadge(?string $badge): static
    {
        $this->badge = null !== $badge && '' !== trim($badge) ? trim($badge) : null;

        return $this;
    }

    public function getBg(): string
    {
        return $this->bg;
    }

    public function setBg(string $bg): static
    {
        $this->bg = trim($bg);

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = null !== $image && '' !== trim($image) ? ltrim(trim($image), '/') : null;

        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string|float|int $price): static
    {
        $this->price = number_format((float) $price, 2, '.', '');

        return $this;
    }

    public function getStockQuantity(): int
    {
        return $this->stockQuantity;
    }

    public function setStockQuantity(int $stockQuantity): static
    {
        $this->stockQuantity = $stockQuantity;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->stockQuantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->stockQuantity = $quantity;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = null !== $description ? trim($description) : null;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
