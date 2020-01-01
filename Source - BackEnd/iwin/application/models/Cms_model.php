<?php
class Cms_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }       
    public function get_cms_data($cms_slug)
    {
    	$this->db->where('cms_slug',$cms_slug);
        return $this->db->get('cms')->first_row();        
    }
}
?>