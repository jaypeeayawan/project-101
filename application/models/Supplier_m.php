<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_m extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function getSupplier(){
		$query = 'SELECT * FROM supplier';
		$sql = $this->db->query($query);
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function createSupplier($supplierName, $supplierAddress, $supplierEmail, $supplierContact){
		$query = 'SELECT * FROM supplier WHERE supplier_name = ?';
		$sql = $this->db->query($query, array($supplierName));		
		if($sql->num_rows() < 1){
			$data = array(
				'supplier_name' => $supplierName,
				'supplier_address' => $supplierAddress,
				'supplier_email' => $supplierEmail,
				'supplier_contact' => $supplierContact
			);
			return $this->db->insert('supplier', $data);
		}
	}

	public function updateSupplier($suppierId, $supplierName, $supplierAddress, $supplierEmail, $supplierContact){
		$data = array(
			'supplier_name' => $supplierName,
			'supplier_address' => $supplierAddress,
			'supplier_email' => $supplierEmail,
			'supplier_contact' => $supplierContact
		);
		$this->db->where('supplier_id', $suppierId);
		return $this->db->update('supplier', $data);
	}

	public function deleteSupplier($suppierId){
		$this->db->where('supplier_id', $suppierId);
		return $this->db->delete('supplier');		
	}
}