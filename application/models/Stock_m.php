<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_m extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	
	public function stockItemConsumable(){
		$query = 'SELECT * FROM item i
			JOIN supplier s ON i.supplier_supplier_id = s.supplier_id
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE sk.is_consumable = ? AND sk.is_stock = ?';
		$sql = $this->db->query($query, array(1, 1));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;		
	}
	
	public function stockItemNoneConsumable(){
		$query = 'SELECT * FROM item i
			JOIN supplier s ON i.supplier_supplier_id = s.supplier_id
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE sk.is_consumable = ? AND sk.is_stock = ?';
		$sql = $this->db->query($query, array(0, 1));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;		
	}
	
	public function stockItemDisposed(){
		$query = 'SELECT * FROM item i
			JOIN supplier s ON i.supplier_supplier_id = s.supplier_id
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE sk.is_disposed = ?';
		$sql = $this->db->query($query, array(1));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;		
	}	

	public function disposeStock($stockId){
		
		$query = 'SELECT * FROM assigned_item ai
			JOIN item i ON ai.item_item_id = i.item_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE sk.stock_id = ? AND sk.is_assigned = ? AND ai.is_returned = ?';
		$sql = $this->db->query($query, array($stockId, 0, 0));	
		
		if($sql->num_rows() <= 0){
			$this->db->set('is_consumable', 0);
			$this->db->set('is_stock', 0);
			$this->db->set('is_disposed', 1);
			$this->db->where('stock_id', $stockId);
			$this->db->update('stock');	
		}
	}
}