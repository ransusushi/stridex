<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_USERS_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account registered with this email address.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_STAFF = 'ROLE_STAFF';
    public const ROLE_USER = 'ROLE_USER';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISABLED = 'disabled';
    public const STATUS_ARCHIVED = 'archived';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'first_name', length: 80)]
    private string $firstName = '';

    #[ORM\Column(name: 'last_name', length: 80)]
    private string $lastName = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Please enter a full name.')]
    #[Assert\Length(min: 2, max: 120, minMessage: 'Name must be at least {{ limit }} characters long.')]
    private string $name = '';

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Please enter an email address.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    #[Assert\Length(max: 180)]
    private string $email = '';

    /**
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [self::ROLE_STAFF];

    #[ORM\Column(length: 20)]
    private string $role = 'staff';

    #[ORM\Column(name: 'password_hash')]
    private string $password = '';

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: [self::STATUS_ACTIVE, self::STATUS_DISABLED, self::STATUS_ARCHIVED])]
    private string $status = self::STATUS_ACTIVE;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'createdBy', orphanRemoval: true)]
    private Collection $products;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $orders;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->products = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = trim($firstName);
        $this->syncFullName();

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = trim($lastName);
        $this->syncFullName();

        return $this;
    }

    public function getName(): string
    {
        if ('' !== $this->name) {
            return $this->name;
        }

        return trim($this->firstName.' '.$this->lastName);
    }

    public function setName(string $name): static
    {
        $trimmed = trim($name);
        $this->name = $trimmed;

        if ('' !== $trimmed) {
            $parts = preg_split('/\s+/', $trimmed, 2) ?: [$trimmed];
            $this->firstName = $parts[0];
            $this->lastName = $parts[1] ?? '';
        } else {
            $this->firstName = '';
            $this->lastName = '';
        }

        return $this;
    }

    private function syncFullName(): void
    {
        $combined = trim($this->firstName.' '.$this->lastName);
        if ('' !== $combined) {
            $this->name = $combined;
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = strtolower(trim($email));

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return '' !== $this->email ? $this->email : 'anonymous';
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        if ('admin' === $this->role && !\in_array(self::ROLE_ADMIN, $roles, true)) {
            $roles[] = self::ROLE_ADMIN;
        } elseif ('staff' === $this->role && !\in_array(self::ROLE_STAFF, $roles, true)) {
            $roles[] = self::ROLE_STAFF;
        }
        $roles[] = self::ROLE_USER;

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = array_values(array_unique($roles));
        if (\in_array(self::ROLE_ADMIN, $this->roles, true)) {
            $this->role = 'admin';
        } elseif (\in_array(self::ROLE_STAFF, $this->roles, true)) {
            $this->role = 'staff';
        } else {
            $this->role = 'user';
        }

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getPrimaryRole(): string
    {
        if ($this->isAdmin()) {
            return self::ROLE_ADMIN;
        }
        if (\in_array(self::ROLE_STAFF, $this->roles, true) || 'staff' === $this->role) {
            return self::ROLE_STAFF;
        }

        return self::ROLE_USER;
    }

    public function setPrimaryRole(string $role): static
    {
        if (self::ROLE_ADMIN === $role || 'admin' === $role) {
            $this->roles = [self::ROLE_ADMIN];
            $this->role = 'admin';
        } elseif (self::ROLE_USER === $role || 'user' === $role) {
            $this->roles = [self::ROLE_USER];
            $this->role = 'user';
        } else {
            $this->roles = [self::ROLE_STAFF];
            $this->role = 'staff';
        }

        return $this;
    }

    public function isAdmin(): bool
    {
        return \in_array(self::ROLE_ADMIN, $this->roles, true) || 'admin' === $this->role;
    }

    public function isStaff(): bool
    {
        return !$this->isAdmin() && (\in_array(self::ROLE_STAFF, $this->roles, true) || 'staff' === $this->role);
    }

    public function getRoleLabel(): string
    {
        if ($this->isAdmin()) {
            return 'Admin';
        }
        if ($this->isStaff()) {
            return 'Staff';
        }

        return 'Customer';
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isActive(): bool
    {
        return self::STATUS_ACTIVE === $this->status;
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

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }
}
