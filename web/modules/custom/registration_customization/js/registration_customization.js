(function(Drupal, $, once) {
  Drupal.behaviors.registration_customization = {
    attach: function (context, settings) {
      // Code
      $( window ).once().on( "load", function() {

        // Compulsory toggler
        var value = $("form.node-registration-edit-form .field--name-field-compulsory input[name='field_compulsory']:checked").next().text();
        $('form.node-registration-edit-form .field--name-field-compulsory-2 input').each(function() {
          if ($(this).next().text() == value) {
            $(this).prop('checked', false)
            $(this).parent().hide();
          }
        });
        
        //arts
        $("form.node-registration-edit-form .field--name-field-optional-arts input").each(function() {
          if($(this).is(':checked')) {
            var value = $(this).next().text();
            $('form.node-registration-edit-form .field--name-field-additional-arts input').each(function() {
              if ($(this).next().text() == value) {
                $(this).prop('checked', false)
                $(this).parent().hide();
              }
            });
          }         
        });

        //commerce
        $("form.node-registration-edit-form .field--name-field-optional-commerce input").each(function() {
          if($(this).is(':checked')) {
            var value = $(this).next().text();
            $('form.node-registration-edit-form .field--name-field-additional-commerce input').each(function() {
              if ($(this).next().text() == value) {
                $(this).prop('checked', false)
                $(this).parent().hide();
              }
            });
          }         
        });

        //science
        $("form.node-registration-edit-form .field--name-field-optional-science input").each(function() {
          if($(this).is(':checked')) {
            var value = $(this).next().text();
            $('form.node-registration-edit-form .field--name-field-additional-science input').each(function() {
              if ($(this).next().text() == value) {
                $(this).prop('checked', false)
                $(this).parent().hide();
              }
            });
          }         
        });

      });
      
      // Compulsory toggler
      $("form.node-registration-form .field--name-field-compulsory input, form.node-registration-edit-form .field--name-field-compulsory input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-registration-form .field--name-field-compulsory-2 input, form.node-registration-edit-form .field--name-field-compulsory-2 input').each(function() {
          if ($(this).next().text() == value) {
            $(this).prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });
      $("form.node-registration-form .field--name-field-compulsory-2 input, form.node-registration-edit-form .field--name-field-compulsory-2 input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-registration-form .field--name-field-compulsory input, form.node-registration-edit-form .field--name-field-compulsory input').each(function() {
          if ($(this).next().text() == value) {
            $(this).prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });

      //arts toggler
      $("form.node-registration-form .field--name-field-optional-arts input, form.node-registration-edit-form .field--name-field-optional-arts input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-registration-form .field--name-field-additional-arts input, form.node-registration-edit-form .field--name-field-additional-arts input').each(function() {
          if ($(this).next().text() == value) {
            $(this).prop('checked', false)
            $(this).parent().toggle();
          }
        });
      });
      $("form.node-registration-form .field--name-field-additional-arts input, form.node-registration-edit-form .field--name-field-additional-arts input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-registration-form .field--name-field-optional-arts input, form.node-registration-edit-form .field--name-field-optional-arts input').each(function() {
          if ($(this).next().text() == value) {
            $(this).prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });

      //commerce toggler
      $("form.node-registration-form .field--name-field-optional-commerce input, form.node-registration-edit-form .field--name-field-optional-commerce input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-registration-form .field--name-field-additional-commerce input, form.node-registration-edit-form .field--name-field-additional-commerce input').each(function() {
          if ($(this).next().text() == value) {
            $(this).prop('checked', false)
            $(this).parent().toggle();
          }
        });
      });
      $("form.node-registration-form .field--name-field-additional-commerce input, form.node-registration-edit-form .field--name-field-additional-commerce input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-registration-form .field--name-field-optional-commerce input, form.node-registration-edit-form .field--name-field-optional-commerce input').each(function() {
          if ($(this).next().text() == value) {
            $(this).prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });
    
      //science toggler
      $("form.node-registration-form .field--name-field-optional-science input, form.node-registration-edit-form .field--name-field-optional-science input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-registration-form .field--name-field-additional-science input, form.node-registration-edit-form .field--name-field-additional-science input').each(function() {
          if ($(this).next().text() == value) {
            $(this).prop('checked', false)
            $(this).parent().toggle();
          }
        });
      });
      $("form.node-registration-form .field--name-field-additional-science input, form.node-registration-edit-form .field--name-field-additional-science input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-registration-form .field--name-field-optional-science input, form.node-registration-edit-form .field--name-field-optional-science input').each(function() {
          if ($(this).next().text() == value) {
            $(this).prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });
      

    }
  };
})(Drupal, jQuery, once);