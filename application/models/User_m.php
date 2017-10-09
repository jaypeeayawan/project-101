<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_m extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function userInfo($personId){
		$query = 'SELECT * FROM person p WHERE p.person_id = ?';
		$sql = $this->db->query($query, array($personId));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function getUsers(){
		$query = 'SELECT * FROM user u JOIN person p ON u.person_person_id = p.person_id';
		$sql = $this->db->query($query);
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function activeUsersCount(){
		$query = 'SELECT * FROM user u
			JOIN person p ON u.person_person_id = p.person_id
			WHERE u.is_active = ?';
		$sql = $this->db->query($query,array(1));
		return $sql->num_rows();
	}

	public function createUserDropdown(){
		$query = 'SELECT * FROM person';
		$sql = $this->db->query($query);
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function createNewUser($idNum,$lName,$fName,$mName,$eName,$etitle,$eDept){
		$query = 'SELECT * FROM person WHERE id_number = ? AND last_name = ? AND first_name = ? AND middle_initial = ? ';
		$sql = $this->db->query($query, array($idNum,$lName,$fName,$mName));
		if($sql->num_rows() < 1){
			$data = array(
				'id_number' => $idNum,
				'last_name' => $lName,
				'first_name' => $fName,
				'middle_initial' => $mName,
				'ext_name' => $eName,
				'username' => $idNum,
				'password' => $this->hash($idNum),
			);
			$this->db->insert('person', $data);
			$personId = $this->db->insert_id(); // person id
			//
			$user = array(
				'person_person_id' => $personId,
				'is_active' => 1
			);
			$this->db->insert('user', $user);

			//
			$employee = array(
				'person_person_id' => $personId,
				'department_department_id' => $eDept,
				'title' => $etitle
			);
			$this->db->insert('employee', $employee);
		}
	}

	public function createUser($personId,$idNumber){
		$data = array(
			'username' => $idNumber,
			'password' => $this->hash($idNumber),
		);
		$this->db->update('person', $data, array('person_id' => $personId));

		$user = array(
			'person_person_id' => $personId,
			'is_active' => 1
		);
		$this->db->insert('user', $user);
	}

	public function activateUser($userId){
		$this->db->set('is_active', 1);
		$this->db->where('user_id', $userId);
		$this->db->update('user');
	}

	public function deactivateUser($userId){
		$this->db->set('is_active', 0);
		$this->db->where('user_id', $userId);
		$this->db->update('user');
	}

	public function changePassword($personId,$currentPass,$newPass,$confirmNewPass){
		$query = 'SELECT * FROM person p WHERE p.person_id = ? AND p.password = ?';
		$sql = $this->db->query($query, array($personId, $this->hash($currentPass)));

		if($sql->num_rows() > 0){
			$this->db->set('password', $this->hash($confirmNewPass));
			$this->db->where('person_id', $personId);
			$this->db->update('person');
		}
	}

	public function hash($string){
		return hash('sha1', $string.config_item('encryption_key'));
	}

}
