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
    $('#btnAdd').click(function () {   
        var divid = $(".clonedInput:last").attr('id');        
        var getnum = divid.split('entry');

        var oldNum = parseInt(getnum[1]);
        var newNum = parseInt(getnum[1]) + 1;  

        newElem = $('#' + divid).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value

        newElem.find('#answer_label_'+oldNum).attr('id', 'answer_label_' + newNum).html('Answer '+newNum);
        newElem.find('.btnDel').attr('id', 'btnDel' + newNum).attr('data-num',newNum);
        newElem.find('#MC_QA_Option_'+oldNum).attr('id', 'MC_QA_Option_'+newNum).attr('name', 'QA_Option['+newNum+']').val('');
        newElem.find('.radio-list > .radio-inline').remove();

        newElem.find('.radio-list').html("<label class='radio-inline'><input id='MC_QA_Choice_"+newNum+"_yes' class='radio' type='radio' name='QA_Choice["+newNum+"]' value='1' autocomplete='off'> Yes</label>");
        newElem.find('.radio-list').append("<label class='radio-inline'><input id='MC_QA_Choice_"+newNum+"_no' class='radio' type='radio' name='QA_Choice["+newNum+"]' value='0' autocomplete='off'> No</label>");
      
        $('#newChoice').append(newElem); 
        
        $('#entry'+newNum+' label.error').remove();       

        newElem.find('#btnDel' + newNum ).attr('disabled', false);
        newElem.find('#btnDel' + newNum ).removeClass('hide');
        removeAddress(newNum);
    });    
    function removeAddress(newNum){
        $('#btnDel'+newNum).click(function () {
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
                        $('#btnDel').attr('disabled', true);
                        // enable the "add" button
                        $('#btnAdd').attr('disabled', false).prop('value', "Add");
                    // });
                }
            return false; // Removes the last section you added
        });

    }
    // Enable the "add" button
    $('#btnAdd').attr('disabled', false);
    // Disable the "remove" button
    $('#btnDel').attr('disabled', true);
    $('#btnDel').addClass('hide');
});
