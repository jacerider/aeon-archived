/**
 * @file
 * Initialize Foundation.
 */

(function ($, document) {

  'use strict';

  Drupal.behaviors.foundationInit = {
    attach: function (context) {
      $(document).once('foundation-init').foundation();
    }
  };

}(jQuery, document));
