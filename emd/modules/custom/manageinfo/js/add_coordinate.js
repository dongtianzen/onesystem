/**
 * to get longitude and latitude
 */
jQuery(document).ready(function() {
  var country = 'Canada';
  var address = '';
  var postalCode = '';
  var province = '';
  var city = '';
  var completeAddress = '';

  /**
   *
   */
  function insertButton() {
    var buttonId = "edit-field-meeting-check-coordinate";

    var buttonTag = '<button id="edit-field-meeting-check-coordinate" type="button">check coordinate</button>';
    jQuery('#edit-field-meeting-postalcode-0-value').after(buttonTag);

    var buttonStyle = {"background-color":"#0082ad", "border":"none", "color":"white", "text-align":"center", "margin":"10px", "padding":"10px"};
    jQuery("#edit-field-meeting-check-coordinate").css(buttonStyle);

    jQuery("#edit-field-meeting-check-coordinate").hover(function() {
      jQuery(this).css("background-color", "#0082ad");
    },
    function(){
      jQuery(this).css("background-color", "#0093ad");
    });
    jQuery('#edit-field-meeting-check-coordinate').click(getData);
  }

  /**
   *
   */
  function getData() {
    completeAddress = '';
    address = jQuery('#edit-field-meeting-venueaddress-0-value').val();
    postalCode = jQuery('#edit-field-meeting-postalcode-0-value').val();
    province = jQuery('#edit-field-meeting-province-0-target-id').val();
    city = jQuery('#edit-field-meeting-city-0-target-id').val();

    if (address) {
      completeAddress += address;
      completeAddress += ' ';
    }
    if (city) {
      completeAddress += city;
      completeAddress += ' ';
    }
    if (province) {
      completeAddress += province;
      completeAddress += ' ';
    }

    completeAddress += country;
    completeAddress += ' ';

    if (postalCode) {
      completeAddress += postalCode;
      completeAddress += ' ';
    }
    if (String(completeAddress) == String('Canada ')) {
      alert("Please input more information");
    }
    else {
      checkData(completeAddress);
    }
  }

  /**
   *
   */
  function checkData(completeAddress) {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode( {"address": completeAddress}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK && results.length > 0) {
        var location = results[0].geometry.location;

        lat = location.lat();
        lng = location.lng();

        if (-90 <  parseInt(lat) && parseInt(lat) < 90) {
          if (-180 < parseInt(lng) && parseInt(lng) < 180) {
            applyData(lat, lng);
          }
        }
        else {
          alert("Please input a city of canada ");
        }
      }
      else {
        coordinateAlert();
      }
    } );
  }

  /**
   *
   */
  function applyData(lat, lng) {
    jQuery('#edit-field-meeting-latitude-0-value').val(lat);
    jQuery('#edit-field-meeting-longitude-0-value').val(lng);
  }

  /**
   *
   */
  function coordinateAlert() {
    alert("We are unable to find Co-ordianates with the provided data. Please input correct information!");
  }

  insertButton();

});
