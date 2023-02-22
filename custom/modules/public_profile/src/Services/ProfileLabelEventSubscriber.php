<?php

namespace Drupal\public_profile\Services;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\profile\Event\ProfileLabelEvent;
use Drupal\profile\Event\ProfileEvents;

/**
 * Listens to the profile label event.
 */
class ProfileLabelEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      ProfileEvents::PROFILE_LABEL => 'setLabel',
    ];
  }

  /**
   * This method is called when the setLabel is dispatched.
   *
   * @param \Drupal\profile\Event\ProfileLabelEvent $event
   *   The dispatched event.
   */
  public function setLabel(ProfileLabelEvent $event) {
    $profile = $event->getProfile();
    $account = $profile->getOwner();
    $label = implode(' ', [
      $account->field_first_name->value,
      $account->field_last_name->value,
    ]);

    $event->setLabel($label);
  }

}
