<?php

namespace App\Entity;

use App\Common\Order\Status;
use App\Entity\Accessor\OrderGetters;
use App\Entity\Accessor\OrderSetters;
use App\Repository\OrderRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

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
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $status = Status::CREATED;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[Pure] public function __construct()
    {
        $this->reservations = new ArrayCollection();
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
