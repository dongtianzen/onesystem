/**
 * @file
 * @see https://www.sitepoint.com/understanding-bootstrap-modals/
 */

/**
 * @see https://stackoverflow.com/questions/18876537/jquery-click-events-not-firing-within-angularjs-templates/18876600
 * @see https://stackoverflow.com/questions/38771949/jquery-click-event-does-not-trigger-in-angularjs-dom
 * Since your "#ID" probably isn't available on DOM ready
 * rather it is pushed into the DOM by Angular when it loads template.html
 * you need to use jQuery's event delegation
 * instead of using jQuery("#ID").click(function() {}):
 */

// make global variable
var userUid = jQuery(this).data('useruid');

jQuery(document).on("click", "#modalPageAnchor", function() {
  userUid = jQuery(this).data('useruid');

  jQuery("#speakerModalTab").modal();

  jQuery('#speakerModalTab').on('shown.bs.modal', function(e) {
    // jQuery('.tab-header-text').text(e.currentTarget.classList[2]);
    // var userUid = jQuery(this).data('useruid');

    var jsonFileUrl = drupalSettings.path.baseUrl + 'dashpage/modal/speakerpop/json/' + userUid;

    /**
     * axios.get
     *
     * self.options = response.data.options;
     * self.columns = response.data.columns;
     * self.data    = response.data.data;
     */
    axios.get(jsonFileUrl).then((response) => {
      var jsonData = response.data.content;

      jQuery('.modal-title-speaker-name').html(jsonData['speaker']['displayName']);

      jQuery('.tab-header-html-content-ytd').html(jsonData['ytd']['header']);
      jQuery('.tab-header-html-content-all').html(jsonData['all']['header']);

      jQuery('.tab-header-html-content-ytd .tab-body-html-content-program').html(jsonData['ytd']['program']);
      jQuery('.tab-header-html-content-all .tab-body-html-content-program').html(jsonData['all']['program']);

      jQuery('.tab-header-html-content-ytd .tab-body-html-content-location').html(jsonData['ytd']['location']);
      jQuery('.tab-header-html-content-all .tab-body-html-content-location').html(jsonData['all']['location']);
    })
    .catch((error) => {
      console.log(error);
    });

    // alert('Modal is successfully shown!');
  });


  // jQuery('#speakerModalTab').on('show.bs.modal', function(e) {
    // alert('Modal is successfully show!');
    // jQuery('.tab-header-text').text('show');
  // });

  // jQuery('#speakerModalTab').on('hidden.bs.modal', function(e) {
  //   alert('Modal is successfully hidden!');
  // });
});
