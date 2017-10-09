<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_m extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

    public function allposts_count(){   
        $query = $this
            ->db
            ->get('department');
        return $query->num_rows();  
    }
    
    public function allposts($limit,$start,$col,$dir){   
       $query = $this
	        ->db
	        ->limit($limit,$start)
	        ->order_by($col,$dir)
	        ->get('department');
        if($query->num_rows()>0){
            return $query->result(); 
        }else{
            return null;
        }
    }
   
    public function posts_search($limit,$start,$search,$col,$dir){
        $query = $this
            ->db
            ->like('department_code',$search)
            ->or_like('department_title',$search)
            ->limit($limit,$start)
            ->order_by($col,$dir)
            ->get('department');
        if($query->num_rows()>0){
            return $query->result();  
        }else{
            return null;
        }
    }

    public function posts_search_count($search){
        $query = $this
            ->db
            ->like('department_code',$search)
            ->or_like('department_title',$search)
            ->get('department');
        return $query->num_rows();
    } 

	// public function getDepartmentJson(){
	// 	$query = 'SELECT * FROM department';
	// 	$sql = $this->db->query($query);
	// 	$arr = array();
	// 	foreach($sql->result_array() as $row){
	// 		$arr[] = array(
	// 			'department_id' => $row['department_id'],
	// 			'department_code' => $row['department_code'],
	// 			'department_title' => $row['department_title']
	// 		);
	// 	}
	// 	header ( "Content-type: application/json" );
	// 	echo ("{\"data\":".json_encode($arr)."}");
	// }	
	
	public function getDepartment(){
		$query = 'SELECT * FROM department';
		$sql = $this->db->query($query);
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function createDepartment($departmentCode, $departmentTitle){
		$query = 'SELECT * FROM department 
			WHERE department_code = ? AND department_title = ? ';
		$sql = $this->db->query($query, array($departmentCode,$departmentTitle));		
		if($sql->num_rows() < 1){
			$data = array(
				'department_code' => $departmentCode,
				'department_title' => $departmentTitle
			);
			return $this->db->insert('department', $data);
		}else{
			echo $sql->num_rows();
		}
	}

	public function updateDepartment($departmentId, $departmentCode, $departmentTitle){
		$data = array(
			'department_code' => $departmentCode,
			'department_title' => $departmentTitle
		);
		$this->db->where('department_id', $departmentId);
		return $this->db->update('department', $data);
	}

	public function deleteDepartment($departmentId){
		$this->db->where('department_id', $departmentId);
		return $this->db->delete('department');		
	}
}