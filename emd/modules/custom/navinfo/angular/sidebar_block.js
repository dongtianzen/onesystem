/**
 * sidebar collapse
 */
jQuery(document).ready(function() {

  var pathArg = drupalSettings.path.currentPath.split('/');

  /**
   *
   */
  var allMenuLink = jQuery(".sidebar-nav ul .metismenu-item-link a");
  var activeMenuLink = null;

  for (var i = 0; i < allMenuLink.length; i++) {

    if(allMenuLink[i].href.includes(pathArg[0])) {
      if(allMenuLink[i].href.includes(pathArg[1])) {
        if(allMenuLink[i].href.includes(pathArg[2])) {
          if(allMenuLink[i].href.includes(pathArg[3])) {

            var localPathArray = allMenuLink[i].href.split( '/' );
            // var localPathName = drupalSettings.path.baseUrl + drupalSettings.path.currentPath;

            // get base url array
            // var localBaseUrlArg = drupalSettings.path.baseUrl.split('/');
            // get rid of empty elemtent in base url array
            // localBaseUrlArg = localBaseUrlArg.filter(function(i){ return i != '';});

            var localPathName = '';

            // count start from the base url
            for (var i = 3; i < localPathArray.length; i++) {
              localPathName = localPathName + '/' + localPathArray[i];
            }

            var activeMenuLink = jQuery(".sidebar-nav ul .metismenu-item-link a[href='" + localPathName + "']");

            break;

          }
        }
      }
    }
  }

  if (activeMenuLink) {
    activeMenuLink.addClass("active-menu");
  }

  //
  jQuery(window).scroll(function(){
    // resize sidebar height to fit page
    var windowHeight = jQuery(document).height();
    var offset = jQuery(window).scrollTop();
    jQuery(".sidebar-nav").css("height", windowHeight);
    if (offset > 100) {
      jQuery(window).off('scroll');
    }
  });


  jQuery('[data-toggle=offcanvas]').click(function() {
    jQuery('.row-offcanvas').toggleClass('active');
    jQuery('.sidebar-nav-item').toggleClass('in').toggleClass('hidden-xs').toggleClass('visible-xs');

    jQuery('.highlighted').parent('section').toggleClass("maximize");
    jQuery('aside').toggleClass("minimize");
    jQuery('.metismenu li a').parent().has('ul').children('a').toggleClass('has-arrow');
    jQuery('.metismenu li ul li a').parent().has('ul').children('a').toggleClass('has-arrow');
    jQuery('.metismenu').toggleClass('mini-menu');
    jQuery('.sidebar-site-logo').toggleClass('sidebar-nav-logo-img-small sidebar-nav-logo-img-big');
    jQuery(".sidebar-nav-icon-number").toggle();
  });

  jQuery(document).click(function(event) {
    var clickover = jQuery(event.target);
    var clickedElement = jQuery('.metismenu li ul');
    if (jQuery('.highlighted').parent('section').hasClass('maximize')) {
      if ( !(clickover.parent().hasClass('metismenu-item-link') || (clickover.parent().parent().hasClass('metismenu-item-link'))) ) {
        clickedElement.collapse("hide");
        clickedElement.attr("aria-expanded","false");
        clickedElement.parent().removeClass("active");
        clickedElement.siblings("a").attr("aria-expanded","false");
      }
    }
  });
});
