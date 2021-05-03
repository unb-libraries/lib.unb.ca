<?php

namespace Drupal\eresources\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class EntityTypeSubscriber.
 */
class EresourcesEventSubscriber implements EventSubscriberInterface {

  /**
   * The current user.
   *
   * @var AccountProxy
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccountInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['checkProxyIp'];
    return $events;
  }

  /**
   * Checks if user is coming from the proxy for protected pages.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The event.
   */
  public function checkProxyIp(GetResponseEvent $event) {
    $request = $event->getRequest();
    $path = $request->getRequestUri();
    $ip = $request->getClientIp();
    if ($this->currentUser->isAnonymous()
      && preg_match('/^\/eresources\/protected\//', $path)
      && !preg_match('/^131\.202\.38./', $ip)) {
      $url = Url::fromRoute('system.403');
      $event->setResponse(new RedirectResponse($url->toString()));
    }
  }

}
