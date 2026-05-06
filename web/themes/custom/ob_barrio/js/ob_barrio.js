(function ($, Drupal, once) {
  'use strict';

  // Sticky header shadow on scroll.
  Drupal.behaviors.obStickyHeader = {
    attach: function (context, settings) {
      once('ob-sticky', '#site-header', context).forEach(function (el) {
        window.addEventListener('scroll', function () {
          el.classList.toggle('scrolled', window.scrollY > 40);
        });
      });
    }
  };

  // Mobile menu keyboard accessibility.
  Drupal.behaviors.obMobileMenu = {
    attach: function (context, settings) {
      once('ob-nav', '.navbar-toggler', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          var expanded = btn.getAttribute('aria-expanded') === 'true';
          btn.setAttribute('aria-expanded', String(!expanded));
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            var collapse = document.querySelector('.navbar-collapse.show');
            if (collapse && typeof bootstrap !== 'undefined') {
              bootstrap.Collapse.getInstance(collapse).hide();
              btn.setAttribute('aria-expanded', 'false');
              btn.focus();
            }
          }
        });
      });
    }
  };

})(jQuery, Drupal, once);
