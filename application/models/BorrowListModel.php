<?php

class BorrowListModel extends CI_Model {
    

    public $borrowerIDNum;
    public $labID;
    public $compSerialNum;
    public $eqpSerialNum;
    public $teacher;
    public $inCharge;
    
	function __construct(){
        parent::__construct();
    }


    public function addBorrowedEquipments(){
        $return = array();
        foreach ($_POST['equipment'] as $equipment) {
            $this->compSerialNum = NULL;
            $this->eqpSerialNum = NULL;

            $this->db->select('eqpSerialNum');
            $this->db->from('equipment');
            $this->db->where('eqpSerialNum', $equipment);
            $result = $this->db->get()->result_array();
            if(count($result) == 0){
                $this->compSerialNum = $equipment;
            }else{
                $this->eqpSerialNum = $equipment;
            }
           $this->borrowerIDNum = $_POST['borrowerID']; 
           $this->labID = $_POST['labID'];
           $this->teacher = $_POST['bteacher'];
           $this->inCharge = $_POST['incharge'];
           $return[] = $this->db->insert('borrowed_list',$this);
        }
        return $return;
    }

    public function getBorrowedEquipments(){
        $result = array();
        $borrower = $_POST['borrower'];
        $lab = $_POST['labID'];

        $this->db->select('studentName');
        $this->db->from('student');
        $this->db->where('studentID', $borrower);
        $result[] = $this->db->get()->result_array();

        $this->db->select('B.borrowerIDNum, C.compSerialNum, C.compName, B.borrowedDate, E.eqpSerialNum, E.eqpName, S.studentName');
        $this->db->from('borrowed_list B');
        $this->db->join('equipment E', 'E.eqpSerialNum = B.eqpSerialNum', 'left');
        $this->db->join('component C', 'C.compSerialNum = B.compSerialNum', 'left');
        $this->db->join('student S', 'S.studentID = B.borrowerIDNum', 'left');
        $this->db->where('borrowerIDNum = "'.$borrower.'" AND B.labID = '.$lab);
        
        $result[] = $this->db->get()->result_array();

        return $result;   
    }

    public function returnEquipments(){
        $result = array();
        foreach ($_POST['equipment'] as $equipment) {
            $this->db->from('borrowed_list');
            $where = 'eqpSerialNum = "'.$equipment.'" OR compSerialNum = "'.$equipment.'"';
            $this->db->where($where);
            $result[] = $this->db->delete();    
        }
        return $result;
    }

    public function getBorrowedList($months = null){
        $data = array();
        for($i = 0; $i < count($months); $i++){
            $this->db->select('count("bListID") as "totalItems"');
            $this->db->where('DATE_FORMAT(borrowedDate,"%M") LIKE "%'.$months[$i].'%" OR DATE_FORMAT(borrowedDate,"%Y") LIKE "%'.$months[$i].'%"');
            $this->db->group_by('DATE_FORMAT(borrowedDate,"%M")');
            $query = $this->db->get('borrowed_list');
            if ($query->num_rows() > 0){
               array_push($data, intval($query->result()[0]->totalItems));
            }else{
                array_push($data, 0);
            }
        }
        return $data;
    }

    public function getBorrowedListPerLab($months = null, $lab = null){
        $data = array();
        for($i = 0; $i < count($months); $i++){
            $this->db->select('count("bListID") as "totalItems"');
            $this->db->where('(DATE_FORMAT(borrowedDate,"%M") LIKE "%'.$months[$i].'%" OR DATE_FORMAT(borrowedDate,"%Y") LIKE "%'.$months[$i].'%") AND labID = '.$lab);
            $this->db->group_by('DATE_FORMAT(borrowedDate,"%M")');
            $query = $this->db->get('borrowed_list');
            if ($query->num_rows() > 0){
               array_push($data, intval($query->result()[0]->totalItems));
            }else{
                array_push($data, 0);
            }
        }
        return $data;
    }
}
