/**
 *
 */
// jQuery(document).ready(function() {

  function saveHtmlToPng(block_id) {
    var blockElement = document.getElementById(block_id);
    html2canvas(blockElement).then(function(canvas) {
      var element = document.createElement('a');
      element.href = canvas.toDataURL("image/png");
      element.download = 'download.png';
      element.click();
    });
  }

// });