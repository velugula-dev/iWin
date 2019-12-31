// Common validation
$('form').each(function(index, element) {
    var clsname = element.className.split(' ');
    // "isautovalid" class in exist in form' class
    if(clsname.indexOf('isautovalid') != -1){
      var frmID = element.id;    
      $("#"+frmID).validate({
          ignore: [],
          errorPlacement: function(error, element) 
          {
              //console.log(element)        
              var text = element[0].className.split(' ');
              if(text.indexOf('ckeditor') != -1){
                  //element[0].id;
                  error.insertAfter('#cke_'+element.attr("name"));
              } else {
                error.insertAfter(element);
              }            
          }
      });
    }
});
jQuery("#form_add_notification").validate({  
  ignore: [],
  rules: {    
    notification_type: {
      required: true
    },
    "Users[]": {
      required: true
    },
    notification_contents: {
      required: true
    },
    notification_date: {
      required: true
    }
  },
  errorPlacement: function(error, element) 
  {
    //alert(element.attr("name"));
    if (element.attr("name") == "Users[]") 
    {      
      error.insertAfter('.SumoSelect');
    } 
    else 
    {
      error.insertAfter(element);
    }
  }  
});
jQuery("#form_add_question").validate({  
  rules: {    
    question_name: {
      required: true
    },
    question_type: {
      required: true
    }
  }
});
jQuery("#form_add_feedback").validate({  
  ignore: [],
  rules: {    
    message: 
    {
      required: function() 
      {
        CKEDITOR.instances.message.updateElement();
      }
    }    
  },
  errorPlacement: function(error, element) 
  {
    //alert(element.attr("name"));
    if (element.attr("name") == "message") 
    {      
      error.insertAfter('#cke_message');
    } 
    else 
    {
      error.insertAfter(element);
    }
  }  
});
// admin email exist check
function checkEmailExist(email,UserID){
  $.ajax({
    type: "POST",
    url: BASEURL+"myprofile/checkEmailExist",
    data: 'email=' + email +'&UserID='+UserID,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#EmailExist').show();
        $('#EmailExist').html("User is already exist with this email id!");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#EmailExist').html("");
        $('#EmailExist').hide();        
        $(':input[type="submit"]').prop("disabled",false);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#EmailExist').show();
      $('#EmailExist').html(errorThrown);
    }
  });
}
$.validator.addMethod("emailcustom",function(value,element)
{
  return this.optional(element) || /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i.test(value);
},"Please enter valid email address");

// custom password
$.validator.addMethod("passwordcustome",function(value,element)
{
  return this.optional(element) || /^(?=.*[0-9])(?=.*[!@#$%^&*)(])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*)(]{8,}$/.test(value);
},"Passwords must contain at least 8 characters, including uppercase, lowercase letters, symbols and numbers.");

// custom code for lesser than
jQuery.validator.addMethod('lesserThan', function(value, element, param) {  
  return ( parseInt(value) <= parseInt(jQuery(param).val()) );
}, 'Must be less than' );

// custom code for greater than
$.validator.addMethod("greaterThan", function(value, element, param) {
  return ( parseInt(value) >= parseInt(jQuery(param).val()) );    
}, "Must be greater than");
// end here
