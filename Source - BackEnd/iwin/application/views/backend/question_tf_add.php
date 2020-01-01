<?php for($c=0,$inc=1;$c<2;$c++,$inc++) {?>
<div class="form-group">
	<label class="control-label col-md-2">Option <?php echo ($inc==1)?"Yes":"No"; ?></label>
	<div class="col-md-10">
		<label class="control-label col-md-3">Correct Answer?</label>
		<div class="col-md-9">
			<div class="true_false_options radio-list">
				<input type="hidden" name="answer_autoid[<?php echo $inc;?>]" value="<?php echo ($editAnswerDetail)?$editAnswerDetail[$c]->question_answer_id:"";?>">   
				<input type="hidden" name="QA_AnswerChoice[<?php echo $inc;?>]" value="<?php echo ($inc==1)?"Yes":"No"; ?>">   
				<label class="radio-inline">
				<input name="QA_Choice[<?php echo $inc;?>]" id="TF_QA_Choice_<?php echo $inc;?>_yes" class="radio-choice" type="radio"  title="" <?php echo ($editAnswerDetail[$c]->is_correct_answer=="1")?"checked":"";?> value="1" autocomplete="off"> Yes</label>
				<label class="radio-inline">
				<input name="QA_Choice[<?php echo $inc;?>]" id="TF_QA_Choice_<?php echo $inc;?>_no" class="radio-choice" type="radio"  title="" <?php echo ($editAnswerDetail[$c]->is_correct_answer=="0")?"checked":"";?> value="0" autocomplete="off"> No</label>
			</div>		
		</div>		
	</div>
</div>
<?php } ?>
<script type="text/javascript">
	$('#TF_QA_Choice_1_yes').click(function () { 		
		$('.true_false_options input').removeClass('checked');	
		$(this).addClass('checked');	
	    $('#TF_QA_Choice_2_no').attr('checked',true);	 	
	    $('#TF_QA_Choice_2_no').addClass('checked');	 		     	    
	});
	$('#TF_QA_Choice_1_no').click(function () {  			
		$('.true_false_options input').removeClass('checked');		
		$(this).addClass('checked');	
	    $('#TF_QA_Choice_2_yes').attr('checked',true);	    	    	       
	    $('#TF_QA_Choice_2_yes').addClass('checked');	    
	});
	$('#TF_QA_Choice_2_yes').click(function () {			
		$('.true_false_options input').removeClass('checked');	 		
		$(this).addClass('checked');	
	    $('#TF_QA_Choice_1_no').attr('checked',true);	    	    	    	      
	    $('#TF_QA_Choice_1_no').addClass('checked');	    
	});
	$('#TF_QA_Choice_2_no').click(function () { 					
		$('.true_false_options input').removeClass('checked');
		$(this).addClass('checked');	
	    $('#TF_QA_Choice_1_yes').attr('checked',true);	    	    	    	    
	    $('#TF_QA_Choice_1_yes').addClass('checked');	    
	});	
</script>