<?php

namespace App\Common\Cart;

use App\Common\Currency\CurrencyFormatterInterface;
use App\Entity\Cart;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CartResource
{
    public const GROUPS = [Cart::READ];

    private CurrencyFormatterInterface $currencyFormatter;
    private NormalizerInterface $normalizer;

    public function __construct(
        CurrencyFormatterInterface $currencyFormatter,
        NormalizerInterface        $normalizer,
    )
    {
        $this->currencyFormatter = $currencyFormatter;
        $this->normalizer = $normalizer;
    }

    /**
     * Normalize a shopping into a set of arrays.
     *
     * @param Cart $cart
     * @param string $format the format the normalization result will be encoded as.
     * @return array
     * @throws ExceptionInterface
     */
    public function normalize(Cart $cart, string $format = 'json'): array
    {
        $context = [
            AbstractNormalizer::GROUPS => self::GROUPS,
            AbstractNormalizer::CALLBACKS => [
                'total' => fn() => $this->currencyFormatter->format($cart->getPrice()),
            ],
        ];

        return $this->normalizer->normalize($cart, $format, $context);
    }
}
