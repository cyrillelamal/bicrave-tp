<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Inject the JSON request parameters into the corresponding parameter bag.
 */
class JsonRequestSubscriber implements EventSubscriberInterface
{
    public const HEADER = 'Content-Type';
    public const CONTENT_TYPE = 'application/json';

    public function replaceRequestParameters(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (self::CONTENT_TYPE === $request->headers->get(self::HEADER)) {
            try {
                $parameters = $request->toArray();

                $bag = $request->isMethod(Request::METHOD_GET)
                    ? $request->query
                    : $request->request;

                $bag->replace($parameters);
            } catch (JsonException) {
                throw new BadRequestHttpException();
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'replaceRequestParameters',
        ];
    }
}
