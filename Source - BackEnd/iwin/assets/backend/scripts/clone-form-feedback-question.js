/*
Author: Tristan Denyer (based on Charlie Griefer's original clone code, and some great help from Dan - see his comments in blog post)
Plugin repo: https://github.com/tristandenyer/Clone-section-of-form-using-jQuery
Demo at http://tristandenyer.com/using-jquery-to-duplicate-a-section-of-a-form-maintaining-accessibility/
Ver: 0.9.4.1
Last updated: Sep 24, 2014

The MIT License (MIT)

Copyright (c) 2011 Tristan Denyer

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
$(function () {
    var num = 0;
    $('#btnAddFeedbackQues').click(function () {   
        var divid = $(".clonedInput:last").attr('id');        
        var getnum = divid.split('entry');

        var oldNum = parseInt(getnum[1]);
        var newNum = parseInt(getnum[1]) + 1;  

        newElem = $('#' + divid).clone().attr('id', 'entry' + newNum); // create the new element via clone(), and manipulate it's ID using newNum value

        newElem.find('.btnDelFeedbackQues').attr('id', 'btnDelFeedbackQues' + newNum).attr('data-num',newNum);
        newElem.find('#feedback_question_id'+oldNum).attr('id', 'feedback_question_id'+newNum).attr('name', 'feedback_question_id['+newNum+']').val('');
        newElem.find('#feedback_question'+oldNum).attr('id', 'feedback_question'+newNum).attr('name', 'feedback_question['+newNum+']').val('');
        newElem.find('#feedback_question_type'+oldNum).attr('id', 'feedback_question_type'+newNum).attr('name', 'feedback_question_type['+newNum+']').find('option:selected').removeAttr('selected');
      
        $('#newFeedbackQuestion').append(newElem); 
        
        $('#entry'+newNum+' label.error').remove();       
        $('#entry'+newNum+' #btnAddFeedbackQues').remove();       

        newElem.find('#btnDelFeedbackQues' + newNum ).attr('disabled', false);
        newElem.find('#btnDelFeedbackQues' + newNum ).removeClass('hide');
        removeAddress(newNum);
    });    
    $('.btnDelFeedbackQues').click(function () {
        if (confirm("Are you sure you wish to remove this section? This cannot be undone."))
        {
            var num = $(this).attr('data-num');
            // how many "duplicatable" input fields we currently have
            $('#entry' + num).slideUp('slow', function () {$(this).remove();
            // if only one element remains, disable the "remove" button
                if (num -1 === 1)
            $('#btnDelFeedbackQues1').attr('disabled', true);
            // enable the "add" button
            $('#btnAddFeedbackQues').attr('disabled', false).prop('value', "Add");});
        }
        return false; // Removes the last section you added
    }); 

    function removeAddress(newNum){
        $('#btnDelFeedbackQues'+newNum).click(function () {
        // Confirmation dialog box. Works on all desktop browsers and iPhone.
            if (confirm("Are you sure you wish to remove this section? This cannot be undone."))
                {
                    var num = $(this).attr('data-num');
                    // how many "duplicatable" input fields we currently have
                    // $('#entry' + num).slideUp('slow', function () {
                        $('#entry' + num).remove();
                        // $(this).remove();
                        // if only one element remains, disable the "remove" button
                        if (num -1 === 1)
                        $('#btnDelFeedbackQues').attr('disabled', true);
                        // enable the "add" button
                        $('#btnAddFeedbackQues').attr('disabled', false).prop('value', "Add");
                    // });
                }
            return false; // Removes the last section you added
        });

    }
    // Enable the "add" button
    $('#btnAddFeedbackQues').attr('disabled', false);
    // Disable the "remove" button
    $('#btnDelFeedbackQues').attr('disabled', true);
    $('#btnDelFeedbackQues').addClass('hide');
    $('#btnDelFeedbackQues1').attr('disabled', true);
    $('#btnDelFeedbackQues1').addClass('hide');
});
