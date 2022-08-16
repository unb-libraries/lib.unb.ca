<?php

namespace Drupal\guides\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter to convert wc-search tags to a full widget.
 *
 * @Filter(
 *   id = "filter_guides_ckeditor_wc-search",
 *   title = @Translation("Replace UNB WorldCat search box placeholder with the full widget"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 */
class FilterCKEditorWcSearch extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $result = new FilterProcessResult($text);

    if (strpos($text, '<div class="wc-search"') !== FALSE) {
      $widget = file_get_contents(drupal_get_path('module', 'guides') . '/templates/ckeditor/wc-search.html.twig');
      $text = str_replace('<div class="wc-search"><img src="/modules/custom/guides/img/wc-search-placeholder.png" /></div>', $widget, $text);
      $result->setProcessedText($text);
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('Replace UNB WorldCat search box placeholder with the full widget');
  }

}
