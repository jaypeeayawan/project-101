<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_m extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function getEmployee(){
		$query = 'SELECT * FROM employee e
			JOIN person p ON e.person_person_id = p.person_id
			JOIN department d ON e.department_department_id = d.department_id';
		$sql = $this->db->query($query);
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function createEmployee($idNumber, $lastName, $firstName, $middleInitial, $extName, $title, $departmentId){
		$query = 'SELECT * FROM person 
			WHERE last_name = ? AND first_name = ? AND middle_initial = ? ';
		$sql = $this->db->query($query, array($lastName,$firstName,$middleInitial));		
		if($sql->num_rows() < 1){
			$data = array(
				'id_number' => $idNumber,
				'last_name' => $lastName,
				'first_name' => $firstName,
				'middle_initial' => $middleInitial,
				'ext_name' => $extName
			);
			$this->db->insert('person', $data);

			$emp = array(
				'person_person_id' => $this->db->insert_id(),
				'department_department_id' => $departmentId,
				'title' => $title				
			);
			$this->db->insert('employee', $emp);
		}
	}

	public function updateEmployee($personId, $idNumber, $lastName, $firstName, $middleInitial, $extName, $title, $departmentId){
		$data = array(
			'id_number' => $idNumber,
			'last_name' => $lastName,
			'first_name' => $firstName,
			'middle_initial' => $middleInitial,
			'ext_name' => $extName,
		);
		$this->db->where('person_id', $personId);
		$this->db->update('person', $data);

		$emp = array(
			'title' => $title,
			'department_department_id' => $departmentId
		);
		$this->db->where('person_person_id', $personId);
		$this->db->update('employee', $emp);
	}

	public function deleteEmployee(){
	
	}
}