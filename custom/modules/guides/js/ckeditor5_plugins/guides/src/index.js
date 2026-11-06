/**
 * @file
 * CKEditor 5 plugins provided by the guides module.
 */

import Eresources from './eresources';
import createPlaceholderWidget from './placeholderwidget';

const WcSearch = createPlaceholderWidget({
  pluginName: 'WcSearch',
  modelName: 'guidesWcSearch',
  cssClass: 'wc-search',
  imgSrc: '/modules/custom/guides/img/wc-search-placeholder.png',
  toolbarItem: 'wcSearch',
  label: 'UNB WorldCat',
});

const SciFree = createPlaceholderWidget({
  pluginName: 'SciFree',
  modelName: 'guidesSciFree',
  cssClass: 'scifree',
  imgSrc: '/modules/custom/guides/img/scifree-placeholder.png',
  toolbarItem: 'sciFree',
  label: 'SciFree',
});

export default {
  Eresources,
  WcSearch,
  SciFree,
};
