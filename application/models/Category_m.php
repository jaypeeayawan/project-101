<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_m extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function getCategory(){
		$query = 'SELECT * FROM category';
		$sql = $this->db->query($query);
		$arr = array();
		foreach ($sql->result_array() as $row) {
			$arr[] = $row;
		}
		return $arr;
	}

	public function createCategory($categoryName){
		$query = 'SELECT * FROM category WHERE category_name = ?';
		$sql = $this->db->query($query, array($categoryName));		
		if($sql->num_rows() < 1){
			$data = array(
				'category_name' => $categoryName
			);
			return $this->db->insert('category', $data);
		}
	}

	public function updateCategory($categoryId, $categoryName){
		$data = array(
			'category_name' => $categoryName
		);
		$this->db->where('category_id', $categoryId);
		return $this->db->update('category', $data);
	}

	public function deleteCategory($categoryId){
		$this->db->where('category_id', $categoryId);
		return $this->db->delete('category');		
	}
}