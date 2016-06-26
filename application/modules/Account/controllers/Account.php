<?php

class Account extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('M_Account');
		$this->load->helper('string');
		
	}
	
	function login()
	{
		$this->session->sess_destroy();
		$this->load->view('Account/login');
	}
	
	function authenticate()
	{
		if($this->input->post()){
			$auth = $this->M_Account->auth($this->input->post('user_emailaddress'), $this->input->post('user_password'));
			if(count($auth) == 1)
			{
				$this->session->set_userdata(array(
					'user_id' => $auth->user_id,
					'logged_in' => 1
				));
				
				redirect(base_url());
			}
			else
			{
				$this->session->set_flashdata('error', 'Incorrect username or password. Please try again');
				redirect(base_url(). 'Account/login');
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Scum detected');
			redirect(base_url(). 'Account/login');
		}
	}
	
	function logout()
	{
		$this->verify_session('logout');
		$this->session->sess_destroy();
		redirect(base_url() . 'Account/login');
	}
	function users($user_id=NULL)
	{
		$this->verify_session('users');
		if(!isset($user_id)){
			$data['user_table'] = $this->create_users_table($this->M_Account->get_users());
			$data['page_header'] = 'User Accounts';
		}
		else
		{
			$data['details'] = $this->M_Account->get_user_by_id('user_id', $user_id);
		}
		
		$data['menu'] = 'users';
		$data['sub_menu'] = 0;
		$data['content_view'] = 'Account/users_v';
		$this->template->call_dashboard_template($data);
	}
	
	function adduser()
	{
		// echo "<pre>";print_r($this->input->post());die;
		if($_FILES['user_image']['tmp_name'])
		{
			$config['upload_path'] = './uploads/user_profiles/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '100';
			$config['max_width']  = '1366';
			$config['max_height']  = '768';

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('user_image'))
			{
				$error = array('error' => $this->upload->display_errors());
				echo "<pre>";print_r($error);die;
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());

				$_POST['user_image'] = base_url() . "uploads/user_profiles/" . $data['upload_data']['file_name'];
			}


		}

		$_POST['user_password'] = $this->create_password();
		
		$data = $this->get_email_address($this->input->post('user_emailaddress'));
		if($data == 0){
			$insert_data = $this->input->post();
			$template_data["first_name"] = $this->input->post('user_firstname');
			$template_data["email_address"] = $this->input->post('user_emailaddress');
			$template_data["last_name"] = $this->input->post('user_lastname');
			$template_data["password"] = $this->input->post('user_password');
			
			$email_data["email_address"] = $this->input->post('user_emailaddress');
			$email_data["subject"] = "Quavatel Credentials";
			$email_data["message"] = $this->load->view("Mail/user_registered_v", $template_data, TRUE);
			$email_sent = $this->mail->send_mail($email_data);
			
			if($email_sent->status)
			{
				$added = $this->M_Account->adduser($insert_data);
				if($added)
				{
					redirect(base_url().'Account/users');
				}
			}
			else
			{
				echo "not sent <br/>";
				echo "<pre>";print_r($email_sent);die;
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Email already exists');
			$this->session->set_flashdata('user_firstname', $this->input->post('user_firstname'));
			$this->session->set_flashdata('user_lastname', $this->input->post('user_lastname'));
			$this->session->set_flashdata('user_email', $this->input->post('user_emailaddress'));
			redirect(base_url().'Account/createuser');
		}
	}
	
	function action($activity, $user_id)
	{
		if($activity== "deactivate")
		{
			$this->M_Account->activation(0, $user_id);
			redirect(base_url() . 'Account/users');
		}
		else if($activity== "activate")
		{
			$this->M_Account->activation(1, $user_id);
			redirect(base_url() . 'Account/users');
		}
		else if($activity== "rstpwd")
		{
			$user_details = $this->M_Account->get_user_by_id('user_id', $user_id);
			
			$new_password = $this->create_password();
			$this->M_Account->reset_password($new_password, $user_id);
			
			$template_data["first_name"] = $user_details->user_firstname;
			$template_data["email_address"] = $user_details->user_emailaddress;
			$template_data["last_name"] =$user_details->user_lastname;
			$template_data["password"] = $new_password;
			
			$email_data["email_address"] = $user_details->user_emailaddress;
			$email_data["subject"] = "Password Reset";
			$email_data["message"] = $this->load->view("Mail/user_pwdreset_v", $template_data, TRUE);
			$email_sent = $this->mail->send_mail($email_data);
			redirect(base_url() . 'Account/users');
		}
		
		else if($activity == "edit")
		{
			if(!$_POST)
			{
				$this->verify_session('users');
				$data['content_view'] = 'Account/createuser_v';
				$data['sub_menu'] = 0;
				$data['menu'] = 'users';
				$data['user_details'] = $this->M_Account->get_user_by_id('user_id', $user_id);
				$data['page_header'] = 'Edit User Details: ' . $this->M_Account->get_user_by_id('user_id', $user_id)->user_firstname;
				$this->template->call_dashboard_template($data);
			}
			else
			{
				if($_FILES['user_image']['tmp_name'])
				{
					$config['upload_path'] = './uploads/user_profiles/';
					$config['allowed_types'] = 'gif|jpg|png';
					$config['max_size']	= '2000000';
					$config['max_width']  = '50000';
					$config['max_height']  = '50000';

					$this->load->library('upload', $config);

					if ( ! $this->upload->do_upload('user_image'))
					{
						$error = array('error' => $this->upload->display_errors());
						$this->session->set_flashdata('image_error', $error);
						$_POST = array();
						redirect(base_url() . 'Account/action/edit/' . $user_id);
					}
					else
					{
						$data = array('upload_data' => $this->upload->data());

						$_POST['user_image'] = base_url() . "uploads/user_profiles/" . $data['upload_data']['file_name'];
					}


				}

				$user_email = $this->M_Account->get_user_by_id('user_id', $user_id)->user_emailaddress;
				if ($user_email != $this->input->post('user_emailaddress')) {
					$data = $this->get_email_address($this->input->post('user_emailaddress'));
					if($data == 0){
						$this->M_Account->update_user_data($this->input->post(), $user_id);
					}
					else
					{
						$this->session->set_flashdata('error', 'The email you entered already exists');
						$_POST = array();
						redirect(base_url() . 'Account/action/edit/' . $user_id);
					}
				}
				else
				{
					$this->M_Account->update_user_data($this->input->post(), $user_id);
				}

				redirect(base_url() . 'Account/users');
			}
		}
	}
	
	function createuser()
	{	
		$this->verify_session('users');
		$data['content_view'] = 'Account/createuser_v';
		$data['sub_menu'] = 0;
		$data['menu'] = 'users';
		$data['page_header'] = 'Add a new user';
		$this->template->call_dashboard_template($data);
	}
	function create_users_table($users)
	{
		$table = "";
		if(is_array($users) && !empty($users))
		{
			$counter = 1;
			foreach($users as $user)
			{
				$active = "<a href = '".base_url()."Account/action/activate/{$user->user_id}' class = 'model_button' style = 'color:green;'>Activate User</a>";
				if($user->user_status == 1)
				{
					$active = "<a href = '".base_url()."Account/action/deactivate/{$user->user_id}' class = 'model_button' style = 'color: red;'>Dectivate User</a>";
				}

				if($user->user_image)
				{
					$image = "<img style = 'width: 150px;height:50px;' src = '{$user->user_image}' />";
				}
				else
				{
					$image = "<img style = 'width: 150px;height:50px;' src = '".base_url()."assets/img/employee.jpg' />";
				}
				$table .= "<tr>
				<td>{$counter}</td>
				<td>{$user->user_firstname}</td>
				<td>{$user->user_lastname}</td>
				<td>{$user->user_emailaddress}</td>
				<td>{$image}</td>
				<td>{$active}</td>
				<td><a href = '".base_url()."Account/action/rstpwd/{$user->user_id}'>Reset Password</a></td>
				<td><a href = '".base_url()."Account/action/edit/{$user->user_id}'>Edit User</a></td>
				<td><a href = '".base_url()."Account/deleteuser/{$user->user_id}' class = 'delete_user'>Delete User</td>
				</tr>";
				$counter++;
			}
		}
		
		return $table;
	}
	
	function create_password()
	{
		return random_string('numeric', 4);
	}
	
	function get_email_address($email_address)
	{
		$data = 0;
		if($this->M_Account->get_user_by_id('user_emailaddress', $email_address))
		{
			$data = 1;
		}
		
		return $data;
	}
	
	function verify_session($page=NULL)
	{
		if(!$this->session->userdata('logged_in'))
		{
			redirect(base_url() . 'Account/login');
		}
		else 
		{
			$user_permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));
			if($user_permission != "admin"){
				$permissions = array(
					'project_manager' => array(
						'home',
						'projects',
						'projects_export',
						'acceptance',
						'laboursheet',
						'wagestructure',
						'logout'
					),
					'finance' => array(
						'home',
						'laboursheet',
						'projects',
						'projects_export',
						'wagestructure',
						'wagestructure_edit',
						'logout'
					),
					'acceptance' => array(
						'acceptance',
						'home',
						'logout'
					),
					'noc_engineer' => array(
						'fault_reporting',
						'home',
						'logout'
					)
				);

				if(!in_array($page, $permissions[$user_permission]))
				{
					redirect(base_url() . "Error/access_denied");
					/* $this->load->module("Error");
					$this->error->access_denied(); */
				}
			}
		}
	}
	
	function deleteuser($user_id)
	{
		$this->M_Account->delete_user($user_id);
		
		redirect(base_url() . 'Account/users');
	}
}