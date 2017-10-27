<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SystemLogs_m extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function getSystemLogs(){
		$query = 'SELECT * FROM logs';
		$sql = $this->db->query($query);
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function createSystemLogs($name, $message, $date){
		$data = array(
			'name' => $name,
			'message' => ucfirst($message),
			'date' => $date
		);

		$this->db->insert('logs', $data);
	}
}