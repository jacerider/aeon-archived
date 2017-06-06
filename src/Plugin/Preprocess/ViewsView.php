<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Component\Utility\Html;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "views_view" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("views_view")
 */
class ViewsView extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $view = $variables['view'];
    $view_array = $variables['view_array'];

    $variables['title_tag'] = 'div';
    $variables['exposed_tag'] = '';
    $variables['exposed_attributes'] = [];
    $variables['content_before'] = [];
    $variables['content_after'] = [];
    $variables['exposed_before'] = [];
    $variables['exposed_after'] = [];

    if (isset($view_array['#title']) && $view_array['#display_id'] == 'default') {
      // Allow title insertion even for 'default' views.
      $variables['title'] = $view_array['#title'];
    }

    if ($view->ajaxEnabled()) {
      $variables->addClass('js-view-dom-id-' . $variables['dom_id']);
      $variables->addClass('view-' . Html::getClass($variables['id']));
      $variables->addClass('view-id-' . $variables['id']);
      $variables->addClass('view-display-id-' . $variables['display_id']);
    }
    if (empty($variables->getClasses())) {
      $variables->removeAttribute('class');
    }

  }

  /**
   * Set the exposed form above the view content.
   */
  protected function setExposedAboveContent() {
    if (!empty($this->variables['exposed'])) {
      $this->variables['content_before']['exposed'] = $this->variables['exposed'];
      $this->variables['exposed'] = [];
    }
  }

  /**
   * Set the content tag.
   */
  protected function setRowsTag($tag = 'div', $attributes = []) {
    $this->variables['rows_tag'] = $tag;
    $this->setRowsAttributes($attributes);
  }

  /**
   * Set the content attributes.
   */
  protected function setRowsAttributes($attributes = []) {
    $this->variables->setAttributes($attributes, 'rows_attributes');
  }

  /**
   * Set the content tag.
   */
  protected function setExposedTag($tag = 'div', $attributes = []) {
    $this->variables['exposed_tag'] = $tag;
    $this->setExposedAttributes($attributes);
  }

  /**
   * Set the content attributes.
   */
  protected function setExposedAttributes($attributes = []) {
    $this->variables->setAttributes($attributes, 'exposed_attributes');
  }

  /**
   * Set the attachment before tag.
   */
  protected function setAttachmentBeforeTag($tag = 'div', $attributes = []) {
    $this->variables['attachment_before_tag'] = $tag;
    $this->setAttachmentBeforeAttributes($attributes);
  }

  /**
   * Set the attachment before attributes.
   */
  protected function setAttachmentBeforeAttributes($attributes = []) {
    $this->variables->setAttributes($attributes, 'attachment_before_attributes');
  }

  /**
   * Set the attachment after tag.
   */
  protected function setAttachmentAfterTag($tag = 'div', $attributes = []) {
    $this->variables['attachment_after_tag'] = $tag;
    $this->setAttachmentBeforeAttributes($attributes);
  }

  /**
   * Set the attachment after attributes.
   */
  protected function setAttachmentAfterAttributes($attributes = []) {
    $this->variables->setAttributes($attributes, 'attachment_after_attributes');
  }

  /**
   * Load the plugin build for a facet.
   */
  protected function facetBuild($id, $label = '') {
    $block_manager = \Drupal::service('plugin.manager.block');
    $config = [];
    $plugin_block = $block_manager->createInstance('facet_block:' . $id, $config);
    if (!$plugin_block) {
      return [];
    }
    $classes = [];
    $classes[] = Html::getClass($id);
    $build = $plugin_block->build();
    $build['#attributes']['class'][] = 'block';
    $build['#attributes']['class'][] = 'facet';
    $build['#attributes']['class'][] = Html::getClass($id);
    $attribute = new Attribute($build['#attributes']);
    $build['#prefix'] = '<div' . $attribute . '><h2>' . $label . '</h2>';
    $build['#suffix'] = '</div>';
    return $build;
  }

}
