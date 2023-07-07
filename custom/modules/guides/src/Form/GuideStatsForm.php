<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\guides\Form\StatsFormBase;

/**
 * Display Google Analytics stats data.
 */
class GuideStatsForm extends StatsFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormType() {
    return 'guide';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, EntityInterface $guide = NULL) {
    $this->setObject($guide);

    $form += parent::buildForm($form, $form_state);

    $form['#title'] = 'Statistics for ' . $guide->label();
    return $form;
  }

}
