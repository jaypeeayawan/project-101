<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item_m extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function getItem($categoryId){
		$this->db->select('*');
		$this->db->from('item i');
		$this->db->join('supplier s', 's.supplier_id = i.supplier_supplier_id');
		$this->db->join('category c', 'c.category_id = i.category_category_id');
		$this->db->join('stock sk', 'sk.item_item_id = i.item_id');
		$this->db->where('sk.is_trashed', 0);
		$this->db->where('sk.is_disposed', 0);
		$this->db->order_by('i.item_code', 'ASC');

		(($categoryId > 0) ? $this->db->where('c.category_id', $categoryId) : null);

		$sql = $this->db->get();
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}	
	
	public function getNonConsumableItemForDropdown(){
		$query = 'SELECT * FROM item i
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE sk.is_trashed = ? AND sk.is_disposed = ? 
			AND sk.is_stock = ? AND sk.is_consumable = ?';
		$sql = $this->db->query($query, array(0, 0, 1, 0));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;		
	}

	public function getConsumableItemForDropdown(){
		$query = 'SELECT * FROM item i
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE sk.is_trashed = ? AND sk.is_disposed = ? 
			AND sk.is_stock = ? AND sk.is_consumable = ?';
		$sql = $this->db->query($query, array(0, 0, 1, 1));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;		
	}	
	
	public function itemTrashed(){
		$query = 'SELECT * FROM item i
			JOIN supplier s ON i.supplier_supplier_id = s.supplier_id
			JOIN category c ON i.category_category_id = c.category_id
			JOIN stock sk ON i.item_id = sk.item_item_id
			WHERE sk.is_trashed = ?';
		$sql = $this->db->query($query, array(1));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;		
	}	

	public function createItem($supplierId,$categoryId,$itemCode,$itemBrand,$itemDescription,$orNumber,$itemPrice,$datePurchased,$isConsumable){
		$data = array(
			'supplier_supplier_id' => $supplierId,
			'category_category_id' => $categoryId,
			'item_code' => $itemCode,
			'item_brand' => $itemBrand,
			'item_description' => $itemDescription,
			'or_number' => $orNumber,
			'item_unit_price' => $itemPrice,
			'date_purchased' => $datePurchased
		);
		$this->db->insert('item', $data);	
		
		$itemId = $this->db->insert_id();
		//
		$stock = array(
			'item_item_id' => $itemId,
			'status' => '',
			'is_consumable' => $isConsumable,
			'is_stock' => 1,
			'is_disposed' => '',
			'is_trashed' => '',
			'is_assigned' => ''
		);
		$this->db->insert('stock', $stock);
		//
		$history = array(
			'item_item_id' => $itemId,
			'date' => date('m/d/Y h:i A'),
			'remarks' => 'Item added to stock by',
			'employee' => 'Asset Management Office(AMO)'
		);
		$this->db->insert('item_history', $history);
	}
	
	public function addComment($itemId,$postData){
		$data = array(
			'item_item_id' => $itemId,
			'comments_date' => date('Y-m-d h:i:s A'),
			'comments' => $postData
		);
		$this->db->insert('comments', $data);
	}
	
	public function viewComments($itemId){
		$query = 'SELECT * FROM item i
			JOIN comments c ON i.item_id = c.item_item_id
			WHERE i.item_id = ?';
		$sql = $this->db->query($query, array($itemId));
		$arr = array();
		foreach($sql->result_array() as $row){
			$arr[] = array(
				'commentId' => $row['comments_id'],
				'itemId' => $row['item_item_id'],
				'commentDate' => $row['comments_date'],
				'comment' => $row['comments']
			);
		}
		header ( "Content-type: application/json" );
		echo json_encode($arr);
	}
	
	public function viewItemHistory($itemId){
		$query = 'SELECT * FROM item_history ih WHERE ih.item_item_id = ?';	
		$sql = $this->db->query($query, array($itemId));
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = array(
				'itemHistoryId' => $row['item_history_id'],
				'itemId' => $row['item_item_id'],
				'date' => $row['date'],
				'remarks' => $row['remarks'],
				'employee' => $row['employee']
			);
		}
		header ( "Content-type: application/json" );
		echo json_encode($arr);		
	}

	public function updateItem($itemId,$supplierId,$categoryId,$itemCode,$itemBrand,$itemDescription,$orNumber,$itemPrice,$datePurchased){
		$data = array(
			'supplier_supplier_id' => $supplierId,
			'category_category_id' => $categoryId,
			'item_code' => $itemCode,
			'item_brand' => $itemBrand,
			'item_description' => $itemDescription,
			'or_number' => $orNumber,
			'item_unit_price' => $itemPrice,
			'date_purchased' => $datePurchased
		);
		$this->db->where('item_id', $itemId);
		$this->db->update('item', $data);
	}

	public function trashItem($itemId){
		
		$query = 'SELECT * FROM assigned_item WHERE item_item_id = ? AND is_returned = ?';
		$sql = $this->db->query($query, array($itemId, 0));
		
		if($sql->num_rows() < 1){
			$this->db->set('is_stock', 0);
			$this->db->set('is_disposed', 0);
			$this->db->set('is_trashed', 1);
			$this->db->set('is_assigned', 0);
			$this->db->where('item_item_id', $itemId);
			$this->db->update('stock');		
		}	
	}
}