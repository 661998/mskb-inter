(function(Drupal, $, once) {
  Drupal.behaviors.part_2_examination_customization = {
    attach: function (context, settings) {
      // Code
      $( window ).on( "load", function() {
        //arts
        if ($("form.node-examination-part-2-form .field--name-field-honours-subject-arts select").val() != '_none') {
          var value = $("form.node-examination-part-2-form .field--name-field-honours-subject-arts option:selected").text();
          $('form.node-examination-part-2-form .field--name-field-subsidiary-1-arts select option').each(function() {
              if ($(this).text() == value) {
                $(this).remove();
              }
          });
          $('form.node-examination-part-2-form .field--name-field-subsidiary-2-arts select option').each(function() {
              if ($(this).text() == value) {
                $(this).remove();
              }
          });
        }
        //commerce
        
        if ($("form.node-examination-part-2-form .field--name-field-honours-subject-commerce select").val() != '_none') {
          var value = $("form.node-examination-part-2-form .field--name-field-honours-subject-commerce option:selected").text();
          $('form.node-examination-part-2-form .field--name-field-subsidiary-1-commerce select option').each(function() {
              if ($(this).text() == value) {
                $(this).remove();
              }
          });
          $('form.node-examination-part-2-form .field--name-field-subsidiary-2-commerce select option').each(function() {
              if ($(this).text() == value) {
                $(this).remove();
              }
          });
        }
        //science
        if ($("form.node-examination-part-2-form .field--name-field-honours-subject-science select").val() != '_none') {
            var value = $("form.node-examination-part-2-form .field--name-field-honours-subject-science option:selected").text();
            $('form.node-examination-part-2-form .field--name-field-subsidiary-1-science select option').each(function() {
                if ($(this).text() == value) {
                  $(this).remove();
                }
            });
            $('form.node-examination-part-2-form .field--name-field-subsidiary-2-science select option').each(function() {
                if ($(this).text() == value) {
                  $(this).remove();
                }
            });
        }
      });
      
      
      //arts toggler
      $("form.node-examination-part-2-form .field--name-field-subsidiary-1-arts select").once().on('change', function(){
        var value = $(this).val();
        $('form.node-examination-part-2-form .field--name-field-subsidiary-2-arts select option').each(function() {
          if ($(this).val() == value) {
            $(this).hide();
          }else{
            $(this).show();
          }
        });
      });
      
      $("form.node-examination-part-2-form .field--name-field-subsidiary-2-arts select").once().on('change', function(){
          var value = $(this).val();
          $('form.node-examination-part-2-form .field--name-field-subsidiary-1-arts select option').each(function() {
            if ($(this).val() == value) {
              $(this).hide();
            }else{
              $(this).show();
            }
          });
      });

      //commerce toggler
      $("form.node-examination-part-2-form .field--name-field-subsidiary-1-commerce select").change(function(){
        var value = $(this).val();
        $('form.node-examination-part-2-form .field--name-field-subsidiary-2-commerce select option').each(function() {
          if ($(this).val() == value) {
            $(this).hide();
          }else{
            $(this).show();
          }
        });
      });
    
      $("form.node-examination-part-2-form .field--name-field-subsidiary-2-commerce select").change(function(){
          var value = $(this).val();
          $('form.node-examination-part-2-form .field--name-field-subsidiary-1-commerce select option').each(function() {
            if ($(this).val() == value) {
              $(this).hide();
            }else{
              $(this).show();
            }
          });
      });
    
      //science toggler
      $("form.node-examination-part-2-form .field--name-field-subsidiary-1-science select").change(function(){
          var value = $(this).val();
          $('form.node-examination-part-2-form .field--name-field-subsidiary-2-science select option').each(function() {
            if ($(this).val() == value) {
              $(this).hide();
            }else{
              $(this).show();
            }
          });
      });
      
      $("form.node-examination-part-2-form .field--name-field-subsidiary-2-science select").change(function(){
          var value = $(this).val();
          $('form.node-examination-part-2-form .field--name-field-subsidiary-1-science select option').each(function() {
            if ($(this).val() == value) {
              $(this).hide();
            }else{
              $(this).show();
            }
          });
      });

    }
  };
})(Drupal, jQuery, once);