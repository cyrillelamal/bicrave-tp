<?php

namespace App\Entity;

use App\Common\Order\Status;
use App\Entity\Accessor\OrderGetters;
use App\Entity\Accessor\OrderSetters;
use App\Repository\OrderRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    use OrderGetters;
    use OrderSetters;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $status = Status::CREATED;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: Reservation::class, cascade: ['persist'])]
    private Collection $reservations;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->reservations = new ArrayCollection();
    }

    /**
     * Get the order number.
     * The order number is used to identify the order by humans.
     */
    #[Pure] public function getNumber(): string
    {
        return (string)$this->getId();
    }

    /**
     * Get the order total cost.
     *
     * @return int cents.
     */
    #[Pure] public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getReservations() as $reservation) /** @var Reservation $reservation */ {
            $total += $reservation->getTotal();
        }

        return $total;
    }

    /**
     * Get the final order price.
     *
     * @return float not formatted application currency value.
     */
    #[Pure] public function getPrice(): float
    {
        return $this->getTotal() / 100;
    }

    /**
     * Check if the provided user is the creator of this order.
     */
    #[Pure] public function isCreatedBy(UserInterface|string|null $user): bool
    {
        return null !== $user?->getUserIdentifier()
            && $user->getUserIdentifier() === $this->getCustomer()->getUserIdentifier();
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->getReservations()->contains($reservation)) {
            $this->getReservations()->add($reservation);
            $reservation->setOrder($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->getReservations()->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getOrder() === $this) {
                $reservation->setOrder(null);
            }
        }

        return $this;
    }
}
