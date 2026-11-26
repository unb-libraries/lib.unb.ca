<?php

namespace Drupal\guides\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
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
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Class constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('renderer')
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
      $render = [
        '#theme' => 'ckeditor-wc-search',
      ];
      $widget = $this->renderer->render($render);
      // Match the placeholder as saved by either CKEditor 4 (<img ... />) or
      // CKEditor 5 (<img ...>).
      $text = preg_replace_callback(
        '#<div class="wc-search">\\s*<img src="/modules/custom/guides/img/wc-search-placeholder\\.png"[^>]*>\\s*</div>#',
        fn() => (string) $widget,
        $text
      );
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
