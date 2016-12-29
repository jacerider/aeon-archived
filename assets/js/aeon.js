/**
 * @file
 * Base script that impacts all child themes.
 */

(function ($, document) {

  'use strict';

  Drupal.behaviors.aeonSidebars = {
    originalClasses: '',
    attach: function (context) {
      var self = this;
      var $body = $('body');
      $body.once().each(function () {
        self.originalClasses = $body.attr('class');
      });
      // Reset body classes to original values.
      $body.attr('class', self.originalClasses);
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
