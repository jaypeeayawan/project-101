<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_m extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function itemCategory(){
		$query = 'SELECT * FROM category';
		$sql = $this->db->query($query);
		$arr = array();
		foreach($sql->result_array() as $row){
			$arr[] = $row;
		}
		return $arr;
	}
	
	public function getItemCost(){
		$query = 'SELECT * FROM item i
			JOIN supplier s ON i.supplier_supplier_id = s.supplier_id
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE sk.is_trashed = ? AND sk.is_disposed = ?';
		$sql = $this->db->query($query, array(0, 0));
		$total = 0;
		foreach($sql->result_array() as $row){
			$total += $row['item_unit_price'];
		}

		return number_format($total,2,'.',',');
	}	
	
	public function itemInStockCount(){
		$query = 'SELECT * FROM item i
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock s ON i.item_id = s.item_item_id
			WHERE s.is_stock = ?';
		$sql = $this->db->query($query, array(1));	
		return $sql->num_rows();
	}
	
	public function itemAssignedCount(){
		$query = 'SELECT * FROM assigned_item ai
			JOIN employee e ON ai.employee_employee_id = e.employee_id
			JOIN person p ON e.person_person_id = p.person_id
			JOIN department d ON e.department_department_id = d.department_id
			JOIN item i ON ai.item_item_id = i.item_id
			JOIN category c ON i.category_category_id = c.category_id
			JOIN supplier s ON i.supplier_supplier_id = s.supplier_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE sk.is_consumable = ? AND sk.is_stock = ? AND ai.is_returned = ?';
		$sql = $this->db->query($query, array(0, 0, 0));	
		return $sql->num_rows();
	}
	
	public function itemDisposedCount(){
		$query = 'SELECT DISTINCT * FROM item i
			JOIN stock s ON i.item_id = s.item_item_id
			WHERE s.is_stock = ? AND is_disposed = ?';
		$sql = $this->db->query($query, array(0, 1));	
		return $sql->num_rows();		
	}
	
	public function itemTrashedCount(){
		$query = 'SELECT * FROM item i
			JOIN stock s ON i.item_id = s.item_item_id
			WHERE s.is_trashed = ?';
		$sql = $this->db->query($query, array(1));
		return $sql->num_rows();
	}
	
	public function itemInStockPerCat($catId){
		$query = 'SELECT * FROM item i
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock s ON i.item_id = s.item_item_id
			WHERE s.is_stock = ? AND c.category_id = ?';
		$sql = $this->db->query($query, array(1, $catId));	
		return $sql->num_rows();
	}
	
	public function assignedItemPerCat($catId){
		$query = 'SELECT * FROM item i
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock s ON i.item_id = s.item_item_id
			JOIN assigned_item ai ON i.item_id = ai.item_item_id
			WHERE s.is_assigned = ? AND c.category_id = ? AND is_returned = ?';
		$sql = $this->db->query($query, array(1, $catId, 0));	
		return $sql->num_rows();		
	}
	
	public function disposedItemPerCat($catId){
		$query = 'SELECT DISTINCT * FROM item i
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock s ON i.item_id = s.item_item_id
			WHERE s.is_disposed = ? AND c.category_id = ?';
		$sql = $this->db->query($query, array(1, $catId));
		
		return $sql->num_rows();		
	}
	
}