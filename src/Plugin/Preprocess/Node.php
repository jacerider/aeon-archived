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

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    // Should the node title be linked.
    $this->variables['title_as_link'] = TRUE;
    // Should the node title be shown.
    $this->variables['title_hide'] = FALSE;
    // Should the node title show up after the content.
    $this->variables['title_after'] = FALSE;
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
   * Show the node title.
   */
  protected function showTitle() {
    $this->variables['title_show'] = FALSE;
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
