<?php

namespace App\Common\Currency;

use NumberFormatter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ApplicationLocaleCurrencyFormatter implements CurrencyFormatterInterface
{
    private CurrencyProviderInterface $currencyProvider;
    private RequestStack $requestStack;
    private string $locale;

    public function __construct(
        CurrencyProviderInterface $currencyProvider,
        RequestStack              $requestStack,
        string                    $locale,
    )
    {
        $this->currencyProvider = $currencyProvider;
        $this->requestStack = $requestStack;
        $this->locale = $locale;
    }

    /**
     * {@inheritDoc}
     */
    public function format(float|int $price): string
    {
        $fmt = new NumberFormatter($this->getLocale(), NumberFormatter::CURRENCY);

        return $fmt->formatCurrency($price, $this->getCurrency()->getCode());
    }

    protected function getLocale(): string
    {
        return $this->getRequest()?->getLocale() ?? $this->locale;
    }

    protected function getCurrency(): Currency
    {
        return $this->currencyProvider->getCurrency();
    }

    protected function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}