<?php
// create slug based on title
function slugify($text,$tablename,$fieldname)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);
  // trim
  $text = trim($text, '-');
  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);
  // lowercase
  $text = strtolower($text);
  if (empty($text)) {
    return 'n-a';
  }
  $i = 1; 
  $baseSlug = $text;
  while(slug_exist($text,$tablename,$fieldname))
  {
		$text = $baseSlug . "-" . $i++;        
  }
  return $text;
}
function slug_exist($text,$tablename,$fieldname)
{
  //check slug is uniquee or not.
  $CI =& get_instance();
  $checkSlug = $CI->db->get_where($tablename,array($fieldname=>$text))->num_rows();  
  if($checkSlug > 0)
  {
    return true;
  }
}
function notification_types()
{
  return array(
    'questionsPosted'=>"Question Posted",
    'answersPosted'=>"Answer Posted",
    'winnersAnnouncement'=>"Winner Announcement",
    'custom'=>"Custom",
    'feedback'=>"Feedback",
  );
}
function getScreenName()
{
  return array(
    'get_profile_data'=>"Profile",
    'get_quiz_questions_list'=>"Attempt Quiz",
    'get_user_quiz_history'=>"Quiz History",
    'get_quiz_result'=>"Quiz Result",
    'feedback_msg_questions_list'=>"Feedback"
  );
}


/*function encriptString(){
  return encrypt('123456');
}*/
?>