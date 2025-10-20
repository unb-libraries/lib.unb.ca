<?php

namespace Drupal\lib_core\Services;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface {

  public function removeXFrameOptions(ResponseEvent $event) {
    $request = $event->getRequest();
    $path = $request->getRequestUri();

    if (preg_match('/^\/lms-widget/', $path)) {
      $response = $event->getResponse();
      $response->headers->remove('X-Frame-Options');
    }
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['removeXFrameOptions', -10];
    return $events;
  }

}
