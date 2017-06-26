<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "taxonomy_term" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("taxonomy_term")
 */
class TaxonomyTerm extends PreprocessBase {
  use PreprocessEntityTrait;

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $this->unlinkTitle();
    $this->setTitleTag('h2');
    $this->showTitle();
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
   * Show the taxonomy term title.
   */
  protected function showTitle() {
    $this->variables['title_show'] = FALSE;
  }

  /**
   * Hide the taxonomy term title.
   */
  protected function hideTitle() {
    $this->variables['title_hide'] = TRUE;
  }

  /**
   * Link taxonomy term title.
   */
  protected function linkTitle() {
    $this->variables['title_as_link'] = TRUE;
  }

  /**
   * Unlink taxonomy term title.
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
