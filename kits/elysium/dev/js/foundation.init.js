/**
 * @file
 * Initialize Foundation.
 */

(function ($, document) {

  'use strict';

  Drupal.behaviors.foundationInit = {
    attach: function (context) {
      $(context).once('foundation-init').foundation();
    }
  };

}(jQuery, document));
