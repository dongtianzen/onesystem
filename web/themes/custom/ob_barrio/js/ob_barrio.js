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

          var dropdown = document.createElement('div');
          dropdown.className = 'ob-lang-dd dropdown';

          var button = document.createElement('button');
          button.className = 'ob-lang-dd__btn dropdown-toggle';
          button.type = 'button';
          button.setAttribute('data-bs-toggle', 'dropdown');
          button.setAttribute('aria-expanded', 'false');

          var flag = document.createElement('span');
          flag.className = 'ob-lang-dd__flag';
          flag.textContent = getFlag(activeSpan);

          var label = document.createElement('span');
          label.className = 'ob-lang-dd__label';
          label.textContent = getLabel(activeSpan);

          button.appendChild(flag);
          button.appendChild(label);

          var menu = document.createElement('ul');
          menu.className = 'dropdown-menu dropdown-menu-end ob-lang-dd__menu';

          otherSpans.forEach(function (span) {
            var originalLink = span.querySelector('a');
            if (!originalLink) return;

            var item = document.createElement('li');
            var link = originalLink.cloneNode(false);
            link.className = 'dropdown-item ob-lang-dd__item';
            link.textContent = getFlag(span) + ' ' + getLabel(span);

            item.appendChild(link);
            menu.appendChild(item);
          });

          dropdown.appendChild(button);
          dropdown.appendChild(menu);
          nav.replaceChildren(dropdown);

          if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            bootstrap.Dropdown.getOrCreateInstance(button, {
              autoClose: true
            });
          }
          else {
            button.addEventListener('click', function (e) {
              e.preventDefault();
              e.stopPropagation();
              var isOpen = menu.classList.toggle('show');
              button.setAttribute('aria-expanded', String(isOpen));
            });

            document.addEventListener('click', function (e) {
              if (!dropdown.contains(e.target)) {
                menu.classList.remove('show');
                button.setAttribute('aria-expanded', 'false');
              }
            });
          }
        });
    }
  };


  // Reading time estimate — populates .ob-reading-time spans.
  Drupal.behaviors.obReadingTime = {
    attach: function (context, settings) {
      once('ob-reading-time', '.ob-node-content', context).forEach(function (content) {
        var slot = content.closest('article')
          ? content.closest('article').querySelector('.ob-reading-time')
          : null;
        if (!slot) return;

        var text  = content.textContent || content.innerText || '';
        var words = text.trim().split(/\s+/).filter(Boolean).length;
        var mins  = Math.max(1, Math.round(words / 220));
        slot.textContent = mins + (mins === 1 ? ' min read' : ' min read');
      });
    }
  };

  // Smooth anchor links — account for sticky header height.
  Drupal.behaviors.obAnchorScroll = {
    attach: function (context, settings) {
      once('ob-anchor', 'a[href^="#"]:not([href="#"])', context).forEach(function (a) {
        a.addEventListener('click', function (e) {
          var id      = a.getAttribute('href').slice(1);
          var target  = document.getElementById(id);
          if (!target) return;
          e.preventDefault();
          var header  = document.getElementById('site-header');
          var offset  = header ? header.offsetHeight + 16 : 80;
          var top     = target.getBoundingClientRect().top + window.scrollY - offset;
          window.scrollTo({ top: top, behavior: 'smooth' });
          history.pushState(null, '', '#' + id);
        });
      });
    }
  };

})(jQuery, Drupal, once);
