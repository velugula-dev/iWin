<?php
class Common_model extends CI_Model {
    function __construct()
    {
        parent::__construct();		
    }	
    
    /****************************************
    Function: addData, Add record in table
    $tablename: Name of table    
    $data: array of data
    *****************************************/
    public function addData($tablename,$data)
    {   
        $this->db->insert($tablename,$data);            
        return $this->db->insert_id();
    }

    /****************************************
    Function: updateData, Update records in table
    $tablename: Name of table    
    $data: array of data
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function updateData($tablename,$data,$wherefieldname,$wherefieldvalue)
    {        
        $this->db->where($wherefieldname,$wherefieldvalue);
        $this->db->update($tablename,$data);
        return $this->db->affected_rows();
    }

    /****************************************
    Function: updateData, Delete records from table
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function deleteData($tablename,$wherefieldname,$wherefieldvalue)
    {        
        $this->db->where($wherefieldname,$wherefieldvalue);
        return $this->db->delete($tablename);        
    }

    /****************************************
    Function: getSingleRow, get first row from table in Object format using single WHERE clause
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function getSingleRow($tablename,$wherefieldname,$wherefieldvalue)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        return $this->db->get($tablename)->first_row();
    }

    /****************************************
    Function: getMultipleRows, get multiple row from table in Object format using single WHERE clause
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    ****************************************/
    public function getMultipleRows($tablename,$wherefieldname,$wherefieldvalue)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        return $this->db->get($tablename)->result();
    }

    /****************************************
    Function: getRowsMultipleWhere, get row from table in Object format using multiple WHERE clause
    $tablename: Name of table        
    $wherearray: where field array    
    ****************************************/
    public function getRowsMultipleWhere($tablename,$wherearray)
    {
        $this->db->where($wherearray);
        return $this->db->get($tablename)->result();
    }

    /****************************************
    Function: deleteInsertRecord, Delete existing records and insert new records
    $tablename: Name of table        
    $wherefieldname: where field name
    $wherefieldvalue: where field value
    $data: array of data that need to insert
    ****************************************/
    public function deleteInsertRecord($tablename,$wherefieldname,$wherefieldvalue,$data)
    {
        $this->db->where($wherefieldname,$wherefieldvalue);
        $this->db->delete($tablename);
        
        return $this->db->insert_batch($tablename,$data);
    }

    /****************************************
    Function: insertBatch, Bulk insert new records
    $tablename: Name of table        
    $data: array of data that need to insert
    ****************************************/
    public function insertBatch($tablename,$data)
    {
        return $this->db->insert_batch($tablename,$data);
    }

    /****************************************
    Function: updateBatch, Bulk update records
    $tablename: Name of table        
    $data: array of data that need to insert
    $fieldname: Field name used as WHERE Clause
    ****************************************/
    public function updateBatch($tablename,$data,$fieldname)
    {
        return $this->db->update_batch($tablename, $data, $fieldname);
    }
}
?>