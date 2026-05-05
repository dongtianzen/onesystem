(function (Drupal, once) {
  Drupal.behaviors.obBarrio = {
    attach(context) {
      once('ob-barrio-init', 'body', context).forEach(function () {
        console.log('OB Barrio initialized');
      });
    }
  };
})(Drupal, once);
