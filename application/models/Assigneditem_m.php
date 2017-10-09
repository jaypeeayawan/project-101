<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AssignedItem_m extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function getAssignedNonConsumableItem(){
		$query = 'SELECT * FROM assigned_item ai
			JOIN employee e ON ai.employee_employee_id = e.employee_id
			JOIN person p ON e.person_person_id = p.person_id
			JOIN department d ON e.department_department_id = d.department_id
			JOIN item i ON ai.item_item_id = i.item_id
			JOIN category c ON i.category_category_id = c.category_id
			JOIN supplier s ON i.supplier_supplier_id = s.supplier_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE ai.is_returned = ? AND sk.is_consumable = ?';
		$sql = $this->db->query($query, array(0, 0));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function getAssignedConsumableItem(){
		$query = 'SELECT * FROM assigned_item ai
			JOIN employee e ON ai.employee_employee_id = e.employee_id
			JOIN person p ON e.person_person_id = p.person_id
			JOIN department d ON e.department_department_id = d.department_id
			JOIN item i ON ai.item_item_id = i.item_id
			JOIN category c ON i.category_category_id = c.category_id
			JOIN supplier s ON i.supplier_supplier_id = s.supplier_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE ai.is_returned = ? AND sk.is_consumable = ?';
		$sql = $this->db->query($query, array(0, 1));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function createAssignedItem($employeeId,$itemId,$location){
		$data = array(
			'employee_employee_id' => $employeeId,
			'item_item_id' => $itemId,
			'date_assigned' => date('m/d/Y h:i A'),
			'location' => $location,
			'is_returned' => '',
			'date_returned' => ''
		);
		$this->db->insert('assigned_item', $data);

		//
		$emp = 'SELECT * FROM employee e
			JOIN person p ON e.person_person_id = p.person_id
			WHERE e.employee_id = ?';
		$sql = $this->db->query($emp, array($employeeId));
		$row = $sql->row();

		$history = array(
			'item_item_id' => $itemId,
			'date' => date('m/d/Y h:i A'),
			'remarks' => 'Item assigned to',
			'employee' => $row->last_name.','.$row->first_name.' '.$row->middle_initial

		);
		$this->db->insert('item_history', $history);

		//
		$this->db->set('is_stock', 0);
		$this->db->set('is_assigned', 1);
		$this->db->where('item_item_id', $itemId);
		$this->db->update('stock');
	}

	public function returnAssignedItem($assignedItemId,$itemId){
		$this->db->set('is_returned', 1);
		$this->db->set('date_returned', date('m/d/Y h:i A'));
		$this->db->where('assigned_item_id', $assignedItemId);
		$this->db->update('assigned_item');

		//
		$this->db->set('is_stock', 1);
		$this->db->set('is_assigned', 0);
		$this->db->where('item_item_id', $itemId);
		$this->db->update('stock');

		//
		$history = array(
			'item_item_id' => $itemId ,
			'date' => date('m/d/Y h:i A'),
			'remarks' => 'Item returned to',
			'employee' => 'Asset Management Office(AMO)'

		);
		$this->db->insert('item_history', $history);
	}

}
