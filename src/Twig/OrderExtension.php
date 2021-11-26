<?php

namespace App\Twig;

use App\Entity\Order;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class OrderExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
    )
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('order_number', fn(Order|int $order) => $this->formatOrderNumber($order)),
        ];
    }

    protected function formatOrderNumber(Order|int $order): string
    {
        $number = $order instanceof Order ? $order->getNumber() : $order;

        return $this->translator->trans('order.number', ['%number%' => $number]);
    }
}
