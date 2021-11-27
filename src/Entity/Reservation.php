<?php

namespace App\Entity;

use App\Entity\Accessor\ReservationGetters;
use App\Entity\Accessor\ReservationSetters;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    use ReservationGetters;
    use ReservationSetters;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $cost = null;

    #[ORM\Column(type: 'integer')]
    private ?int $number = 0;

    #[ORM\ManyToOne(targetEntity: Product::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Order $order = null;

    /**
     * Make a new reservation.
     *
     * @param Demand $demand the reserved product.
     * @param Order $order the related order.
     * @return static
     */
    public static function of(Demand $demand, Order $order): self
    {
        $reservation = new self();

        $reservation->setProduct($demand->getProduct());
        $reservation->setOrder($order);

        $reservation->setNumber($demand->getNumber());
        $reservation->setCost($demand->getProduct()->getCost());

        return $reservation;
    }

    /**
     * Get the price total cost.
     *
     * @return int cents.
     */
    #[Pure] public function getTotal(): int
    {
        return $this->getNumber() * $this->getCost();
    }

    /**
     * Get the final reservation price.
     *
     * @return float not formatted application currency value.
     */
    #[Pure] public function getPrice(): float
    {
        return $this->getTotal() / 100;
    }

    /**
     * Apply the reservation by reserving the demand.
     *
     * @return int the new number of units of the product in the stock.
     */
    public function apply(): int
    {
        return $this->getProduct()->reserve($this->getNumber());
    }
}
