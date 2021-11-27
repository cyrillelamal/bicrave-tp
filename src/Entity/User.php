<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Security\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'registration_form.email.unique')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email(message: 'registration_form.email.bad_email')]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = Role::DEFAULT_ROLES;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'registration_form.password.not_blank')]
    #[Assert\Length(min: 6, minMessage: 'registration_form.password.min')]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: Cart::class, cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Order::class)]
    private Collection $orders;

    #[Pure] public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    #[Pure] public function getUserIdentifier(): string
    {
        return (string)$this->getEmail();
    }

    /**
     * {@inheritDoc}
     */
    #[Pure] public function getUsername(): string
    {
        return (string)$this->getEmail();
    }

    /**
     * Check if the user has role.
     * This method should not be user for authorization purposes!
     */
    #[Pure] public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * {@inheritDoc}
     */
    #[Pure] public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function addRole(string $role): self
    {
        $this->setRoles([...$this->getRoles(), $role]);

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    #[Pure] public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    #[Pure] public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): self
    {
        // set the owning side of the relation if necessary
        if ($cart->getOwner() !== $this) {
            $cart->setOwner($this);
        }

        $this->cart = $cart;

        return $this;
    }

    /**
     * @return Collection<Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->getOrders()->contains($order)) {
            $this->getOrders()->add($order);
            $order->setCustomer($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }
}
