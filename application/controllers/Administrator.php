<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Administrator extends CI_Controller{

	public function __construct(){
		parent::__construct();

		$this->load->helper(array('url', 'html', 'form', 'date'));
		$this->load->library(array('session', 'Pdf'));

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

		$query = 'SELECT * FROM admin a
			JOIN person p ON a.person_person_id = p.person_id
			WHERE p.username = ? AND p.password = ?';
		$sql = $this->db->query($query, array($username, $this->user_m->hash($password)));
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
		$this->load->view('index', $data);

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
					$this->content .= '<a href="#"><span id="user-info" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="'.$this->getController().'" class="label label-info"><i class="fa fa-user"></i> Administrator</span></a>';
					$this->content .= nbs();
					$this->content .= '<a href="'.base_url().''.$this->getController().'/generalSettings/"><span id="user-info" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="General Settings | Change Password" class="label label-warning"><i class="fa fa-cog"></i> General Settings</span></a>';
				$this->content .= '</div>';
			$this->content .= '</div>';
		$this->content .= '</div>';

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
	}

	// dashboard
	public function dashboard(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		// $this->activeUsersCount = $this->user_m->activeUsersCount();

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

		$this->content .= '<div class="row dashboard-content">';
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

		// $data['activeUsersCount'] = $this->activeUsersCount;

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
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
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Create Department</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-department" class="table table-striped table-hover table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Department Code</th>';
									$this->content .= '<th>Descriptive Title</th>';
									$this->content .= '<th>Actions</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= form_open('', 'id="dept-create-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= form_label('Department Code<span class="required">*</span> :', 'department-code');
							$this->content .= form_input('departmentCode', '', 'class="form-control" id="dept-code" data-parsley-required-message="This field is required" required="required"');
							$this->content .= form_label('Descriptive Title<span class="required">*</span> :', 'descriptive-title');
							$this->content .= form_input('descriptiveTitle', '', 'class="form-control" id="dept-title" data-parsley-required-message="This field is required" required="required"');
							$this->content .= br();
							$form_saveBtn_attr = array(
								'name' => 'saveDept',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'create-dept',
								'content' => '<i class="fa fa-save"></i> Save Department'
							);
							$this->content .= form_button($form_saveBtn_attr);
							$this->content .= form_button('cancelBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnCancel"');
						$this->content .= form_close();
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
			// update modal
			$this->content .= '<div id="update-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Update Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= form_open('', 'id="dept-update-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= '<div class="modal-header">';// modal header
								$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
								$this->content .= heading('Update Department', 4);
							$this->content .= '</div>';// .modal header
							$this->content .= '<div class="modal-body">';// modal body
								$this->content .= form_label('Department Code<span class="required">*</span> :', 'department-code');
								$this->content .= '<div id="dept_code"></div>';
								$this->content .= form_label('Descriptive Title<span class="required">*</span> :', 'descriptive-title');
								$this->content .= '<div id="dept_title"></div>';
							$this->content .= '<div class="clearfix"></div>';
							$this->content .= '</div>';// .modal body
							$this->content .= '<div class="modal-footer">';// modal footer
								$form_updateBtn_attr = array(
									'name' => 'updateDept',
									'type' => 'submit',
									'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
									'id' => 'update-dept',
									'content' => '<i class="fa fa-save"></i> Save Changes'
								);
								$this->content .= form_button($form_updateBtn_attr);
								$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
							$this->content .= '</div>';// .modal footer
						$this->content .= form_close();
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Update Modal
			// delete modal
			$this->content .= '<div id="delete-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Delete Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Delete Department', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<p style="color: #800000;"><span class="glyphicon glyphicon-question-sign"></span> Are you sure?</p>';
							$this->content .= '<div id="dept"></div>';
						$this->content .= '</div>';// .modal body
						$this->content .= '<div class="modal-footer">';// modal footer
							$form_deleteBtn_attr = array(
								'name' => 'deleteDept',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-danger myFormBtnSubmit',
								'id' => 'delete-dept',
								'content' => 'Delete'
							);
							$this->content .= form_button($form_deleteBtn_attr);
							$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Delete Modal

		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
	}

	public function departmentJson(){
		$columns = array(
	        0 => 'department_id',
	        1 => 'department_code',
	        2 => 'department_title'
	    );

		$limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];

        $totalData = $this->department_m->allposts_count();
        $totalFiltered = $totalData;

        if(empty($this->input->post('search')['value'])) {
            $posts = $this->department_m->allposts($limit,$start,$order,$dir);
        }else {
            $search = $this->input->post('search')['value'];
            $posts =  $this->department_m->posts_search($limit,$start,$search,$order,$dir);
            $totalFiltered = $this->department_m->posts_search_count($search);
        }

        $data = array();
        if(!empty($posts)){
            foreach ($posts as $post){
                $nestedData['department_id'] = $post->department_id;
                $nestedData['department_code'] = $post->department_code;
                $nestedData['department_title'] = $post->department_title;

                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
		header ( "Content-type: application/json" );
		echo json_encode($json_data);
	}

	public function createDepartmentManager(){
		$postData = $this->input->post('postData');
		$departmentCode = htmlentities(strtoupper($postData[0]));
		$departmentTitle = htmlentities($postData[1]);
		$this->department_m->createDepartment($departmentCode, $departmentTitle);
	}

	public function updateDepartmentManager(){
		$postData = $this->input->post('postData');
		$departmentId = $this->uri->segment(3, 0);
		$departmentCode = htmlentities(strtoupper($postData[0]));
		$departmentTitle = htmlentities($postData[1]);
		$this->department_m->updateDepartment($departmentId, $departmentCode, $departmentTitle);
	}

	public function deleteDepartmentManager(){
		$departmentId = $this->uri->segment(3, 0);
		$this->department_m->deleteDepartment($departmentId);
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
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Create User</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-user" class="table table-striped table-hover table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>ID Number</th>';
									$this->content .= '<th>Last Name</th>';
									$this->content .= '<th>First Name</th>';
									$this->content .= '<th>Middle Initial</th>';
									$this->content .= '<th>Ext. Name</th>';
									$this->content .= '<th>Status</th>';
									$this->content .= '<th>Actions</th>';
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
									$this->content .= '<td>';

									(($row['is_active'] == 1) ? $this->content .= '<a id="'.$row['user_id'].'" class="btn btn-danger btn-xs deactivateBtn"><i class="fa fa-thumbs-o-down"></i> Deactivate</a>' : $this->content .= '<a id="'.$row['user_id'].'" class="btn btn-success btn-xs activateBtn"><i class="fa fa-thumbs-o-up"></i> Activate</a>');

									$this->content .= '</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';

						$this->content .= '<div class="row">';
						$this->content .= '<div class="col-md-12 col-sm-12 col-xs-12">';
							$this->content .= '<div class="x_panel tile">';
								$this->content .= '<div class="x_title">';
									$this->content .= heading('Create User Options', 2);
									$this->content .= '<ul class="nav navbar-right panel_toolbox">';
										$this->content .= '<li><a class="close-link"><i class="fa fa-close"></i></a>';
										$this->content .= '<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>';
									$this->content .= '</ul>';
									$this->content .= '<div class="clearfix"></div>';
								$this->content .= '</div>';
								$this->content .= '<div class="x_content">';
									$this->content .= br();
									$this->content .= '<label>';
										$this->content .= '<input type="radio" name="options" id="option1"> New Account';
									$this->content .= '</label>';
									$this->content .= br();
									$this->content .= '<label>';
										$this->content .= '<input type="radio" name="options" id="option2"> Existing Account';
									$this->content .= '</label>';

								$this->content .= '</div>';
							$this->content .= '</div>';
						$this->content .= '</div>';// .col-md-12 col-sm-12 col-xs-12
						$this->content .= '</div>';

						$this->content .= '<div class="row existing">';
						$this->content .= '<div class="col-md-12 col-sm-12 col-xs-12">';
							$this->content .= '<div class="x_panel tile">';
								$this->content .= '<div class="x_title">';
									$this->content .= heading('Existing Account', 2);
									$this->content .= '<ul class="nav navbar-right panel_toolbox">';
										$this->content .= '<li><a class="close-link"><i class="fa fa-close"></i></a>';
										$this->content .= '<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>';
									$this->content .= '</ul>';
									$this->content .= '<div class="clearfix"></div>';
								$this->content .= '</div>';
								$this->content .= '<div class="x_content">';
									// user-create-form
									$this->content .= form_open('', 'id="user-create-form" data-parsley-validate class="form-horizontal form-label-left"');
										$this->content .= form_label('Select Employee', 'multiple-account');
										$this->content .= '<select id="multiple-account" class="form-control" required>';
											$this->content .= '<option value="">Choose..</option>';
											foreach ($this->user_m->createUserDropdown() as $row) {
												$this->content .= '<option value="'.$row['person_id'].'_'.$row['id_number'].'">'.$row['id_number'].' - '.$row['last_name'].', '.$row['first_name'].' '.$row['middle_initial'].'</option>';
											}
										$this->content .= '</select>';
										$this->content .= br();
										$form_saveBtn_attr = array(
											'name' => 'saveUser',
											'type' => 'submit',
											'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
											'id' => 'create-user',
											'content' => '<i class="fa fa-save"></i> Save User'
										);
										$this->content .= form_button($form_saveBtn_attr);
										$this->content .= form_button('cancelBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnCancel"');
									$this->content .= form_close();
								$this->content .= '</div>';
							$this->content .= '</div>';
						$this->content .= '</div>';// .col-md-12 col-sm-12 col-xs-12
						$this->content .= '</div>';

						$this->content .= '<div class="row new">';
						$this->content .= '<div class="col-md-12 col-sm-12 col-xs-12">';
							$this->content .= '<div class="x_panel tile">';
								$this->content .= '<div class="x_title">';
									$this->content .= heading('Create New User', 2);
									$this->content .= '<ul class="nav navbar-right panel_toolbox">';
										$this->content .= '<li><a class="close-link"><i class="fa fa-close"></i></a>';
										$this->content .= '<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>';
									$this->content .= '</ul>';
									$this->content .= '<div class="clearfix"></div>';
								$this->content .= '</div>';
								$this->content .= '<div class="x_content">';
									// user-new-create-form
									$this->content .= form_open('', 'id="user-new-create-form" data-parsley-validate class="form-horizontal form-label-left"');
										$this->content .= form_label('ID Number<span class="required">*</span> :', 'id-number');
										$this->content .= form_input('idNumber', '', 'class="form-control" id="idNumber" data-parsley-required-message="This field is required" required="required"');
										$this->content .= form_label('Last Name<span class="required">*</span> :', 'last-name');
										$this->content .= form_input('lastName', '', 'class="form-control" id="lname" data-parsley-required-message="This field is required" required="required"');
										$this->content .= form_label('First Name<span class="required">*</span> :', 'first-name');
										$this->content .= form_input('firstName', '', 'class="form-control" id="fname" data-parsley-required-message="This field is required" required="required"');
										$this->content .= form_label('MIddle Initial<span class="required">*</span> :', 'last-name');
										$this->content .= form_input('middleInitial', '', 'class="form-control" id="mname" data-parsley-required-message="This field is required" required="required"');
										$this->content .= form_label('Ext Name :', 'ext-name');
										$this->content .= form_input('extName', '', 'class="form-control" id="ename"');
										$this->content .= form_label('Title :', 'title');
										$this->content .= form_input('title', '', 'class="form-control" id="etitle" data-parsley-required-message="This field is required" required="required"');
										$this->content .= form_label('Department<span class="required">*</span> :', 'emp-department');
										$this->content .= '<select id="edepartment" class="form-control" required>';
											$this->content .= '<option value="">Choose..</option>';
											foreach ($this->department_m->getDepartment() as $row) {
												$this->content .= '<option value="'.$row['department_id'].'">'.$row['department_code'].' - '.$row['department_title'].'</option>';
											}
										$this->content .= '</select>';
										$this->content .= br();
										$form_saveBtn_attr = array(
											'name' => 'saveUser',
											'type' => 'submit',
											'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
											'id' => 'create-user',
											'content' => '<i class="fa fa-save"></i> Save User'
										);
										$this->content .= form_button($form_saveBtn_attr);
										$this->content .= form_button('cancelBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnCancel"');
									$this->content .= form_close();
								$this->content .= '</div>';
							$this->content .= '</div>';
						$this->content .= '</div>';// .col-md-12 col-sm-12 col-xs-12
						$this->content .= '</div>';
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
			// activate modal
			$this->content .= '<div id="activate-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Delete Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Activate User', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<p><span class="glyphicon glyphicon-question-sign"></span> Are you sure?</p>';
						$this->content .= '</div>';// .modal body
						$this->content .= '<div class="modal-footer">';// modal footer
							$form_activateBtn_attr = array(
								'name' => 'activateUser',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'activate-user',
								'content' => 'Activate'
							);
							$this->content .= form_button($form_activateBtn_attr);
							$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Activate Modal
			// deactivate modal
			$this->content .= '<div id="deactivate-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Delete Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Deactivate User', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<p><span class="glyphicon glyphicon-question-sign"></span> Are you sure?</p>';
						$this->content .= '</div>';// .modal body
						$this->content .= '<div class="modal-footer">';// modal footer
							$form_deactivateBtn_attr = array(
								'name' => 'deactivateUser',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-danger myFormBtnSubmit',
								'id' => 'deactivate-user',
								'content' => 'Deactivate'
							);
							$this->content .= form_button($form_deactivateBtn_attr);
							$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Activate Modal
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
	}

	public function createNewUserManager(){
		$postData = $this->input->post('postData');
		$idNum = htmlentities($postData[0]);
		$lName = htmlentities($postData[1]);
		$fName = htmlentities($postData[2]);
		$mName = htmlentities($postData[3]);
		$eName = htmlentities($postData[4]);
		$etitle = htmlentities($postData[5]);
		$eDept = htmlentities($postData[6]);

		$this->user_m->createNewUser($idNum,$lName,$fName,$mName,$eName,$etitle,$eDept);
	}

	public function createUserManager(){
		$postData = $this->input->post('postData');
		$personId = $postData[0];
		$idNumber = $postData[1];
		$this->user_m->createUser($personId,$idNumber);
	}

	public function activateUserManager($userId){
		$userId = $this->uri->segment(3, 0);
		$this->user_m->activateUser($userId);
	}

	public function deactivateUserManager($userId){
		$userId = $this->uri->segment(3, 0);
		$this->user_m->deactivateUser($userId);
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
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Create Employee</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-responsive" class="table table-hover table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
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
									/*$this->content .= '<td>';
										$this->content .= '<div class="btn-group">';
											$this->content .= '<button type="button" id="" class="btn btn-sm btn-primary updateBtn"><i class="fa fa-edit"></i> Edit</button>';
											$this->content .= '<button type="button" id="" class="btn btn-sm btn-danger deleteBtn"><i class="fa fa-times"></i> Delete</button>';
										$this->content .= '</div>';
									$this->content .= '</td>';*/
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= form_open('', 'id="emp-create-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= form_label('ID Number<span class="required">*</span> :', 'id-number');
							$this->content .= form_input('idNumber', '', 'class="form-control" id="idNumber" data-parsley-required-message="This field is required" required="required"');
							$this->content .= form_label('Last Name<span class="required">*</span> :', 'last-name');
							$this->content .= form_input('lastName', '', 'class="form-control" id="lname" data-parsley-required-message="This field is required" required="required"');
							$this->content .= form_label('First Name<span class="required">*</span> :', 'first-name');
							$this->content .= form_input('firstName', '', 'class="form-control" id="fname" data-parsley-required-message="This field is required" required="required"');
							$this->content .= form_label('MIddle Initial<span class="required">*</span> :', 'last-name');
							$this->content .= form_input('middleInitial', '', 'class="form-control" id="mname" data-parsley-required-message="This field is required" required="required"');
							$this->content .= form_label('Ext Name :', 'ext-name');
							$this->content .= form_input('extName', '', 'class="form-control" id="ename"');
							$this->content .= form_label('Title :', 'title');
							$this->content .= form_input('title', '', 'class="form-control" id="etitle" data-parsley-required-message="This field is required" required="required"');
							$this->content .= form_label('Department<span class="required">*</span> :', 'emp-department');
							$this->content .= '<select id="edepartment" class="form-control" required>';
								$this->content .= '<option value="">Choose..</option>';
								foreach ($this->department_m->getDepartment() as $row) {
									$this->content .= '<option value="'.$row['department_id'].'">'.$row['department_code'].' - '.$row['department_title'].'</option>';
								}
							$this->content .= '</select>';
							$this->content .= br();
							$form_saveBtn_attr = array(
								'name' => 'saveEmp',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'create-emp',
								'content' => '<i class="fa fa-save"></i> Save Employee'
							);
							$this->content .= form_button($form_saveBtn_attr);
							$this->content .= form_button('cancelBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnCancel"');
						$this->content .= form_close();
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
		$this->load->view('index', $data);
	}

	public function createEmployeeManager(){
		$postData = $this->input->post('postData');
		$idNumber = htmlentities($postData[0]);
		$lastName = htmlentities(ucfirst($postData[1]));
		$firstName = htmlentities(ucfirst($postData[2]));
		$middleInitial = htmlentities(ucfirst(substr($postData[3], 0, 1)));
		$extName = htmlentities(ucfirst($postData[4]));
		$title = htmlentities(ucfirst($postData[5]));
		$departmentId = htmlentities($postData[6]);
		$this->employee_m->createEmployee($idNumber, $lastName, $firstName, $middleInitial, $extName, $title, $departmentId);
	}

	public function updateEmployeeManager(){ }

	public function deleteEmployeeManager(){ }

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
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Create Supplier</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-supplier" class="table table-hover table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Supplier Name</th>';
									$this->content .= '<th>Supplier Address</th>';
									$this->content .= '<th>Supplier Email</th>';
									$this->content .= '<th>Supplier Contact</th>';
									$this->content .= '<th>Actions</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								foreach ($this->supplier_m->getSupplier() as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['supplier_name'].'</td>';
									$this->content .= '<td>'.$row['supplier_address'].'</td>';
									$this->content .= '<td>'.$row['supplier_email'].'</td>';
									$this->content .= '<td>'.$row['supplier_contact'].'</td>';
									$this->content .= '<td>';
										$this->content .= '<a id="'.$row['supplier_id'].'_'.$row['supplier_name'].'_'.$row['supplier_address'].'_'.$row['supplier_email'].'_'.$row['supplier_contact'].'" class="btn btn-info btn-xs updateBtn"><i class="fa fa-pencil"></i> Edit</a>';
										$this->content .= '<a id="'.$row['supplier_id'].'_'.$row['supplier_name'].'_'.$row['supplier_address'].'_'.$row['supplier_email'].'_'.$row['supplier_contact'].'" class="btn btn-danger btn-xs deleteBtn"><i class="fa fa-times"></i> Delete</a>';
									$this->content .= '</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= form_open('', 'id="sup-create-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= form_label('Supplier Name<span class="required">*</span> :', 'supplier-name');
							$this->content .= form_input('supplierName', '', 'class="form-control" id="sup-name" data-parsley-required-message="This field is required" required="required"');
							$this->content .= form_label('Supplier Address<span class="required">*</span> :', 'supplier-address');
							$this->content .= form_input('supplierAddress', '', 'class="form-control" id="sup-address" data-parsley-required-message="This field is required" required="required"');
							$this->content .= form_label('Supplier Email<span class="required">*</span> :', 'supplier-email');
							$this->content .= form_input('supplierEmail', '', 'class="form-control" id="sup-email" data-parsley-type="email" data-parsley-required-message="This field is required" required="required"');
							$this->content .= form_label('Supplier Contact<span class="required">*</span> :', 'supplier-contact');
							$this->content .= form_input('supplierContact', '', 'class="form-control" id="sup-contact" data-parsley-type="digits" data-parsley-required-message="This field is required" required="required"');
							$this->content .= br();
							$form_saveBtn_attr = array(
								'name' => 'saveSup',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'create-sup',
								'content' => '<i class="fa fa-save"></i> Save Supplier'
							);
							$this->content .= form_button($form_saveBtn_attr);
							$this->content .= form_button('cancelBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnCancel"');
						$this->content .= form_close();
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
			// update modal
			$this->content .= '<div id="update-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Update Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
							$this->content .= form_open('', 'id="sup-update-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= '<div class="modal-header">';// modal header
								$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
								$this->content .= heading('Update Supplier', 4);
							$this->content .= '</div>';// .modal header
							$this->content .= '<div class="modal-body">';// modal body
								$this->content .= form_label('Supplier Name<span class="required">*</span> :', 'supplier-name');
								$this->content .= '<div id="sup_name"></div>';
								$this->content .= form_label('Supplier Address<span class="required">*</span> :', 'supplier-address');
								$this->content .= '<div id="sup_address"></div>';
								$this->content .= form_label('Supplier Email<span class="required">*</span> :', 'supplier-email');
								$this->content .= '<div id="sup_email"></div>';
								$this->content .= form_label('Supplier Contact<span class="required">*</span> :', 'supplier-contact');
								$this->content .= '<div id="sup_contact"></div>';
							$this->content .= '<div class="clearfix"></div>';
							$this->content .= '</div>';// .modal body
							$this->content .= '<div class="modal-footer">';// modal footer
								$form_updateBtn_attr = array(
									'name' => 'updateSup',
									'type' => 'submit',
									'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
									'id' => 'update-sup',
									'content' => '<i class="fa fa-save"></i> Save Changes'
								);
								$this->content .= form_button($form_updateBtn_attr);
								$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
							$this->content .= '</div>';// .modal footer
						$this->content .= form_close();
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Update Modal
			// delete modal
			$this->content .= '<div id="delete-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Delete Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Delete Supplier', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<p style="color: #800000;"><span class="glyphicon glyphicon-question-sign"></span> Are you sure?</p>';
							$this->content .= '<div id="sup"></div>';
						$this->content .= '</div>';// .modal body
						$this->content .= '<div class="modal-footer">';// modal footer
							$form_deleteBtn_attr = array(
								'name' => 'deleteSup',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-danger myFormBtnSubmit',
								'id' => 'delete-sup',
								'content' => 'Delete'
							);
							$this->content .= form_button($form_deleteBtn_attr);
							$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Delete Modal
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
	}

	public function createSupplierManager(){
		$postData = $this->input->post('postData');
		$supplier_name = htmlentities($postData[0]);
		$supplier_address = htmlentities($postData[1]);
		$supplier_email = htmlentities($postData[2]);
		$supplier_contact = htmlentities($postData[3]);
		$this->supplier_m->createSupplier($supplier_name, $supplier_address, $supplier_email, $supplier_contact);
	}

	public function updateSupplierManager(){
		$postData = $this->input->post('postData');
		$supplier_id = $this->uri->segment(3 ,0);
		$supplier_name = htmlentities($postData[0]);
		$supplier_address = htmlentities($postData[1]);
		$supplier_email = htmlentities($postData[2]);
		$supplier_contact = htmlentities($postData[3]);
		$this->supplier_m->updateSupplier($supplier_id, $supplier_name, $supplier_address, $supplier_email, $supplier_contact);
	}

	public function deleteSupplierManager(){
		$supplierId = $this->uri->segment(3, 0);
		$this->supplier_m->deleteSupplier($supplierId);
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
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Create Category</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-display" class="table table-hover table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Category</th>';
									$this->content .= '<th>Items</th>';
									$this->content .= '<th>Actions</th>';
								$this->content .= '</tr>';
							$this->content .= '</thead>';
							$this->content .= '<tbody>';
								$categories = $this->category_m->getCategory();
								foreach ($categories as $row):
									$this->content .= '<tr>';
									$this->content .= '<td>'.$row['category_name'].'</td>';
									$this->content .= '<td>';
										$this->content .= '<a id="'.$row['category_id'].'" class="btn btn-success btn-xs redirectToItemBtn"><i class="fa fa-location-arrow"></i> Items</a>';
									$this->content .= '</td>';
									$this->content .= '<td>';
										$this->content .= '<a id="'.$row['category_id'].'_'.$row['category_name'].'" class="btn btn-info btn-xs updateBtn"><i class="fa fa-pencil"></i> Edit</a>';
										$this->content .= '<a id="'.$row['category_id'].'_'.$row['category_name'].'" class="btn btn-danger btn-xs deleteBtn"><i class="fa fa-times"></i> Delete</a>';
									$this->content .= '</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= form_open('', 'id="cat-create-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= form_label('Category Name<span class="required">*</span> :', 'category-name');
							$this->content .= form_input('categoryName', '', 'class="form-control" id="cat-name" data-parsley-required-message="This field is required" required="required"');
							$this->content .= br();
							$form_saveBtn_attr = array(
								'name' => 'saveCat',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'create-cat',
								'content' => '<i class="fa fa-save"></i> Save Category'
							);
							$this->content .= form_button($form_saveBtn_attr);
							$this->content .= form_button('cancelBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnCancel"');
						$this->content .= form_close();
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
			// update modal
			$this->content .= '<div id="update-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Update Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Update Category', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= form_open('', 'id="cat-update-form" data-parsley-validate class="form-horizontal form-label-left"');
								$this->content .= form_label('Category Name<span class="required">*</span> :', 'category-name');
								$this->content .= '<div id="cat_name"></div>';
								$this->content .= br();
								$this->content .= '<div class="modal-footer">';// modal footer
								$form_updateBtn_attr = array(
									'name' => 'updateCat',
									'type' => 'submit',
									'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
									'id' => 'update-cat',
									'content' => '<i class="fa fa-save"></i> Save Changes'
								);
								$this->content .= form_button($form_updateBtn_attr);
								$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
							$this->content .= '</div>';// .modal footer
							$this->content .= form_close();
						$this->content .= '</div>';// .modal body
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Update Modal
			// delete modal
			$this->content .= '<div id="delete-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Delete Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Delete Category', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<p style="color: #800000;"><span class="glyphicon glyphicon-question-sign"></span> Are you sure?</p>';
							$this->content .= '<div id="cat"></div>';
						$this->content .= '</div>';// .modal body
						$this->content .= '<div class="modal-footer">';// modal footer
							$form_deleteBtn_attr = array(
								'name' => 'deleteCat',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-danger myFormBtnSubmit',
								'id' => 'delete-cat',
								'content' => 'Delete'
							);
							$this->content .= form_button($form_deleteBtn_attr);
							$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Delete Modal
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
	}

	public function createCategoryManager(){
		$postData = $this->input->post('postData');
		$this->category_m->createCategory(htmlentities($postData[0]));
	}

	public function updateCategoryManager(){
		$postData = $this->input->post('postData');
		$categoryId = $this->uri->segment(3, 0);
		$categoryNmae = htmlentities($postData[0]);
		$this->category_m->updateCategory($categoryId, $categoryNmae);
	}

	public function deleteCategoryManager(){
		$categoryId = $this->uri->segment(3, 0);
		$this->category_m->deleteCategory($categoryId);
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
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Add Item</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<table id="datatable-item" class="table table-hover table-condensed table-striped projects nowrap" width="100%">';
							$this->content .= '<thead>';
								$this->content .= '<tr>';
									$this->content .= '<th>Item ID</th>';
									$this->content .= '<th>Category/Name</th>';
									$this->content .= '<th>Brand</th>';
									$this->content .= '<th>Supplier</th>';
									$this->content .= '<th>Comments</th>';
									$this->content .= '<th>History</th>';
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
										$this->content .= '<a id="'.$row['item_id'].'" class="btn btn-primary btn-xs commentsAddBtn"><i class="fa fa-plus-circle"></i> Add</a>';
										$this->content .= '<a id="'.$row['item_id'].'" class="btn btn-info btn-xs commentsViewBtn"><i class="fa fa-eye"></i> View</a>';
									$this->content .= '</td>';
									$this->content .= '<td>';
										$this->content .= '<a id="'.$row['item_id'].'" class="btn btn-warning btn-xs historyViewBtn"><i class="fa fa-history"></i> History</a>';
										$this->content .= '<a id="'.$row['item_id'].'_'.$row['supplier_id'].'_'.$row['supplier_name'].'_'.$row['category_id'].'_'.$row['category_name'].'_'.$row['item_code'].'_'.$row['item_brand'].'_'.$row['item_description'].'_'.$row['or_number'].'_'.$row['item_unit_price'].'_'.$row['date_purchased'].'" class="btn btn-success btn-xs informationBtn"><i class="fa fa-folder"></i> Info </a>';
									$this->content .= '</td>';
									$this->content .= '<td>';
										$this->content .= '<a id="'.$row['item_id'].'_'.$row['supplier_id'].'_'.$row['supplier_name'].'_'.$row['category_id'].'_'.$row['category_name'].'_'.$row['item_code'].'_'.$row['item_brand'].'_'.$row['item_description'].'_'.$row['or_number'].'_'.$row['item_unit_price'].'_'.$row['date_purchased'].'" class="btn btn-info btn-xs updateBtn"><i class="fa fa-pencil"></i> Edit </a>';
										$this->content .= '<a id="'.$row['item_id'].'_'.$row['supplier_id'].'_'.$row['supplier_name'].'_'.$row['category_id'].'_'.$row['category_name'].'_'.$row['item_code'].'_'.$row['item_brand'].'_'.$row['item_description'].'_'.$row['or_number'].'_'.$row['item_unit_price'].'_'.$row['date_purchased'].'" class="btn btn-danger btn-xs deleteBtn"><i class="fa fa-trash-o"></i> Trash </a>';
									$this->content .= '</td>';
								endforeach;
								$this->content .= '</tr>';
							$this->content .= '</tbody>';
						$this->content .= '</table>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= form_open('', 'id="item-create-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
								$this->content .= form_label('Supplier Name<span class="required">*</span> :', 'supplier-name');
								$this->content .= '<select id="item-sup" class="form-control" required>';
									$this->content .= '<option value="">Choose..</option>';
									foreach ($this->supplier_m->getSupplier() as $row) {
										$this->content .= '<option value="'.$row['supplier_id'].'">'.$row['supplier_name'].'</option>';
									}
								$this->content .= '</select>';
							$this->content .= '</div>';
							$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
								$this->content .= form_label('Category Name<span class="required">*</span> :', 'category-name');
								$this->content .= '<select id="item-cat" class="form-control" required>';
									$this->content .= '<option value="">Choose..</option>';
									foreach ($this->category_m->getCategory() as $row) {
										$this->content .= '<option value="'.$row['category_id'].'">'.$row['category_name'].'</option>';
									}
								$this->content .= '</select>';
							$this->content .= '</div>';
							$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
								$this->content .= form_label('Item ID<span class="required">*</span> :', 'item-id');
								$this->content .= form_input('itemCode', '' , 'class="form-control inputNoneConsumable" id="item-code" data-parsley-required-message="This field is required" required="required"');

								//$this->content .= form_input('itemCode', $this->itemCodeGenerate() , 'class="form-control inputConsumable" id="item-code" data-parsley-required-message="This field is required" required="required"');

							$this->content .= '</div>';
							$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
								$this->content .= form_label('OR Number<span class="required">*</span> :', 'or-number');
								$this->content .= form_input('orNumber', '', 'class="form-control" id="or-number" data-parsley-required-message="This field is required" required="required"');
							$this->content .= '</div>';
							$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
								$this->content .= form_label('Item Brand<span class="required">*</span> :', 'item-brand');
								$this->content .= form_input('itemBrand', '', 'class="form-control" id="item-brand" data-parsley-required-message="This field is required" required="required"');
							$this->content .= '</div>';
							$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
								$this->content .= form_label('Date Purchased<span class="required">*</span> :', 'date-purchased');
								$this->content .= form_input('datePurchased', '', 'class="form-control" id="date-purchased" data-parsley-required-message="This field is required" required="required"');
							$this->content .= '</div>';
							$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
								$this->content .= form_label('Item Description<span class="required">*</span> :', 'item-description');
								$this->content .= form_textarea('itemDescription', '', 'class="form-control" id="item-description" data-parsley-required-message="This field is required" required="required"');
							$this->content .= '</div>';
							$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
								$this->content .= form_label('Item Unit Price<span class="required">*</span> :', 'item-unit-price');
								$price_input_attr = array(
									'name' => 'itemUnitPrice',
									'type' => 'number',
									'class' => 'form-control',
									'id' => 'item-unit-price',
									'required' => 'required',
									'data-parsley-required-message' => 'This field is required',
									'data-parsley-type' => 'digits'
								);
								$this->content .= form_input($price_input_attr);
							$this->content .= '</div>';
							$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
							$this->content .= form_label('Is Consumable?<span class="required">*</span> :', 'is-consumable');
							$this->content .= '<p>';
							$checkbox_attr = array(
								'name' => 'is-consumable',
								'class' => 'flat isConsumableChecbox',
								'id' => 'is-consumable'
							);
							$this->content .= form_checkbox($checkbox_attr).'Consumable';
							$this->content .= '</p>';
							$this->content .= '</div>';
							$this->content .= br(2);
							$this->content .= '<div class="col-md-12 col-sm-12 col-xs-12 form-group">';
								$form_saveBtn_attr = array(
									'name' => 'saveSup',
									'type' => 'submit',
									'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
									'id' => 'create-sup',
									'content' => '<i class="fa fa-save"></i> Save Item'
								);
								$this->content .= form_button($form_saveBtn_attr);
								$this->content .= form_button('cancelBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnCancel"');
							$this->content .= '</div>';
						$this->content .= form_close();
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
			// item add comments
			$this->content .= '<div id="item-add-comments-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= form_open('', 'id="item-add-comment-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= '<div class="modal-header">';// modal header
								$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
								$this->content .= heading('Add Comment', 4);
							$this->content .= '</div>';// .modal header
							$this->content .= '<div class="modal-body">';// modal body
								$this->content .= form_label('Item Comment<span class="required">*</span> :', 'item-comment');
								$from_textarea_attr = array(
									'name'  => 'addItemComment',
									'id'    => 'item-add-comment',
									'class' => 'form-control',
									'data-parsley-required-message' => 'This field is required',
									'required' => 'required'
								);
								$this->content .= form_textarea($from_textarea_attr);
							$this->content .= '<div class="clearfix"></div>';
							$this->content .= '</div>';// .modal body
							$this->content .= '<div class="modal-footer">';// modal footer
								$form_updateBtn_attr = array(
									'name' => 'itemAddComment',
									'type' => 'submit',
									'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
									'id' => 'item-add-comment',
									'content' => '<i class="fa fa-save"></i> Save Comment'
								);
								$this->content .= form_button($form_updateBtn_attr);
								$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
							$this->content .= '</div>';// .modal footer
						$this->content  .= form_close();
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';
			$this->content .= '</div>';
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
			// update modal
			$this->content .= '<div id="update-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Update Modal
				$this->content .= '<div class="modal-dialog modal-lg">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= form_open('', 'id="item-update-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= '<div class="modal-header">';// modal header
								$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
								$this->content .= heading('Update Item', 4);
							$this->content .= '</div>';// .modal header
							$this->content .= '<div class="modal-body">';// modal body
								$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
									$this->content .= form_label('Supplier Name<span class="required">*</span> :', 'supplier-name');
									$this->content .= '<select id="item-sup" class="form-control item_sup" required>';
										foreach ($this->supplier_m->getSupplier() as $row) {
											$this->content .= '<option value="'.$row['supplier_id'].'">'.$row['supplier_name'].'</option>';
										}
									$this->content .= '</select>';
								$this->content .= '</div>';
								$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
									$this->content .= form_label('Category Name<span class="required">*</span> :', 'category-name');
									$this->content .= '<select id="item-cat" class="form-control item_cat" required>';
										foreach ($this->category_m->getCategory() as $row) {
											$this->content .= '<option value="'.$row['category_id'].'">'.$row['category_name'].'</option>';
										}
									$this->content .= '</select>';
								$this->content .= '</div>';
								$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
									$this->content .= form_label('Item ID<span class="required">*</span> :', 'item-code');
									$this->content .= '<div id="item_code"></div>';
								$this->content .= '</div>';
								$this->content .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">';
									$this->content .= form_label('Item Brand<span class="required">*</span> :', 'item-brand');
									$this->content .= '<div id="item_brand"></div>';
								$this->content .= '</div>';
								$this->content .= '<div class="col-md-6 col-sm-6 col-xs-6 form-group">';
									$this->content .= form_label('OR Number<span class="required">*</span> :', 'or-number');
									$this->content .= '<div id="or_number"></div>';
								$this->content .= '</div>';
								$this->content .= '<div class="col-md-6 col-sm-6 col-xs-6 form-group">';
									$this->content .= form_label('Item Unit Price<span class="required">*</span> :', 'item-unit-price');
									$this->content .= '<div id="item_unit_price"></div>';
								$this->content .= '</div>';
								$this->content .= '<div class="col-md-6 col-sm-6 col-xs-6 form-group">';
									$this->content .= form_label('Date Purchased<span class="required">*</span> :', 'date-purchased');
									$this->content .= '<div id="date_purchased"></div>';
								$this->content .= '</div>';
								$this->content .= '<div class="col-md-6 col-sm-6 col-xs-6 form-group">';
									$this->content .= form_label('Item Description<span class="required">*</span> :', 'item-description');
									$this->content .= '<div id="item_description"></div>';
								$this->content .= '</div>';
								$this->content .= '<div class="clearfix"></div>';
							$this->content .= '</div>';// .modal body
							$this->content .= '<div class="modal-footer">';// modal footer
								$form_updateBtn_attr = array(
									'name' => 'updateItem',
									'type' => 'submit',
									'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
									'id' => 'update-item',
									'content' => '<i class="fa fa-save"></i> Save Changes'
								);
								$this->content .= form_button($form_updateBtn_attr);
								$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
							$this->content .= '</div>';// .modal footer
						$this->content .= form_close();
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Update Modal
			// delete modal
			$this->content .= '<div id="delete-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Delete Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Trash Item', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<p style="color: #800000;"><span class="glyphicon glyphicon-question-sign"></span> Are you sure? </p>';
							$this->content .= '<div><p style="color:#800000;font-widget:bolder;text-indent:2em;text-decoration:underline;">Note: Make sure that this item is not assigned because this action is irreversible.</p></div>';
							$this->content .= '<div id="item"></div>';
						$this->content .= '</div>';// .modal body
						$this->content .= '<div class="modal-footer">';// modal footer
							$form_deleteBtn_attr = array(
								'name' => 'deleteItem',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-danger myFormBtnSubmit',
								'id' => 'delete-item',
								'content' => 'Trash'
							);
							$this->content .= form_button($form_deleteBtn_attr);
							$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Delete Modal
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
	}

	public function createItemManager(){
		$postData = $this->input->post('postData');
		$supplierId = htmlentities($postData[0]);
		$categoryId = htmlentities($postData[1]);
		$itemCode = htmlentities($postData[2]);
		$itemBrand = htmlentities($postData[3]);
		$itemDescription = htmlentities($postData[4]);
		$orNumber = htmlentities($postData[5]);
		$itemPrice = htmlentities($postData[6]);
		$datePurchased = htmlentities($postData[7]);
		$isConsumable = htmlentities($postData[8]);
		$this->item_m->createItem($supplierId,$categoryId,$itemCode,$itemBrand,$itemDescription,$orNumber,$itemPrice,$datePurchased,$isConsumable);
	}

	public function addCommentItemManager(){
		$itemId = $this->uri->segment(3, 0);
		$postData = htmlentities($this->input->post('postData'));
		$this->item_m->addComment($itemId, $postData);
	}

	public function viewCommentItemManager(){
		$itemId = $this->uri->segment(3, 0);
		$this->item_m->viewComments($itemId);
	}

	public function viewHistoryItemManager(){
		$itemId = $this->uri->segment(3, 0);
		$this->item_m->viewItemHistory($itemId);
	}

	public function updateItemManager(){
		$postData = $this->input->post('postData');
		$itemId = $this->uri->segment(3, 0);
		$supplierId = htmlentities($postData[0]);
		$categoryId = htmlentities($postData[1]);
		$itemCode = htmlentities($postData[2]);
		$itemBrand = htmlentities($postData[3]);
		$itemDescription = htmlentities($postData[4]);
		$orNumber = htmlentities($postData[5]);
		$itemPrice = htmlentities($postData[6]);
		$datePurchased = htmlentities($postData[7]);
		$this->item_m->updateItem($itemId,$supplierId,$categoryId,$itemCode,$itemBrand,$itemDescription,$orNumber,$itemPrice,$datePurchased);
	}

	public function trashItemManager(){
		$itemId = $this->uri->segment(3, 0);
		$this->item_m->trashItem($itemId);
	}

	// assigned non consumable item manager
	public function assignedNonConsumableItemManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Assigned Item Manager';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Assigned Non Consumable Items Manager', 3);
		$this->js = 'assignedNonConsumableItemManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Assigned Non Consumable Items</a></li>';
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Assign Non Consumable Item</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<div class="table-responsive">';
							$this->content .= '<table id="datatable-assigned-item" class="table table-hover table-striped table-bordered table-condensed" width="100%">';
								$this->content .= '<thead>';
									$this->content .= '<tr>';
										$this->content .= '<th>Item ID</th>';
										$this->content .= '<th>Name/Category</th>';
										$this->content .= '<th>Assigned To</th>';
										$this->content .= '<th>Department</th>';
										$this->content .= '<th>Date Assigned</th>';
										$this->content .= '<th>Location</th>';
										$this->content .= '<th>Action</th>';
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
										$this->content .= '<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="Return item" href="#" id="'.$row['assigned_item_id'].'_'.$row['item_id'].'_'.$row['item_code'].'_'.$row['item_brand'].'_'.$row['category_name'].'" class="btn btn-primary btn-xs returnItemBtn"><i class="fa fa-undo"></i></a></td>';
									endforeach;
									$this->content .= '</tr>';
								$this->content .= '</tbody>';
							$this->content .= '</table>';
						$this->content .= '</div>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= form_open('', 'id="assign-item-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= form_label('Employee Name<span class="required">*</span> :', 'employee-name');
							$this->content .= '<select id="emp-name" class="form-control" required>';
								$this->content .= '<option value="">Choose..</option>';
								foreach ($this->employee_m->getEmployee() as $row) {
									$this->content .= '<option value="'.$row['employee_id'].'">'.$row['last_name'].', '.$row['first_name'].' '.$row['middle_initial'].'</option>';
								}
							$this->content .= '</select>';
							$this->content .= form_label('Item Name<span class="required">*</span> :', 'item-name');
							$this->content .= '<select id="item-name" class="form-control" required>';
								$this->content .= '<option value="">Choose..</option>';
								foreach ($this->item_m->getNonConsumableItemForDropdown() as $row) {
									$this->content .= '<option value="'.$row['item_id'].'">'.$row['category_name'].' - '.$row['item_code'].' ('.$row['item_brand'].')</option>';
								}
							$this->content .= '</select>';
							$this->content .= form_label('Location<span></span> :', 'location');
							$this->content .= form_input('location', '', 'class="form-control" id="location" data-parsley-required-message="This field is required" required="required"');
							$this->content .= br();
							$form_saveBtn_attr = array(
								'name' => 'saveAssignItem',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'create-sup',
								'content' => '<i class="fa fa-shopping-cart"></i> Assign'
							);
							$this->content .= form_button($form_saveBtn_attr);
							$this->content .= form_button('cancelBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnCancel"');
						$this->content .= form_close();
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			$this->content .= '</div>';// .x_content
			// return assigned item modal
			$this->content .= '<div id="return-assigned-item-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Delete Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Return Item', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<p style="color: #800000;"><span class="glyphicon glyphicon-question-sign"></span> Are you sure? <span style="color: #800000; font-size: 11px; font-style: oblique; font-widget: bolder; text-decoration: underline;">Note: Action is irreversible.</span></p>';
							$this->content .= '<div id="returnItemContainer"></div>';
						$this->content .= '</div>';// .modal body
						$this->content .= '<div class="modal-footer">';// modal footer
							$form_returnBtn_attr = array(
								'name' => 'returnItemBtn',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'return-item',
								'content' => '<i class="fa fa-undo"></i> Return'
							);
							$this->content .= form_button($form_returnBtn_attr);
							$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Return Item Modal
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
	}

	public function createAssignedItem(){
		$postData = $this->input->post('postData');
		$employeeId = htmlentities($postData[0]);
		$itemId = htmlentities($postData[1]);
		$location = htmlentities($postData[2]);
		$this->assigneditem_m->createNonConsumableAssignedItem($employeeId,$itemId,$location);
	}

	public function returnAssignedNonConsumableItemManager(){
		$assignedItemId = $this->uri->segment(3, 0);
		$itemId = $this->uri->segment(4, 0);
		$this->assigneditem_m->returnAssignedItem($assignedItemId,$itemId);
	}

	// assigned consumable item manager
	public function assignedConsumableItemManager(){
		if(!$this->loggedIn()) $this->redirectTo('login');

		$this->title = 'Assigned Item Manager';
		$this->getController = $this->getController();
		$this->pageTitle = heading('Assigned Consumable Items Manager', 3);
		$this->js = 'assignedConsumableItemManager_js';
		$this->content = '<div class="x_panel">';
			$this->content .= '<div class="x_content">';
			$this->content .= '<div class="" role="tabpanel" data-example-id="togglable-tabs">';
				$this->content .= '<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">';
					$this->content .= '<li role="presentation" class="active"><a href="#tab_content1" id="list-tab" role="tab" data-toggle="tab" aria-expanded="true">Assigned Consumable Items</a></li>';
					$this->content .= '<li role="presentation"><a href="#tab_content2" id="create-tab" role="tab" data-toggle="tab" aria-expanded="true">Assign Consumable Item</a></li>';
				$this->content .= '</ul>';
				$this->content .= '<div id="myTabContent" class="tab-content">';
					$this->content .= '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="list-tab">';
						$this->content .= '<div class="table-responsive">';
							$this->content .= '<table id="datatable-assigned-item" class="table table-hover table-striped table-bordered table-condensed" width="100%">';
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
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= form_open('', 'id="assign-item-form" data-parsley-validate class="form-horizontal form-label-left"');
							$this->content .= form_label('Employee Name<span class="required">*</span> :', 'employee-name');
							$this->content .= '<select id="emp-name" class="form-control" required>';
								$this->content .= '<option value="">Choose..</option>';
								foreach ($this->employee_m->getEmployee() as $row) {
									$this->content .= '<option value="'.$row['employee_id'].'">'.$row['last_name'].', '.$row['first_name'].' '.$row['middle_initial'].'</option>';
								}
							$this->content .= '</select>';
							$this->content .= form_label('Item Name<span class="required">*</span> :', 'item-name');
							$this->content .= '<select id="item-name" class="form-control" required>';
								$this->content .= '<option value="">Choose..</option>';
								foreach ($this->item_m->getConsumableItemForDropdown() as $row) {
									$this->content .= '<option value="'.$row['item_id'].'">'.$row['category_name'].' - '.$row['item_code'].' ('.$row['item_brand'].')</option>';
								}
							$this->content .= '</select>';
							$this->content .= form_label('Location<span></span> :', 'location');
							$this->content .= form_input('location', '', 'class="form-control" id="location" data-parsley-required-message="This field is required" required="required"');
							$this->content .= br();
							$form_saveBtn_attr = array(
								'name' => 'saveAssignItem',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'create-sup',
								'content' => '<i class="fa fa-shopping-cart"></i> Assign'
							);
							$this->content .= form_button($form_saveBtn_attr);
							$this->content .= form_button('cancelBtn', '<i class="fa fa-refresh"></i> Clear', 'class="btn btn-sm btn-default myFormBtnCancel"');
						$this->content .= form_close();
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
		$this->load->view('index', $data);

	}

	public function createAssignConsumableItemManager(){
		$postData = $this->input->post('postData');
		$employeeId = htmlentities($postData[0]);
		$itemId = htmlentities($postData[1]);
		$location = htmlentities($postData[2]);
		$this->assigneditem_m->createAssignedItem($employeeId,$itemId,$location);
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
						$this->content .= '<div class="table-responsive">';
							$this->content .= '<table id="" class="stock-item table table-hover table-striped table-bordered">';
								$this->content .= '<thead>';
									$this->content .= '<tr>';
										$this->content .= '<th>Item ID</th>';
										$this->content .= '<th>Name/Category</th>';
										$this->content .= '<th>Brand</th>';
										$this->content .= '<th>Supplier</th>';
										$this->content .= '<th>Actions</th>';
									$this->content .= '</tr>';
								$this->content .= '</thead>';
								$this->content .= '<tbody>';
									foreach ($this->stock_m->stockItemConsumable() as $row):
										$this->content .= '<tr>';
										$this->content .= '<td>'.$row['item_code'].'</td>';
										$this->content .= '<td>'.$row['category_name'].'</td>';
										$this->content .= '<td>'.$row['item_brand'].'</td>';
										$this->content .= '<td>'.$row['supplier_name'].'</td>';
										$this->content .= '<td>';
											$this->content .= '<a href="#" id="'.$row['item_id'].'" class="btn btn-info btn-xs commentsBtn"><i class="fa fa-eye"></i> Comment(s)</a>';
										$this->content .= '</td>';
									endforeach;
									$this->content .= '</tr>';
								$this->content .= '</tbody>';
							$this->content .= '</table>';
						$this->content .= '</div>';
					$this->content .= '</div>';// .tabpane #tab-content1
					$this->content .= '<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="create-tab">';
						$this->content .= '<div class="table-responsive">';
							$this->content .= '<table id="" class="stock-item table table-hover table-striped table-bordered">';
								$this->content .= '<thead>';
									$this->content .= '<tr>';
										$this->content .= '<th>Item ID</th>';
										$this->content .= '<th>Name/Category</th>';
										$this->content .= '<th>Brand</th>';
										$this->content .= '<th>Supplier</th>';
										$this->content .= '<th>Dispose</th>';
									$this->content .= '</tr>';
								$this->content .= '</thead>';
								$this->content .= '<tbody>';
									foreach ($this->stock_m->stockItemNoneConsumable() as $row):
										$this->content .= '<tr>';
										$this->content .= '<td>'.$row['item_code'].'</td>';
										$this->content .= '<td>'.$row['category_name'].'</td>';
										$this->content .= '<td>'.$row['item_brand'].'</td>';
										$this->content .= '<td>'.$row['supplier_name'].'</td>';
										$this->content .= '<td>';
											$this->content .= '<a href="#" id="'.$row['stock_id'].'_'.$row['item_code'].'_'.$row['category_name'].'_'.$row['item_brand'].'" class="btn btn-primary btn-xs disposeItemBtn"><i class="fa fa-recycle"></i> Dispose</a>';
										$this->content .= '</td>';
									endforeach;
									$this->content .= '</tr>';
								$this->content .= '</tbody>';
							$this->content .= '</table>';
						$this->content .= '</div>';
					$this->content .= '</div>';// .tabpane #tab-content2
				$this->content .= '</div>';// .tab-content
			$this->content .= '</div>';// #tabpanel
			// dispose item modal
			$this->content .= '<div id="dispose-item-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';// Delete Modal
				$this->content .= '<div class="modal-dialog">';// modal dialog
					$this->content .= '<div class="modal-content">';// modal content
						$this->content .= '<div class="modal-header">';// modal header
							$this->content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							$this->content .= heading('Return Item', 4);
						$this->content .= '</div>';// .modal header
						$this->content .= '<div class="modal-body">';// modal body
							$this->content .= '<p style="color: #800000;"><span class="glyphicon glyphicon-question-sign"></span> Are you sure? <span style="color: #800000; font-size: 11px; font-style: oblique; font-widget: bolder; text-decoration: underline;">Note: Action is irreversible.</span></p>';
							$this->content .= '<div id="disposeItemContainer"></div>';
						$this->content .= '</div>';// .modal body
						$this->content .= '<div class="modal-footer">';// modal footer
							$form_disposeBtn_attr = array(
								'name' => 'disposeItemBtn',
								'type' => 'submit',
								'class' => 'btn btn-sm btn-primary myFormBtnSubmit',
								'id' => 'dispose-item',
								'content' => '<i class="fa fa-recycle"></i> Dispose'
							);
							$this->content .= form_button($form_disposeBtn_attr);
							$this->content .= form_button('cancelBtn', 'Cancel', 'class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true"');
						$this->content .= '</div>';// .modal footer
					$this->content .= '</div>';// .modal content
				$this->content .= '</div>';// .modal dialog
			$this->content .= '</div>';// .Dispose Item Modal
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
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
	}

	public function disposeStockManager(){
		$stockId = $this->uri->segment(3, 0);
		$this->stock_m->disposeStock($stockId);
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
							$this->content .= '<div class="table-responsive">';
								$this->content .= '<table id="disposed-item" class="table table-hover table-striped table-bordered">';
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
				$this->content .= '</div>';
			$this->content .= '</div>';// .x_content
		$this->content .= '</div>';// .x_panel

		$data['title'] = $this->title;
		$data['getController'] = $this->getController;
		$data['pageTitle'] = $this->pageTitle;
		$data['js'] = $this->js;
		$data['content'] = $this->content;
		$this->load->view('index', $data);
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
							$this->content .= '<table id="datatable-responsive" class="table table-hover table-striped table-bordered">';
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
		$this->load->view('index', $data);
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

	private function itemCodeGenerate($maxlength = 6) {
		$characters = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z",
						"0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
						"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
		$return_str = "";
		for ($x = 0; $x <= $maxlength; $x++) {
			$return_str .= $characters[rand(0, count($characters)-1)];
		}
		return 'ID-'.$return_str;
	}

}
