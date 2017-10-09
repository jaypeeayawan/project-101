<?php defined('BASEPATH') OR exit('No direct script access allowed');

class LostPassword extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		
		$this->load->helper(array('url', 'html', 'form', 'date'));
		$this->load->model('user_m');
	}
	
	public function index(){
		$username = htmlentities($this->input->post('username'));
		$email = htmlentities($this->input->post('email'));
		
		$query = 'SELECT * FROM user u
			JOIN person p ON u.person_person_id = p.person_id
			WHERE p.username = ? AND u.is_active = ?';
		$sql = $this->db->query($query, array($username, 1));
		
		if($sql->num_rows() > 0){
			
			$config = Array(
				'protocol' => 'smtp',
				'smtp_host' => 'ssl://smtp.gmail.com',
				'smtp_port' => '465',
				'smtp_user' => 'user@gmail.com',
				'smtp_pass' => 'userpass',
				'mailtype' => 'html',
				'charset' => 'utf-8',
				'wordwrap' => TRUE
			);
			
			$this->load->library('email', $config);
			$this->email->set_newline("\r\n");
			
			$this->email->from('user@gmail.com', 'Jaypee Ayawan');
			$this->email->to($email);
			$this->email->subject('Your new password');
			
			$pword = trim($this->generatePassword());// generated password
			
			$this->email->message($pword);
			
			if($this->email->send()){
				$this->db->set('password', $this->user_m->hash($pword));
				$this->db->where('username', $username);
				$this->db->update('person');
				echo 'Your new password was sent to your email.';
			}else{
				show_error($this->email->print_debugger());
			}
			
		}
		redirect(base_url().''.$getConrtroller);
	}
	
	private function generatePassword($maxlength = 6) {
		$characters = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z",
						"0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
						"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
		$return_str = "";
		for ($x = 0; $x <= $maxlength; $x++) {
			$return_str .= $characters[rand(0, count($characters)-1)];
		}
		return $return_str;		
	}	
	
}

?>