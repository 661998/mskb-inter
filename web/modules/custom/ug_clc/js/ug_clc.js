(function($) {
  Drupal.behaviors.ug_clc = {
    attach: function (context, settings) {
      // Code
      $(once('bind-click-event', '.print', context)).each(function () {
        $(this).on('click', function(e) {
          e.preventDefault();

          var getUrl = window.location;
          var baseUrl = getUrl .protocol + "//" + getUrl.host;
          var divToPrint = document.getElementById('clc-wrapper');
          var anotherWindow = window.open('', 'Print-Window');
          anotherWindow.document.open();
          anotherWindow.document.write('<html><head><link rel="stylesheet" type="text/css" href="' + baseUrl +'/modules/custom/ug_clc/css/pdf.css" /></head><body onload="window.print()" class="print-wrapper" id="clc-wrapper">' + divToPrint.innerHTML + '</body></html>');
          anotherWindow.document.close();
          setTimeout(function() {
            anotherWindow.close();
          }, 5000);
        });
      });
    }
  };
})(jQuery);