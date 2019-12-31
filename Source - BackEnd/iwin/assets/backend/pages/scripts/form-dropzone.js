var FormDropzone = function () {


    return {
        //main function to initiate the module
        init: function () {  
            Dropzone.options.myDropzone = {
                init: function() {                    
                    var dropzoneHtml = '<div class="dz-default dz-message"><span>Drop Files Here or Click to Upload</span></div>';
                    dropzoneHtml = "<div class='fine-uploader-manual-trigger'><div class='qq-uploader-selector qq-uploader'>"+dropzoneHtml+"</div></div";
                    $('.dz-default').remove();
                    $("#my-dropzone").prepend(dropzoneHtml);                    
                    this.on("addedfile", function(file) {                      
                        // Create the remove button
                        var noOffile = $('.qq-uploader .dz-preview').length;
                        noOffile = parseInt(noOffile + 1);
                        var removeButton = Dropzone.createElement("<button class='btn btn-sm btn-block' id='"+noOffile+"'>Remove file</button>");
                        // Capture the Dropzone instance as closure.
                        var _this = this;

                        // Listen to the click event
                        removeButton.addEventListener("click", function(e) {
                            var imageID = $('#uploaded_file_id').val();
                            var removedimageID = $(this).attr('id');
                            var newimgid = imageID.split(',');
                            removedimageID = removedimageID -1;
                            var newelementId = newimgid[removedimageID];
                            jQuery.ajax({
                              type : "POST",
                              dataType : "json",
                              url : BASEURL +'admin/hotels/DeleteBeforeCall',                             
                              data : {'HotelImageID':newelementId},
                              success: function(response) {
                                return true;
                              }
                            });
                            newimgid.splice($.inArray(newimgid[removedimageID],newimgid) ,1 );
                            $('#uploaded_file_id').val(newimgid);
                          // Make sure the button click doesn't submit the form:
                          e.preventDefault();
                          e.stopPropagation();

                          // Remove the file preview.
                          _this.removeFile(file);
                          $(".qq-uploader-selector .dz-preview").each(function( index ) 
                          {
                            $(this).find(".btn-block").attr('id',index+1); 
                          });   

                          // If you want to the delete the file on the server as well,
                          // you can do the AJAX request here.
                        });

                        // Add the button to the file preview element.
                        file.previewElement.appendChild(removeButton);
                        $('.qq-uploader-selector').append(file.previewElement);
                        //file.previewElement.remove();
                    });
                    this.on("success", function(fileID,response) 
                    {                      
                      $('.dz-preview').css('display','none');                      
                      $('.qq-uploader-selector .dz-preview').css('display','');
                      var imageID = $('#uploaded_file_id').val();
                      imageID = imageID + response + ',';
                      $('#uploaded_file_id').val(imageID);
                    });
                }            
            }
        }
    };
}();