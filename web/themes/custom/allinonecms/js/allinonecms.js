(function($, Drupal) {
    Drupal.behaviors.allinonecms = {
        attach: function (context, settings) {
            
            // code
            $('.icon').off().click(function() {
                $('#side-menu').toggleClass('shrink');
                $(this).find('i').toggleClass('fa-bars fa-xmark');
            });

            // Menu open in small screen
            $(".navbar-toggler").off().click(function(){
                $(this).find('i').toggleClass('fa-bars fa-xmark');
                $("#superfish-main-toggle").click();
            });

            // Download ID Card after generate
            $(document).ready(function () {
                setTimeout(function() {
                    $('a[data-auto-download]').each(function(){
                        var $this = $(this);
                        setTimeout(function() {
                        window.location = $this.attr('href');
                        }, 1000);
                    });
                    
                },5000);
            });

            $(".commerce-checkout-flow-multistep-default .button.button--primary.js-form-submit.form-submit").click();
        }
   } ;
   })(jQuery, Drupal);