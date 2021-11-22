<?php

namespace App\Entity;

use App\Entity\Accessor\DemandGetters;
use App\Entity\Accessor\DemandSetters;
use App\Repository\DemandRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandRepository::class)]
class Demand
{
    use DemandGetters;
    use DemandSetters;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(type: 'integer')]
    private ?int $number = 0;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'demands')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $cart = null;
}
