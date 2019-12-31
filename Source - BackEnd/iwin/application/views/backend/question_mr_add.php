<?php $loopVar = ($editAnswerDetail)?count($editAnswerDetail):2; ?>
<?php for($c=0,$inc=1;$c<$loopVar;$c++,$inc++) 
{?>
<div id="entry<?php echo $inc;?>" class="clonedInput">
	<div class="form-group">
		<label class="control-label col-md-2" id="answer_label_<?php echo $inc;?>">Answer <?php echo $inc;?></label>
		<div class="col-md-4">
			<textarea id="MR_QA_Option_<?php echo $inc;?>" name="QA_Option[<?php echo $inc;?>]" class="form-control option" rows="6"><?php echo $editAnswerDetail[$c]->answer;?></textarea>
		</div>
		<label class="control-label col-md-2">Correct Answer?</label>
		<div class="col-md-3">
			<div class="true_false_options radio-list">
				<input type="hidden" name="answer_autoid[<?php echo $inc;?>]" value="<?php echo ($editAnswerDetail)?$editAnswerDetail[$c]->question_answer_id:"";?>">   
				<label class="radio-inline">
				<input name="QA_Choice[<?php echo $inc;?>]" id="MR_QA_Choice_<?php echo $inc;?>_yes" class="radio-choice" type="radio"  title="" <?php echo ($editAnswerDetail[$c]->is_correct_answer ===1)?"Checked":"";?> value="1" autocomplete="off"> Yes</label>
				<label class="radio-inline">
				<input name="QA_Choice[<?php echo $inc;?>]" id="MR_QA_Choice_<?php echo $inc;?>_no" class="radio-choice" type="radio"  title="" <?php echo ($editAnswerDetail[$c]->is_correct_answer === 0)?"Checked":"";?> value="0" autocomplete="off"> No</label>
			</div>		
		</div>
		<div class="col-md-2">
             <?php if($inc>2) {?>
                <input type="button" value="Remove" id="btnDel<?php echo $inc;?>" class="btnDel btnDelMR btn danger-btn text-trans-none" name="btnDel<?php echo $inc;?>" data-num="<?php echo $inc;?>">                                                        
            <?php } else{?>
                <input type="button" value="Remove" id="btnDel" class="btnDel btnDelMR hide btn danger-btn text-trans-none" name="btnDel">                                                        
            <?php }?>
		</div>		
	</div>
</div>
<?php }?>
<script type="text/javascript">
$('.btnDelMR').click(function () {
    if (confirm("Are you sure you wish to remove this section? This cannot be undone."))
    {
        var num = $(this).attr('data-num');
        // how many "duplicatable" input fields we currently have
        $('#entry' + num).slideUp('slow', function () {$(this).remove();
        // if only one element remains, disable the "remove" button
            if (num -1 === 1)
        $('#btnDel').attr('disabled', true);
        // enable the "add" button
        $('#btnAdd').attr('disabled', false).prop('value', "Add");});
    }
    return false; // Removes the last section you added
});	
</script>

