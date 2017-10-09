<?php 
	
	$userID = $this->session->userdata(base_url().''.$getController.'/personId');
	$query = 'SELECT * FROM person 
		WHERE person_id = ?';
	
	$sql = $this->db->query($query, array($userID));
	$user = $sql->row();

	echo '<i class="fa fa-user">'.nbs().$user->last_name.', '.$user->first_name.' '.$user->middle_initial.'.'.nbs().'</i>';
?>

