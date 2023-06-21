(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.jumpMenu = {
    attach: function (context, settings) {

      var s = null;
      var gumShoe = null;

      once('radicati-jumpmenu', '.radicati-jumpmenu', context).forEach(function(item) {
        // The nav wrapper
        var $nav = $('.radicati-jumpmenu__menu-wrapper', item);

        // If the body has any padding on the top, we need to add that padding
        // to the jump menu.
        var bodyPaddingTop = parseInt($('body').css('padding-top'));
        var stickyBitsOffset = 0;

        if (bodyPaddingTop > 0) {
          stickyBitsOffset = bodyPaddingTop;
        }

        s = stickybits($nav, {
          useStickyClasses: true,
          stickyBitStickyOffset: stickyBitsOffset,
        });

        // Menu was found and made into a sticky menu, now load Gumshoe
        gumShoe = new Gumshoe(".radicati-jumpmenu a");

      });

    }
  }

})(jQuery, Drupal);