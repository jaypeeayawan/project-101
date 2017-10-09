<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Additional_Administrator extends CI_Migration {
	
	public function up(){
		
		$this->load->dbforge();
		
		// person table
		if(!$this->db->table_exists('person')){
			$this->dbforge->add_field(array(
				'person_id' => array(
					'type' => 'bigint',
					'constraint' => '20',
					'unsigned' => TRUE,
					'auto_increment' => TRUE
				),
				'id_nummer' => array(
					'type' => 'varchar',
					'constraint' => '45',
					'null' => FALSE
				),
				'last_name' => array(
					'type' => 'varchar',
					'constraint' => '45',
					'null' => FALSE
				),
				'first_name' => array(
					'type' => 'varchar',
					'constraint' => '45',
					'null' => FALSE
				),
				'middle_initial' => array(
					'type' => 'char',
					'constraint' => '1',
					'null' => FALSE
				),
				'ext_name' => array(
					'type' => 'varchar',
					'constraint' => '45',
					'null' => FALSE
				),
				'username' => array(
					'type' => 'varchar',
					'constraint' => '45',
					'null' => FALSE
				),
				'password' => array(
					'type' => 'varchar',
					'constraint' => '120',
					'null' => FALSE
				)
			));
			
			$this->dbforge->add_key('person_id', TRUE);
			$this->dbforge->create_table('person');
			
		}
		
		// admin table
		if(!$this->db->table_exists('admin')){
			$this->dbforge->add_field(array(
				'admin_id' => array(
					'type' => 'bigint',
					'constraint' => '20',
					'unsigned' => TRUE,
					'auto_increment' => TRUE
				),
			));	
			$this->dbforge->add_field('CONSTRAINT FOREIGN KEY (person_person_id) REFERENCES person(person_id)');
			
			$this->dbforge->add_key('admin_id', TRUE);
			$this->dbforge->create_table('admin');
		
		}
		
		$personData = array(
			'id_nummer' => '000074AMO',
			'last_name' => 'Sagamla',
			'first_name' => 'Fides',
			'middle_initial' => 'A',
			'ext_name' => '',
			'username' => '000074AMO',
			'password' => sha1('000074AMO'),
		);
		
		$personId = $this->db->insert_id();
		$this->db->insert('person', $personData);
		
		$adminData = array(
			'person_person_id' => $personId
		);
		$this->db->insert('admin', $adminData);
		
	}
	
	public function down(){
		
		$this->dbforge->drop_table('person');
		$this->dbforge->drop_table('admin');
		
	}
	
}

?>