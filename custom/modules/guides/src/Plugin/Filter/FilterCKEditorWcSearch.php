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
      $widget = '<form accept-charset="UTF-8" action="https://lib.unb.ca/worldcat-search-helper" method="post" class="alert alert-info" target="_blank">
        <div class="form-group mb-0">
          <fieldset>
            <div class="form-row mb-2 ml-1">
              <legend class="custom-legend mb-1 mr-3">
                Search UNB WorldCat:
              </legend>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0 font-weight-bold">
                <input checked="checked" class="custom-control-input" id="scope_UNBLibraries_WCD" name="scope" type="radio" value="wz:66413">
                <label class="custom-control-label" for="scope_UNBLibraries_WCD"><span class="sr-only">Search </span>UNB Libraries</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0 font-weight-bold">
                <input class="custom-control-input" id="scope_worldwide_WCD" name="scope" type="radio" value="">
                <label class="custom-control-label" for="scope_worldwide_WCD"><span class="sr-only">Search </span>Libraries Worldwide</label>
              </div>
            </div>
          </fieldset>
          <div class="form-row">
            <div class="col-md-5 mb-2">
              <label class="sr-only" for="queryString_WCD">
                Search for:
              </label>
              <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-search"></i></div>
                </div>
                <input class="form-control" id="queryString_WCD" name="queryString" placeholder="Enter search terms" type="search" required="">
              </div>
            </div>
            <div class="col-md-5 mb-2 input-group flex-nowrap">
              <label class="sr-only" for="searchIndex_WCD">
                Search index
              </label>
               <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fas fa-server"></i>
                </span>
              </div>
              <select class="form-control" id="searchIndex_WCD" name="searchIndex" tabindex="-1">
                <option value="kw">keyword</option>
                <option value="ti">title</option>
                <option value="au">author</option>
                <option value="nu">call number</option>
                <option value="tj">journal title</option>
                <option value="su">subject</option>
              </select>
            </div>
             <div class="col-md-2 mb-2">
              <button class="btn btn-primary" id="search_WCD" title="Search" type="submit">GO</button>
            </div>
          </div>
          <div class="form-row ml-1 mb-2">
            <div class="form-check form-check-inline"><span class="font-weight-bold">Limit to:</span>&nbsp;
              <input class="form-check-input" type="radio" value="" id="formatAll" name="format" checked>
              <label class="form-check-label" for="formatAll">All</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" value="Book" id="formatBooks" name="format">
              <label class="form-check-label" for="formatBooks">Books Only</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" value="Book::book_digital" id="formateBooks" name="format">
              <label class="form-check-label" for="formateBooks">eBooks Only</label>
            </div>
          </div>
          <div class="p-2">
            <a href="https://unb.on.worldcat.org/advancedsearch">Advanced Search</a> |
            <a href="https://lib.unb.ca/about/loc_call">Locations Guide</a> |
            <a href="https://lib.unb.ca/worldcat/unb-worldcat-frequently-asked-questions" title="Using WorldCat Discovery"><i class="fa fa-question-circle"></i> Help</a>
          </div>
        </div>
      </form>';
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
