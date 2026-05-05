/**
 * @file
 * NovaCast theme behaviors — mobile menu, sticky header, smooth scroll.
 */

(function ($, Drupal, once) {
  'use strict';

  /**
   * Mobile menu toggle.
   *
   * Toggles .nav-open on the site header; manages aria-expanded;
   * closes on outside click or Escape key.
   */
  Drupal.behaviors.mobileMenu = {
    attach: function (context) {
      once('mobile-menu', '.nav-toggle', context).forEach(function (toggle) {
        const header  = document.querySelector('.site-header');
        const overlay = document.querySelector('.mobile-nav-overlay');

        function openMenu() {
          header.classList.add('nav-open');
          toggle.setAttribute('aria-expanded', 'true');
          document.body.style.overflow = 'hidden';
          if (overlay) overlay.setAttribute('aria-hidden', 'false');
        }

        function closeMenu() {
          header.classList.remove('nav-open');
          toggle.setAttribute('aria-expanded', 'false');
          document.body.style.overflow = '';
          if (overlay) overlay.setAttribute('aria-hidden', 'true');
          // Also collapse any open dropdowns.
          document.querySelectorAll('.primary-nav__item.dropdown-open').forEach(function (item) {
            item.classList.remove('dropdown-open');
          });
        }

        toggle.addEventListener('click', function () {
          if (header.classList.contains('nav-open')) {
            closeMenu();
          } else {
            openMenu();
          }
        });

        // Close on Escape key.
        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && header.classList.contains('nav-open')) {
            closeMenu();
            toggle.focus();
          }
        });

        // Close on overlay click.
        if (overlay) {
          overlay.addEventListener('click', closeMenu);
        }

        // Mobile dropdown sub-menus (tap to expand).
        const dropdownTriggers = document.querySelectorAll(
          '.primary-nav__link[aria-expanded]'
        );
        dropdownTriggers.forEach(function (trigger) {
          trigger.addEventListener('click', function (e) {
            if (window.innerWidth <= 899) {
              e.preventDefault();
              const parent = trigger.closest('.primary-nav__item');
              const isOpen = parent.classList.contains('dropdown-open');

              // Close siblings.
              document.querySelectorAll('.primary-nav__item.dropdown-open').forEach(function (item) {
                if (item !== parent) item.classList.remove('dropdown-open');
              });

              parent.classList.toggle('dropdown-open', !isOpen);
              trigger.setAttribute('aria-expanded', String(!isOpen));
            }
          });
        });

        // Re-enable scroll and close on window resize past breakpoint.
        window.addEventListener('resize', function () {
          if (window.innerWidth > 899 && header.classList.contains('nav-open')) {
            closeMenu();
          }
        });
      });
    }
  };

  /**
   * Sticky header shadow on scroll.
   *
   * Adds .scrolled to .site-header when scrollY > 40px.
   */
  Drupal.behaviors.stickyHeader = {
    attach: function (context) {
      once('sticky-header', 'body', context).forEach(function () {
        const header = document.querySelector('.site-header');
        if (!header) return;

        function updateScrolled() {
          header.classList.toggle('scrolled', window.scrollY > 40);
        }

        updateScrolled();
        window.addEventListener('scroll', updateScrolled, { passive: true });
      });
    }
  };

  /**
   * Smooth scrolling for in-page anchor links.
   *
   * Only applies to links starting with # that target an existing element.
   */
  Drupal.behaviors.smoothScroll = {
    attach: function (context) {
      once('smooth-scroll', 'a[href^="#"]', context).forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
          const targetId = anchor.getAttribute('href').slice(1);
          if (!targetId) return;

          const target = document.getElementById(targetId);
          if (!target) return;

          e.preventDefault();

          const headerHeight = document.querySelector('.site-header')
            ? document.querySelector('.site-header').offsetHeight
            : 0;

          const targetPos = target.getBoundingClientRect().top + window.scrollY - headerHeight - 16;

          window.scrollTo({ top: targetPos, behavior: 'smooth' });
          target.setAttribute('tabindex', '-1');
          target.focus({ preventScroll: true });
        });
      });
    }
  };

}(jQuery, Drupal, once));
