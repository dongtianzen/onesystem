(function (Drupal) {

  'use strict';

  Drupal.behaviors.wanbo = {
    attach: function (context, settings) {
      document.addEventListener('DOMContentLoaded', function() {
        var navbarToggler = document.querySelector('.navbar-toggler.navbar-toggler-right');

        navbarToggler.addEventListener('click', function() {
          var targetId = this.getAttribute('data-target');
          var targetElement = document.querySelector(targetId);

          if (targetElement) {
            targetElement.classList.toggle('show');
          }
        });
      });
    }
  };

})(Drupal);
