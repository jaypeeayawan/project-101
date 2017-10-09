<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Asset extends CI_Controller{

	public function __construct(){
		parent::__construct();

		$this->load->helper(array('url', 'html', 'form', 'date'));
		$this->load->library(array('session'));

		$this->load->model('user_m');
		$this->load->model('dashboard_m');
		$this->load->model('department_m');
		$this->load->model('employee_m');
		$this->load->model('supplier_m');
		$this->load->model('category_m');
		$this->load->model('item_m');
		$this->load->model('assigneditem_m');
		$this->load->model('stock_m');
	}

	public function index(){
		$this->home();
	}

	public function login(){
		if($this->loggedIn()) $this->redirectTo('index');

		$this->title = 'Login';
		$this->getController = $this->getController();

		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$query = 'SELECT * FROM user u
			JOIN person p ON u.person_person_id = p.person_id
			WHERE p.username = ? AND p.password = ? AND u.is_active = ?';
		$sql = $this->db->query($query, array($username, $this->user_m->hash($password), 1));
		if($sql->num_rows() > 0){
			$row = $sql->row();

			$CI =& get_instance();
			$CI->load->library('session');
  			$data = array(
				base_url().''.strtolower(get_class($CI)).'/personId' => $row->person_id,
				base_url().''.strtolower(get_class($CI)).'/loggedIn' => TRUE
  			);
  			$CI->session->set_userdata($data);
  			redirect(base_url().''.strtolower(get_class()).'/dashboard/');
  		}else{

  		}

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$this->load->view('login', $data);
	}

	public function generalSettings(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'General Settings';
		$this->getController = $this->getController();
		$this->pageTitle = heading('General Settings', 3);
		$this->js = 'generalSettings_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">User Information</a></li>';
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Change Password</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<div class="col-xs-2 col-md-1">';
							$this->content .= '<i style="font-size: 50px;" class="fa fa-user"></i>';
						$this->content .= '</div>';
						$this->content .= '<div class="col-xs-12 col-sm-10 col-md-11">';
								foreach ($this->user_m->userInfo($this->personId()) as $row):
									$this->content .= heading($row['last_name'].', '.$row['first_name'].' '.$row['middle_initial'], 2);
								endforeach;
								$this->content .= '<a href="#"><span id="user-info" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="ID Number : '.$row['id_number'].'" class="label label-danger"><i class="fa fa-tag"></i> '.$row['id_number'].'</span></a>';
								$this->content .= nbs(2);
								$this->content .= '<a href="#"><span id="user-info" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="'.$this->getController().'" class="label label-info"><i class="fa fa-user"></i> Administrator</span></a>';
						$this->content .= '</div>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= form_open('', 'id="change-pass-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= form_label('Current Password<span class="required">*</span> :', 'current-password');
							$this->content .= form_password('currentPassword', '', 'class="form-control" id="current-password" data-parsley-required-message="Current password field is required" required="required"');
							$this->content .= form_label('New Password<span class="required">*</span> :', 'new-password');
							$this->content .= form_password('newPassword', '', 'class="form-control" id="new-password" data-parsley-length="[8, 15]" data-parsley-required-message="New username field is required" required="required"');
							$this->content .= form_label('Confirm New Password<span class="required">*</span> :', 'confirm-new-password');
							$this->content .= form_password('confirmNewPassword', '', 'class="form-control" id="confirm-new-password" data-parsley-equalto="#new-password" data-parsley-required-message="Confirm new username field is required" required="required"');
							$this->content .= br();
							$form_saveBtn_attr = array(
								'name' => 'changePass',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'change-pass-btn',
								'content' => '<i class="fa fa-save"></i> Save Changes'
							);
							$this->content .= form_button($form_saveBtn_attr);
							$this->content .= form_button('clearBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnClear"');
						$this->content .= form_close();
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';
		$this->content .= '</div>';

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	public function changePasswordManager(){
		$postData = $this->input->post('postData');
		$personId = $this->personId();
		$currentPass = htmlentities($postData[0]);
		$newPass = htmlentities($postData[1]);
		$confirmNewPass = htmlentities($postData[2]);
		$this->user_m->changePassword($personId,$currentPass,$newPass,$confirmNewPass);
	}

	public function logout(){
		$this->session->sess_destroy();
		$this->redirectTo('login');
	}

	public function home(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Home';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Home', 3);
		$this->js = 'home_js';
		$this->content = '<div id="successLoginDiv"></div>';
		$this->content .= '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
				$this->content .= '<div class="col-xs-2 col-md-1">';
					$this->content .= '<i style="font-size: 50px;" class="fa fa-user"></i>';
				$this->content .= '</div>';
				$this->content .= '<div class="col-xs-12 col-sm-10 col-md-11">';
					foreach ($this->user_m->userInfo($this->personId()) as $row):
						$this->content .= heading($row['last_name'].', '.$row['first_name'].' '.$row['middle_initial'], 2);
					endforeach;
					$this->content .= '<a href="#"><span id="user-info" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="'.ucfirst($this->getController()).'" class="label label-info"><i class="fa fa-user"></i> '.ucfirst($this->getController()).'</span></a>';
					$this->content .= nbs();
					$this->content .= '<a href="'.base_url().''.$this->getController().'/generalSettings/"><span id="user-info" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="General Settings" class="label label-warning"><i class="fa fa-cog"></i> General Settings</span></a>';
				$this->content .= '</div>';
			$this->content .= '</div>';
		$this->content .= '</div>';

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// dasboard
	public function dashboard(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Dasboard';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Dasboard', 3);
		$this->js = 'dashboard_js';
		$this->content = '<hr/>';
		$this->content .= '<div class="row tile_count">';
			$this->content .= '<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">';
				$this->content .= '<span class="count_top"><i class="fa fa-desktop"></i> Items In Stock</span>';
				$this->content .= '<div class="count">'.$this->dashboard_m->itemInStockCount().'</div>';
				$this->content .= '<a href="'.base_url().''.$this->getController().'/stockManager/"><span class="green count_bottom"><i class="fa fa-desktop"></i> Stock Manager</span></a>';
			$this->content .= '</div>';

			$this->content .= '<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">';
				$this->content .= '<span class="count_top"><i class="fa fa-shopping-cart"></i> Assigned Items</span>';
				$this->content .= '<div class="count">'.$this->dashboard_m->itemAssignedCount().'</div>';
				$this->content .= '<a href="'.base_url().''.$this->getController().'/assignedItemManager/"><span class="green count_bottom"><i class="fa fa-shopping-cart"></i> Assigned Item Manager</span></a>';
			$this->content .= '</div>';

			$this->content .= '<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">';
				$this->content .= '<span class="count_top"><i class="fa fa-recycle"></i> Disposed Items</span>';
				$this->content .= '<div class="count">'.$this->dashboard_m->itemDisposedCount().'</div>';
				$this->content .= '<a href="'.base_url().''.$this->getController().'/disposedItemManager/"><span class="green count_bottom"><i class="fa fa-recycle"></i> Disposed Manager</span></a>';
			$this->content .= '</div>';

			$this->content .= '<div class="col-md-6 col-sm-8 col-xs-10 tile_stats_count">';
				$this->content .= '<span class="count_top"><i class="fa fa-rub"></i> Estimated KCP Worth</span>';
				$this->content .= '<div class="count"><i class="fa fa-rub"></i>'.$this->dashboard_m->getItemCost().'</div>';
				$this->content .= '<span class="green count_bottom"><i class="fa fa-rub"></i> Total</span>';
			$this->content .= '</div>';
		$this->content .= '</div>';

		$this->content .= '<div class="row">';
		$this->content .= '<div class="col-md-4 col-sm-4 col-xs-12">';
			$this->content .= '<div class="x_panel tile">';
				$this->content .= '<div class="x_title">';
					$this->content .= heading('Items In Stock', 2);
					$this->content .= '<ul class="nav navbar-right panel_toolbox">';
						$this->content .= '<li><a class="close-link"><i class="fa fa-close"></i></a>';
						$this->content .= '<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>';
					$this->content .= '</ul>';
					$this->content .= '<div class="clearfix"></div>';
				$this->content .= '</div>';
				$this->content .= '<div class="x_content">';
					$categories = $this->dashboard_m->itemCategory();
					foreach($categories as $category){
						$this->content .= '<div class="widget_summary">';
							$this->content .= '<div class="w_left w_25">';
								$this->content .= '<span>'.$category['category_name'].'</span>';
							$this->content .= '</div>';
							$this->content .= '<div class="w_center w_55">';
								$this->content .= '<div class="progress">';
									$this->content .= '<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="'.$this->dashboard_m->itemInStockPerCat($category['category_id']).'">';
									$this->content .= '</div>';
								$this->content .= '</div>';
							$this->content .= '</div>';
							$this->content .= '<div class="w_right w_20">';
								$this->content .= '<span>'.$this->dashboard_m->itemInStockPerCat($category['category_id']).'</span>';
							$this->content .= '</div>';
							$this->content .= '<div class="clearfix"></div>';
						$this->content .= '</div>';
					}
				$this->content .= '</div>';
			$this->content .= '</div>';
		$this->content .= '</div>';// .col-md-4 col-sm-4 col-xs-12
		$this->content .= '<div class="col-md-4 col-sm-4 col-xs-12">';
			$this->content .= '<div class="x_panel tile">';
				$this->content .= '<div class="x_title">';
					$this->content .= heading('Assigned Items', 2);
					$this->content .= '<ul class="nav navbar-right panel_toolbox">';
						$this->content .= '<li><a class="close-link"><i class="fa fa-close"></i></a>';
						$this->content .= '<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>';
					$this->content .= '</ul>';
					$this->content .= '<div class="clearfix"></div>';
				$this->content .= '</div>';
				$this->content .= '<div class="x_content">';
					$categories = $this->dashboard_m->itemCategory();
					foreach($categories as $category){
						$this->content .= '<div class="widget_summary">';
							$this->content .= '<div class="w_left w_25">';
								$this->content .= '<span>'.$category['category_name'].'</span>';
							$this->content .= '</div>';
							$this->content .= '<div class="w_center w_55">';
								$this->content .= '<div class="progress">';
									$this->content .= '<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="'.$this->dashboard_m->assignedItemPerCat($category['category_id']).'">';
									$this->content .= '</div>';
								$this->content .= '</div>';
							$this->content .= '</div>';
							$this->content .= '<div class="w_right w_20">';
								$this->content .= '<span>'.$this->dashboard_m->assignedItemPerCat($category['category_id']).'</span>';
							$this->content .= '</div>';
							$this->content .= '<div class="clearfix"></div>';
						$this->content .= '</div>';
					}
				$this->content .= '</div>';
			$this->content .= '</div>';
		$this->content .= '</div>';// .col-md-4 col-sm-4 col-xs-12
		$this->content .= '<div class="col-md-4 col-sm-4 col-xs-12">';
			$this->content .= '<div class="x_panel tile">';
				$this->content .= '<div class="x_title">';
					$this->content .= heading('Disposed Items', 2);
					$this->content .= '<ul class="nav navbar-right panel_toolbox">';
						$this->content .= '<li><a class="close-link"><i class="fa fa-close"></i></a>';
						$this->content .= '<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>';
					$this->content .= '</ul>';
					$this->content .= '<div class="clearfix"></div>';
				$this->content .= '</div>';
				$this->content .= '<div class="x_content">';
					$categories = $this->dashboard_m->itemCategory();
					foreach($categories as $category){
						$this->content .= '<div class="widget_summary">';
							$this->content .= '<div class="w_left w_25">';
								$this->content .= '<span>'.$category['category_name'].'</span>';
							$this->content .= '</div>';
							$this->content .= '<div class="w_center w_55">';
								$this->content .= '<div class="progress">';
									$this->content .= '<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="'.$this->dashboard_m->disposedItemPerCat($category['category_id']).'">';
									$this->content .= '</div>';
								$this->content .= '</div>';
							$this->content .= '</div>';
							$this->content .= '<div class="w_right w_20">';
								$this->content .= '<span>'.$this->dashboard_m->disposedItemPerCat($category['category_id']).'</span>';
							$this->content .= '</div>';
							$this->content .= '<div class="clearfix"></div>';
						$this->content .= '</div>';
					}
				$this->content .= '</div>';
			$this->content .= '</div>';
		$this->content .= '</div>';// .col-md-4 col-sm-4 col-xs-12
		$this->content .= '</div>';// .row

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// department manager
	public function departmentManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Department Manager';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Department Manager', 3);
		$this->js = 'departmentManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Department List</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Department Code</th>';
									$this->content .= '<th>Descriptive Title</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								foreach ($this->department_m->getDepartment() as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['department_code'].'</td>';
									$this->content .= '<td>'.$row['department_title'].'</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// user manager
	public function userManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');
		$this->title = 'User Manager';
		$this->getController = $this->getController();
		$this->pageTitle = heading('User Manager', 3);
		$this->js = 'userManager_js';
		$this->content = '<div class="x_panel">';

			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">User(s) List</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>ID Number</th>';
									$this->content .= '<th>Last Name</th>';
									$this->content .= '<th>First Name</th>';
									$this->content .= '<th>Middle Initial</th>';
									$this->content .= '<th>Ext. Name</th>';
									$this->content .= '<th>Status</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								foreach ($this->user_m->getUsers() as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['id_number'].'</td>';
									$this->content .= '<td>'.$row['last_name'].'</td>';
									$this->content .= '<td>'.$row['first_name'].'</td>';
									$this->content .= '<td>'.$row['middle_initial'].'</td>';
									$this->content .= '<td>'.$row['ext_name'].'</td>';
									$this->content .= '<td>'.(($row['is_active'] == 1) ? 'Active' : 'Inactive').'</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// employee manager
	public function employeeManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');
		$this->title = 'Employee Manager';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Employee Manager', 3);
		$this->js = 'employeeManager_js';
		$this->content = '<div class="x_panel">';

			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Employee List</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>ID Number</th>';
									$this->content .= '<th>Last Name</th>';
									$this->content .= '<th>First Name</th>';
									$this->content .= '<th>Middle Initial</th>';
									$this->content .= '<th>Ext. Name</th>';
									$this->content .= '<th>Title</th>';
									$this->content .= '<th>Department</th>';
									//$this->content .= '<th>Actions</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								foreach ($this->employee_m->getEmployee() as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['id_number'].'</td>';
									$this->content .= '<td>'.$row['last_name'].'</td>';
									$this->content .= '<td>'.$row['first_name'].'</td>';
									$this->content .= '<td>'.$row['middle_initial'].'</td>';
									$this->content .= '<td>'.$row['ext_name'].'</td>';
									$this->content .= '<td>'.$row['title'].'</td>';
									$this->content .= '<td>'.$row['department_code'].'</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content

		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// supplier manger
	public function supplierManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Supplier Manager';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Supplier Manager', 3);
		$this->js = 'supplierManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Supplier List</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Supplier Name</th>';
									$this->content .= '<th>Supplier Address</th>';
									$this->content .= '<th>Supplier Email</th>';
									$this->content .= '<th>Supplier Contact</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								foreach ($this->supplier_m->getSupplier() as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['supplier_name'].'</td>';
									$this->content .= '<td>'.$row['supplier_address'].'</td>';
									$this->content .= '<td>'.$row['supplier_email'].'</td>';
									$this->content .= '<td>'.$row['supplier_contact'].'</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// category manager
	public function categoryManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Category Manager';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Category Manager', 3);
		$this->js = 'categoryManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Category List</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-display" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Category</th>';
									$this->content .= '<th>Items</th>';
									// $this->content .= '<th>Actions</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								foreach ($this->category_m->getCategory() as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['category_name'].'</td>';
									$this->content .= '<td>';
										$this->content .= '<a id="'.$row['category_id'].'" class="btn btn-success btn-xs redirectToItemBtn"><i class="fa fa-location-arrow"></i> Items</a>';
									$this->content .= '</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// item manager
	public function itemManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$categoryId = $this->uri->segment(3, 0);

		$this->title = 'Item Manager';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Item Manager', 3);
		$this->js = 'itemManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Item List</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-display" class="table table-striped projects">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Item ID</th>';
									$this->content .= '<th>Category/Name</th>';
									$this->content .= '<th>Brand</th>';
									$this->content .= '<th>Supplier</th>';
									$this->content .= '<th>Actions</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								foreach ($this->item_m->getItem($categoryId) as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['item_code'].'</td>';
									$this->content .= '<td>'.$row['category_name'].'</td>';
									$this->content .= '<td>'.$row['item_brand'].'</td>';
									$this->content .= '<td>'.$row['supplier_name'].'</td>';
									$this->content .= '<td>';
										$this->content .= '<a id="'.$row['item_id'].'" class="btn btn-info btn-xs commentsViewBtn"><i class="fa fa-eye"></i> View</a>';
										$this->content .= '<a id="'.$row['item_id'].'" class="btn btn-warning btn-xs historyViewBtn"><i class="fa fa-history"></i> History</a>';
										$this->content .= '<a id="'.$row['item_id'].'_'.$row['supplier_id'].'_'.$row['supplier_name'].'_'.$row['category_id'].'_'.$row['category_name'].'_'.$row['item_code'].'_'.$row['item_brand'].'_'.$row['item_description'].'_'.$row['or_number'].'_'.$row['item_unit_price'].'_'.$row['date_purchased'].'" class="btn btn-success btn-xs informationBtn"><i class="fa fa-folder"></i> Info </a>';
									$this->content .= '</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
			// item view comments
			$this->content .= '<div id="item-view-comments-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Item Comment(s)', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<div class="dashboard-widget-content">';
								$this->content .= '<ul id="itemCommentsContainer" class="list-unstyled timeline widget">';
								$this->content .= '</ul> ';
							$this->content .= '</div>';
						$this->content .= '</div>';
						$this->content .= '<div class="modal-footer">';// modal footer
							$this->content .= form_button('closeBtn', 'Close', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';
				$this->content .= '</div>';
			$this->content .= '</div>';
			// item view history
			$this->content .= '<div id="item-view-history-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Item History', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<div class="dashboard-widget-content">';
								$this->content .= '<ul id="itemHistoryContainer" class="list-unstyled timeline widget">';
								$this->content .= '</ul> ';
							$this->content .= '</div>';
						$this->content .= '</div>';
						$this->content .= '<div class="modal-footer">';// modal footer
							$this->content .= form_button('closeBtn', 'Close', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';
				$this->content .= '</div>';
			$this->content .= '</div>';
			// item information modal
			$this->content .= '<div id="item-information-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Item Information', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<div class="itemInfoContainer"></div>';
						$this->content .= '</div>';
						$this->content .= '<div class="modal-footer">';// modal footer
							$this->content .= form_button('closeBtn', 'Close', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';
				$this->content .= '</div>';
			$this->content .= '</div>';
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	public function viewCommentItemManager(){
		$itemId = $this->uri->segment(3, 0);
		$this->item_m->viewComments($itemId);
	}

	public function viewHistoryItemManager(){
		$itemId = $this->uri->segment(3, 0);
		$this->item_m->viewItemHistory($itemId);
	}

	// assigned item manager
	public function assignedItemsManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Assigned Item Manager';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Assigned Items Manager', 3);
		$this->js = 'assignedItemManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Assigned Non Consumable Items</a></li>';
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Assigned Consumable Items</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<div class="table-responsive">';
							$this->content .= '<table id="" class="table display table-hover table-striped table-bordered table-condensed" width="100%">';
								$this->content .= '<thead>';
									$this->content .= '<tr>';
										$this->content .= '<th>Item ID</th>';
										$this->content .= '<th>Name/Category</th>';
										$this->content .= '<th>Assigned To</th>';
										$this->content .= '<th>Department</th>';
										$this->content .= '<th>Date Assigned</th>';
										$this->content .= '<th>Location</th>';
									$this->content .= '</tr>';
								$this->content .= '</thead>';
								$this->content .= '<tbody>';
									foreach ($this->assigneditem_m->getAssignedNonConsumableItem() as $row):
										$this->content .= '<tr>';
										$this->content .= '<td>'.$row['item_code'].'</td>';
										$this->content .= '<td>'.$row['category_name'].'</td>';
										$this->content .= '<td>'.$row['last_name'].', '.$row['first_name'].' '.$row['middle_initial'].'</td>';
										$this->content .= '<td>'.$row['department_code'].'</td>';
										$this->content .= '<td>'.$row['date_assigned'].'</td>';
										$this->content .= '<td>'.$row['location'].'</td>';
									endforeach;
									$this->content .= '</tr>';
								$this->content .= '</tbody>';
							$this->content .= '</table>';
						$this->content .= '</div>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="list-tab">';
						$this->content .= '<div class="table-responsive">';
							$this->content .= '<table id="" class="table display table-hover table-striped table-bordered table-condensed" width="100%">';
								$this->content .= '<thead>';
									$this->content .= '<tr>';
										$this->content .= '<th>Item ID</th>';
										$this->content .= '<th>Name/Category</th>';
										$this->content .= '<th>Assigned To</th>';
										$this->content .= '<th>Department</th>';
										$this->content .= '<th>Date Assigned</th>';
										$this->content .= '<th>Location</th>';
									$this->content .= '</tr>';
								$this->content .= '</thead>';
								$this->content .= '<tbody>';
									foreach ($this->assigneditem_m->getAssignedConsumableItem() as $row):
										$this->content .= '<tr>';
										$this->content .= '<td>'.$row['item_code'].'</td>';
										$this->content .= '<td>'.$row['category_name'].'</td>';
										$this->content .= '<td>'.$row['last_name'].', '.$row['first_name'].' '.$row['middle_initial'].'</td>';
										$this->content .= '<td>'.$row['department_code'].'</td>';
										$this->content .= '<td>'.$row['date_assigned'].'</td>';
										$this->content .= '<td>'.$row['location'].'</td>';
									endforeach;
									$this->content .= '</tr>';
								$this->content .= '</tbody>';
							$this->content .= '</table>';
						$this->content .= '</div>';
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// stock manager
	public function stockManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Stock Items';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Stock Items', 3);
		$this->js = 'stockManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Consumable Items</a></li>';
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Non-Consumable Items</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<div>';
							$this->content .= 'Show/Hide Column: <a class="toggle-vis" data-column="0">Item</a> - <a class="toggle-vis" data-column="1">Name/Category</a> - <a class="toggle-vis" data-column="2">Brand</a> - <a class="toggle-vis" data-column="3">Supplier</a> - <a class="toggle-vis" data-column="4">Actions</a>';
						$this->content .= '</div>';
						$this->content .= '<hr/>';
						$this->content .= '<table id="" class="display table table-striped table-bordered">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Item ID</th>';
									$this->content .= '<th>Name/Category</th>';
									$this->content .= '<th>Brand</th>';
									$this->content .= '<th>Supplier</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								foreach ($this->stock_m->stockItemConsumable() as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['item_code'].'</td>';
									$this->content .= '<td>'.$row['category_name'].'</td>';
									$this->content .= '<td>'.$row['item_brand'].'</td>';
									$this->content .= '<td>'.$row['supplier_name'].'</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= '<table id="" class="display table table-striped table-bordered">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Item ID</th>';
									$this->content .= '<th>Name/Category</th>';
									$this->content .= '<th>Brand</th>';
									$this->content .= '<th>Supplier</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								foreach ($this->stock_m->stockItemNoneConsumable() as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['item_code'].'</td>';
									$this->content .= '<td>'.$row['category_name'].'</td>';
									$this->content .= '<td>'.$row['item_brand'].'</td>';
									$this->content .= '<td>'.$row['supplier_name'].'</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// disposed item manager
	public function disposedItemManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Disposed Items';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Disposed Items ', 3);
		$this->js = 'disposedItemManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
				$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
					$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
						$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Disposed Items</a></li>';
					$this->content .= '</ul>';
					$this->content .= '<div id="myTabContent" class="tab-content">';
						$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
							$this->content .= '<table id="datatable-buttons" class="table table-striped table-bordered">';
								$this->content .= '<thead>';
									$this->content .= '<tr>';
										$this->content .= '<th>Item ID</th>';
										$this->content .= '<th>Name/Category</th>';
										$this->content .= '<th>Brand</th>';
										$this->content .= '<th>Supplier</th>';
									$this->content .= '</tr>';
								$this->content .= '</thead>';
								$this->content .= '<tbody>';
									foreach ($this->stock_m->stockItemDisposed() as $row):
										$this->content .= '<tr>';
										$this->content .= '<td>'.$row['item_code'].'</td>';
										$this->content .= '<td>'.$row['category_name'].'</td>';
										$this->content .= '<td>'.$row['item_brand'].'</td>';
										$this->content .= '<td>'.$row['supplier_name'].'</td>';
									endforeach;
									$this->content .= '</tr>';
								$this->content .= '</tbody>';
							$this->content .= '</table>';
						$this->content .= '</div>';
					$this->content .= '</div>';
				$this->content .= '</div>';
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// trashed item manager
	public function trashedItemManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Trashed Items';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Trashed Items', 3);
		$this->js = 'trashedItemManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
				$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
					$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
						$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Trashed Items</a></li>';
					$this->content .= '</ul>';
					$this->content .= '<div id="myTabContent" class="tab-content">';
						$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
							$this->content .= '<table id="datatable-responsive" class="table table-striped table-bordered">';
								$this->content .= '<thead>';
									$this->content .= '<tr>';
										$this->content .= '<th>Item ID</th>';
										$this->content .= '<th>Name/Category</th>';
										$this->content .= '<th>Brand</th>';
										$this->content .= '<th>Supplier</th>';
									$this->content .= '</tr>';
								$this->content .= '</thead>';
								$this->content .= '<tbody>';
									foreach ($this->item_m->itemTrashed() as $row):
										$this->content .= '<tr>';
										$this->content .= '<td>'.$row['item_code'].'</td>';
										$this->content .= '<td>'.$row['category_name'].'</td>';
										$this->content .= '<td>'.$row['item_brand'].'</td>';
										$this->content .= '<td>'.$row['supplier_name'].'</td>';
									endforeach;
									$this->content .= '</tr>';
								$this->content .= '</tbody>';
							$this->content .= '</table>';
						$this->content .= '</div>';
					$this->content .= '</div>';
				$this->content .= '</div>';
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('asset', $data);
	}

	// private functions
	private function getController(){
		return strtolower(get_class());
	}

	private function redirectTo($to){
		return redirect(base_url().''.$this->getController().'/'.$to);
	}

	private function personId(){
		return $this->session->userdata(base_url().''.$this->getController().'/personId');
	}

	private function loggedIn(){
		return $this->session->userdata(base_url().''.$this->getController().'/loggedIn');
	}

}
