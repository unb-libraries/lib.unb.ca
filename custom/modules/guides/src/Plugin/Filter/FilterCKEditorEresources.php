<?php

namespace Drupal\guides\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;

/**
 * Provides a filter to convert eresources tags to resource lists.
 *
 * @Filter(
 *   id = "filter_guides_ckeditor_eresources",
 *   title = @Translation("Convert eresources tags to resource lists"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 */
class FilterCKEditorEresources extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Class constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
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
      $container->get('entity_type.manager'),
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

    if (strpos($text, '<eresources') !== FALSE) {
      $document = Html::load($text);
      $xpath = new \DOMXPath($document);

      foreach ($xpath->query('//eresources') as $node) {
        $ids = $node->nodeValue;
        if (empty($ids)) {
          $info = $document->createElement('div');
          $info->appendChild($document->createTextNode('e-Resources List: No resources selected.'));
          $node->parentNode->replaceChild($info, $node);
        }
        else {
          $storage = $this->entityTypeManager->getStorage('eresources_record');
          $query = $storage->getQuery();
          $resources = $query->condition('id', $ids, 'IN')->execute();

          if (!empty($resources)) {
            $options = [];

            $keyresources = $node->getAttribute('keyresources');
            $options['keyresources'] = (is_numeric($keyresources) ? $keyresources : 999);
            $options['headings'] = $node->getAttribute('noheadings') ? FALSE : TRUE;
            $options['searchbox'] = $node->getAttribute('searchbox') ? TRUE : FALSE;

            $listHtml = HTML::load($this->buildResourceList($resources, $options));
            $list = $document->importNode($listHtml->documentElement, TRUE);

            $node->parentNode->replaceChild($list, $node);
          }
        }
      }

      $text = Html::serialize($document);

      $result->setProcessedText($text);
    }

    return $result;
  }

  /**
   * Build an HTML list of e-Resource info.
   *
   * @param array $resources
   *   Array of e-Resources records.
   * @param array $options
   *   Array of options for rendering the list.
   *
   * @return string
   *   Rendered HTML.
   */
  protected function buildResourceList(array $resources, array $options) {
    $render = [
      '#theme' => 'ckeditor-eresources',
      '#resources' => $resources,
      '#options' => $options,
    ];
    return $this->renderer->render($render);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('e-Resources tags are converted to HTML.');
  }

}
