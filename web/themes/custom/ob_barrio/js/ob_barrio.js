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

  Drupal.behaviors.obLangDropdown = {
    attach: function (context, settings) {
      once('ob-lang-dropdown', '.ob-lang-switcher nav.links', context)
        .forEach(function (nav) {

          var spans = nav.querySelectorAll('span.nav-link');
          if (spans.length < 2) return;

          var activeSpan = null;
          var otherSpans = [];
          spans.forEach(function (span) {
            var a = span.querySelector('a.language-link');
            if (a && a.classList.contains('is-active')) {
              activeSpan = span;
            } else {
              otherSpans.push(span);
            }
          });

          if (!activeSpan) {
            activeSpan = spans[0];
            otherSpans = Array.from(spans).slice(1);
          }

          function getLabel(span) {
            var hl = span.getAttribute('hreflang');
            if (hl === 'zh-hans') return '中文';
            if (hl === 'en') return 'EN';
            var a = span.querySelector('a');
            return a ? a.textContent.trim() : hl;
          }

          function getFlag(span) {
            var hl = span.getAttribute('hreflang');
            if (hl === 'zh-hans') return '🇨🇳';
            if (hl === 'en') return '🇬🇧';
            return '🌐';
          }

          var dropdownHtml = '<div class="ob-lang-dd dropdown">'
            + '<button class="ob-lang-dd__btn dropdown-toggle" type="button"'
            + ' data-bs-toggle="dropdown"'
            + ' data-bs-display="static"'
            + ' aria-expanded="false">'
            + '<span class="ob-lang-dd__flag">' + getFlag(activeSpan) + '</span>'
            + '<span class="ob-lang-dd__label">' + getLabel(activeSpan) + '</span>'
            + '</button>'
            + '<ul class="dropdown-menu dropdown-menu-end ob-lang-dd__menu">';

          otherSpans.forEach(function (span) {
            var a = span.querySelector('a');
            if (!a) return;
            dropdownHtml += '<li>'
              + '<a class="dropdown-item ob-lang-dd__item" href="' + a.getAttribute('href') + '">'
              + getFlag(span) + ' ' + getLabel(span)
              + '</a></li>';
          });

          dropdownHtml += '</ul></div>';

          nav.innerHTML = dropdownHtml;
        });
    }
  };

})(jQuery, Drupal, once);
