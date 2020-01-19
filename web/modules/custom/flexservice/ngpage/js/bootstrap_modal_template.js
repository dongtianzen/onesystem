/**
 *
 */

jQuery(document).on("click", "#modal-page-anchor", function() {
  var userUid = jQuery(this).data('useruid');
  console.log(userUid);

  jQuery("#bootstrap-modal-link").modal();

  // 'shown.bs.modal'
  jQuery('#bootstrap-modal-link').on('shown.bs.modal', function(e) {
    // jQuery('.tab-header-text').text(e.currentTarget.classList[2]);

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
      console.log(userUid);
      console.log(jsonData['speaker']['displayName']);
      jQuery('.modal-title-speaker-name').html(jsonData['speaker']['displayName']);
      jQuery('.modal-title').html(jsonData['speaker']['displayName']);

      jQuery('.tab-header-html-content-ytd').html(jsonData['ytd']['header']);
      jQuery('.tab-header-html-content-all').html(jsonData['all']['header']);

      // jQuery('.tab-header-html-content-ytd .tab-body-html-content-program').html(jsonData['ytd']['program']);
      // jQuery('.tab-header-html-content-all .tab-body-html-content-program').html(jsonData['all']['program']);

      // jQuery('.tab-header-html-content-ytd .tab-body-html-content-location').html(jsonData['ytd']['location']);
      // jQuery('.tab-header-html-content-all .tab-body-html-content-location').html(jsonData['all']['location']);
    })
    .catch((error) => {
      console.log(error);
    });

    // alert('Modal is successfully shown!');
  });

});
