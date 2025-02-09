(function (Drupal) {

  'use strict';

  Drupal.behaviors.wanbo = {
    attach: function (context, settings) {
      console.log(888);
      var navbarToggler = context.querySelector('.navbar-toggler.navbar-toggler-right');

      if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
          var targetId = this.getAttribute('data-target');
          var targetElement = context.querySelector(targetId);

          if (targetElement) {
            targetElement.classList.toggle('show');
          }
        });
      }
    }
  };

})(Drupal);
