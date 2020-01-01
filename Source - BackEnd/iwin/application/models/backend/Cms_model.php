<?php
class Cms_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
    function cmsList()
    {
        $this->db->order_by('cms','asc');
        return $this->db->get('cms')->result();
    }
    function upateSystemOption($systemOptionData)
    {
        $this->db->update_batch('cms', $systemOptionData, 'cms_id');
    }
}
?>