/**
 * @file
 * Base script that impacts all child themes.
 */

(function ($, document) {

  'use strict';

  Drupal.behaviors.aeonSidebars = {
    attach: function (context) {
      var self = this;
      var $body = $('body');
      // Reset body classes to original values.
      $body.removeClass('sidebar-first sidebar-second');
      var $sidebars = $('.region.sidebar');
      if ($sidebars.length) {
        var $first = $sidebars.filter('.first');
        var $second = $sidebars.filter('.second');
        var hasFirst = $first.length && $.trim($first.html().replace(/(<([^>]+)>)/ig,"")).length;
        var hasLast = $second.length && $.trim($sidebars.filter('.second').html().replace(/(<([^>]+)>)/ig,"")).length;
        if (hasFirst) {
          $body.addClass('sidebar-first');
        }
        if (hasLast) {
          $body.addClass('sidebar-second');
        }
      }
    }
  };

}(jQuery, document));
