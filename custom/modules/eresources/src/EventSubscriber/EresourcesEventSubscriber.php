<?php

namespace Drupal\eresources\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
    $events[KernelEvents::RESPONSE][] = ['checkProxyIpOnResponse'];
    return $events;
  }

  /**
   * Checks if user is coming from the proxy for protected pages.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The event.
   */
  public function checkProxyIpOnResponse(ResponseEvent $event) {
    // Don't bother proceeding on sub-requests.
    if (!$event->isMasterRequest()) {
      return;
    }

    $request = $event->getRequest();
    $path = $request->getRequestUri();

    // Eresources protected paths are only viewable by anonymous users
    // if they access them via the proxy.
    if (preg_match('/^\/eresources\/protected\//', $path)) {
      $response = $event->getResponse();
      $ip = empty($_SERVER["HTTP_X_REAL_IP"]) ? \Drupal::request()->getClientIp() : $_SERVER["HTTP_X_REAL_IP"];
      if ($this->currentUser->isAnonymous() && !preg_match('/^131\.202\.38\./', $ip)) {
        $url = Url::fromRoute('system.403');
        $response = new RedirectResponse($url->toString());
      }
      $response->setMaxAge(0);
      $event->setResponse($response);
      \Drupal::service('page_cache_kill_switch')->trigger();
    }
  }

}
