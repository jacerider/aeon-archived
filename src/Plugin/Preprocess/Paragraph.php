<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "paragraph" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("paragraph")
 */
class Paragraph extends PreprocessBase {

  /**
   * Set the content tag.
   */
  protected function setContentOnly() {
    $this->variables['tag'] = NULL;
  }

}
