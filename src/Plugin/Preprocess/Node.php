<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "node" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("node")
 */
class Node extends PreprocessBase {
  use PreprocessEntityTrait;

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    // Default variables.
    $this->variables['title_prefix'] = isset($this->variables['title_prefix']) ? $this->variables['title_prefix'] : '';
    $this->variables['title_suffix'] = isset($this->variables['title_suffix']) ? $this->variables['title_suffix'] : '';
    $this->variables['contextual_links'] = '';
    if (!empty($this->variables['title_suffix']['contextual_links'])) {
      $this->variables['contextual_links'] = $this->variables['title_suffix']['contextual_links'];
      unset($this->variables['title_suffix']['contextual_links']);
    }
    $this->setTitleTag('h2');
    // Should the node title be linked.
    $this->linkTitle();
    // Should the node title be shown.
    $this->showTitle();
    // Should the node title show up after the content.
    $this->showTitleBefore();
  }

  /**
   * Set element as link to title.
   */
  protected function setAsLink() {
    $this->unlinkTitle();
    $this->variables['tag'] = 'a';
    $this->variables['attributes']['href'] = $this->variables['url'];
    $this->variables['attributes']['rel'] = 'bookmark';
  }

  /**
   * Set the content tag.
   */
  protected function setTitleTag($tag = 'div') {
    $this->variables['title_tag'] = $tag;
  }

  /**
   * Set the title attributes.
   */
  protected function setTitleAttributes($attributes = []) {
    $this->variables->setAttributes($attributes, 'title_attributes');
  }

  /**
   * Show the node title.
   */
  protected function showTitle() {
    $this->variables['title_hide'] = FALSE;
  }

  /**
   * Hide the node title.
   */
  protected function hideTitle() {
    $this->variables['title_hide'] = TRUE;
  }

  /**
   * Link node title.
   */
  protected function linkTitle() {
    $this->variables['title_as_link'] = TRUE;
  }

  /**
   * Unlink node title.
   */
  protected function unlinkTitle() {
    $this->variables['title_as_link'] = FALSE;
  }

  /**
   * Show the title after the content.
   */
  protected function showTitleBefore() {
    $this->variables['title_after'] = FALSE;
  }

  /**
   * Show the title after the content.
   */
  protected function showTitleAfter() {
    $this->variables['title_after'] = TRUE;
  }

}
