<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\guides\Form\StatsFormBase;

/**
 * Display Google Analytics stats data.
 */
class GuideCategoryStatsForm extends StatsFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormType() {
    return 'guide_category';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, EntityInterface $guide_category = NULL) {
    $this->setObject($guide_category);

    $form += parent::buildForm($form, $form_state);

    $form['#title'] = 'Statistics for ' . $guide_category->label();
    return $form;
  }

}
