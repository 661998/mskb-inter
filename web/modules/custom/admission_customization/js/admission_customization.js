(function(Drupal, $, once) {
  Drupal.behaviors.admission_customization = {
    attach: function (context, settings) {
      // Code
      $(document).ready(function () {

        // Compulsory toggler
        var value = $("form.node-admission-part-1-edit-form .field--name-field-compulsory input[name='field_compulsory']:checked").next().text();
        $('form.node-admission-part-1-edit-form .field--name-field-compulsory-2 input').each(function() {
          if ($(this).next().text() == value) {
            $(this). prop('checked', false)
            $(this).parent().hide();
          }
        });
        
        //arts
        $("form.node-admission-part-1-edit-form .field--name-field-optional-arts input").each(function() {
          if($(this).is(':checked')) {
            var value = $(this).next().text();
            $('form.node-admission-part-1-edit-form .field--name-field-additional-arts input').each(function() {
              if ($(this).next().text() == value) {
                $(this). prop('checked', false)
                $(this).parent().hide();
              }
            });
          }         
        });

        //commerce
        $("form.node-admission-part-1-edit-form .field--name-field-optional-commerce input").each(function() {
          if($(this).is(':checked')) {
            var value = $(this).next().text();
            $('form.node-admission-part-1-edit-form .field--name-field-additional-commerce input').each(function() {
              if ($(this).next().text() == value) {
                $(this). prop('checked', false)
                $(this).parent().hide();
              }
            });
          }         
        });

        //science
        $("form.node-admission-part-1-edit-form .field--name-field-optional-science input").each(function() {
          if($(this).is(':checked')) {
            var value = $(this).next().text();
            $('form.node-admission-part-1-edit-form .field--name-field-additional-science input').each(function() {
              if ($(this).next().text() == value) {
                $(this). prop('checked', false)
                $(this).parent().hide();
              }
            });
          }         
        });

      });
      
      // Compulsory toggler
      $(once('bind-click-event', 'form.node-admission-part-1-form .field--name-field-compulsory input, form.node-admission-part-1-edit-form .field--name-field-compulsory input', context)).on('change', function(){
      // $("form.node-admission-part-1-form .field--name-field-compulsory input, form.node-admission-part-1-edit-form .field--name-field-compulsory input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-admission-part-1-form .field--name-field-compulsory-2 input, form.node-admission-part-1-edit-form .field--name-field-compulsory-2 input').each(function() {
          if ($(this).next().text() == value) {
            $(this). prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });

      $(once('bind-click-event', 'form.node-admission-part-1-form .field--name-field-compulsory-2 input, form.node-admission-part-1-edit-form .field--name-field-compulsory-2 input', context)).on('change', function(){
      // $("form.node-admission-part-1-form .field--name-field-compulsory-2 input, form.node-admission-part-1-edit-form .field--name-field-compulsory-2 input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-admission-part-1-form .field--name-field-compulsory input, form.node-admission-part-1-edit-form .field--name-field-compulsory input').each(function() {
          if ($(this).next().text() == value) {
            $(this). prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });

      //arts toggler
      $(once('bind-click-event', 'form.node-admission-part-1-form .field--name-field-optional-arts input, form.node-admission-part-1-edit-form .field--name-field-optional-arts input', context)).on('change', function(){
      // $("form.node-admission-part-1-form .field--name-field-optional-arts input, form.node-admission-part-1-edit-form .field--name-field-optional-arts input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-admission-part-1-form .field--name-field-additional-arts input, form.node-admission-part-1-edit-form .field--name-field-additional-arts input').each(function() {
          if ($(this).next().text() == value) {
            $(this). prop('checked', false)
            $(this).parent().toggle();
          }
        });
      });
      $(once('bind-click-event', 'form.node-admission-part-1-form .field--name-field-additional-arts input, form.node-admission-part-1-edit-form .field--name-field-additional-arts input', context)).on('change', function(){
      // $("form.node-admission-part-1-form .field--name-field-additional-arts input, form.node-admission-part-1-edit-form .field--name-field-additional-arts input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-admission-part-1-form .field--name-field-optional-arts input, form.node-admission-part-1-edit-form .field--name-field-optional-arts input').each(function() {
          if ($(this).next().text() == value) {
            $(this). prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });

      //commerce toggler
      $(once('bind-click-event', 'form.node-admission-part-1-form .field--name-field-optional-commerce input, form.node-admission-part-1-edit-form .field--name-field-optional-commerce input', context)).on('change', function(){
      // $("form.node-admission-part-1-form .field--name-field-optional-commerce input, form.node-admission-part-1-edit-form .field--name-field-optional-commerce input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-admission-part-1-form .field--name-field-additional-commerce input, form.node-admission-part-1-edit-form .field--name-field-additional-commerce input').each(function() {
          if ($(this).next().text() == value) {
            $(this). prop('checked', false)
            $(this).parent().toggle();
          }
        });
      });
      $(once('bind-click-event', 'form.node-admission-part-1-form .field--name-field-additional-commerce input, form.node-admission-part-1-edit-form .field--name-field-additional-commerce input', context)).on('change', function(){
      // $("form.node-admission-part-1-form .field--name-field-additional-commerce input, form.node-admission-part-1-edit-form .field--name-field-additional-commerce input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-admission-part-1-form .field--name-field-optional-commerce input, form.node-admission-part-1-edit-form .field--name-field-optional-commerce input').each(function() {
          if ($(this).next().text() == value) {
            $(this). prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });
    
      //science toggler
      $(once('bind-click-event', 'form.node-admission-part-1-form .field--name-field-optional-science input, form.node-admission-part-1-edit-form .field--name-field-optional-science input', context)).on('change', function(){
      // $("form.node-admission-part-1-form .field--name-field-optional-science input, form.node-admission-part-1-edit-form .field--name-field-optional-science input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-admission-part-1-form .field--name-field-additional-science input, form.node-admission-part-1-edit-form .field--name-field-additional-science input').each(function() {
          if ($(this).next().text() == value) {
            $(this). prop('checked', false)
            $(this).parent().toggle();
          }
        });
      });
      $(once('bind-click-event', 'form.node-admission-part-1-form .field--name-field-additional-science input, form.node-admission-part-1-edit-form .field--name-field-additional-science input', context)).on('change', function(){
      // $("form.node-admission-part-1-form .field--name-field-additional-science input, form.node-admission-part-1-edit-form .field--name-field-additional-science input").once().on('change', function(){
        var value = $(this).next().text();
        $('form.node-admission-part-1-form .field--name-field-optional-science input, form.node-admission-part-1-edit-form .field--name-field-optional-science input').each(function() {
          if ($(this).next().text() == value) {
            $(this). prop('checked', false)
            $(this).parent().hide();
          }else{
            $(this).parent().show();
          }
        });
      });

      // Hide submit on click
      $(once('bind-click-event', 'form.node-form .form-actions .form-submit', context)).on('click', function (){
        $(this).hide();
        $(".node-form .form-actions").append('<div><b>Uploading...</b></div>');
      });

      // Change text to Uppercase for Name
      $('input[id="edit-field-name-0-value"]').keyup(function(){
        this.value=this.value.toUpperCase();
       });


    }
  };
})(Drupal, jQuery, once);