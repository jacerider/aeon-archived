<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "exo_site_settings" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("exo_site_settings")
 */
class ExoSiteSettings extends PreprocessBase {
  use PreprocessEntityTrait;

}
