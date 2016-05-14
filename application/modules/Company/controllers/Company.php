<?php

class Company extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('M_Company');
	}
	
	function index()
	{
		$this->account->verify_session('company');
		$data['page_header'] = 'Company Profiles';
		$data['company_list'] = $this->create_company_list();
		$data['menu'] = 'company';
		$data['sub_menu'] = 0;
		$data['content_view'] = 'Company/companies_v';
		$this->template->call_dashboard_template($data);
	}
	
	function create_company_list()
	{
		$company_list = "";
		$companies = $this->M_Company->get_all_companies();
		
		if(count($companies)>= 1)
		{
			$project_types = array("isp", "osp", "fat", "mss");
			
			foreach($companies as $company)
			{
				$data_count = array();
				foreach($project_types as $project_type)
				{
					$data_count[$project_type] = $this->M_Company->get_project_by_company($project_type, $company->company_id);
				}
				
				//echo '<img src="'.base_url() . 'assets/logos/'. $company->company_logo.'" alt="" style = 'width: 300px;height:200px;'>'
				$company_list .= '<div class="col-sm-6 col-md-3">
			            <div class="thumbnail" style = "height: 260px;">
			                <img src="'.base_url() . 'assets/logos/'. $company->company_logo.'" alt="" style = "height: 107px;">
			                <div class="caption">
			                    <h4 style = "text-align: center;">'.$company->company_name.'</h4>
			                                                                                                                                                                                                                      
			                    
			                    <div class="m-b-5">
			                        <center>
				                        <a data-href="'.base_url().'Company/edit/'.$company->company_id.'" class="btn btn-primary waves-effect"><i class="zmdi zmdi-edit"></i></a>
				                        <button data-href="'.base_url().'Company/deleteCompany/'.$company->company_id.'" class="btn btn-danger waves-effect data-delete" data-displayname = "'.$company->company_name.'"><i class="zmdi zmdi-close"></i></button>
			                        
			                        
			                        <p class = "c-black" style = "margin-top: 5px;margin-bottom: 0;">Number of projects: '.$this->M_Company->get_total_projects_by_company($company->company_id)->projects.'</p>
			                        <p class = "c-black" style = "margin: 5px;">OSP: '.$data_count["osp"]->projects.' |  FAT: '.$data_count["fat"]->projects.'</p>
			                        <p class = "c-black" style = "margin: 5px;">ISP: '.$data_count["isp"]->projects.' |  MSS: '.$data_count["mss"]->projects.'</p>
			                        
			                        </center>
			                    </div>
			                    
			                </div>
			            </div>
			        </div>';
			}
		}
		else
		{
			$company_list = "<center>No companies have been added</center>";
		}
		return $company_list;
	}
	
	function newcompany()
	{
		$this->account->verify_session('company');
		$data['page_header'] = 'New Company';
		$data['menu'] = 'company';
		$data['sub_menu'] = 0;
		$data['content_view'] = 'Company/new_company_v';
		$this->template->call_dashboard_template($data);
	}
	
	function addCompany()
	{
		$this->account->verify_session('company');
		//echo "<pre>";print_r($_FILES);die;
		
		$errors = $data = array();
		$config['upload_path'] = './assets/logos/';
		$config['allowed_types'] = 'gif|jpg|png';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('company_logo'))
		{
			$error = array('error' => $this->upload->display_errors());
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
		}
		
		if(count($errors) > 0)
		{
			$this->session->set_flashdata('upload_error', $errors);
			redirect(base_url() . 'Company/newcompany');
		}
		else
		{
			$_POST['company_logo'] = $data['upload_data']['file_name'];
			$this->M_Company->add_company();
			redirect(base_url() . 'Company');
		}
	}
	
	function editCompany()
	{
		$this->account->verify_session('company');
		if ($_FILES['company_logo']['size'] != 0) {
			$errors = $data = array();
			$config['upload_path'] = './assets/logos/';
			$config['allowed_types'] = 'gif|jpg|png';
	
			$this->load->library('upload', $config);
	
			if ( ! $this->upload->do_upload('company_logo'))
			{
				$error = array('error' => $this->upload->display_errors());
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());
			}
			
			if(count($errors) > 0)
			{
				$this->session->set_flashdata('upload_error', $errors);
				redirect(base_url() . 'Company/editCompany');
			}
			else
			{
				$_POST['company_logo'] = $data['upload_data']['file_name'];
			}
		}
		
		$company_id = $_POST['company_id'];
		unset($_POST['company_id']);
		
		$this->M_Company->update_details($company_id);
		redirect(base_url() . 'Company');
	}
	
	function deleteCompany($company_id)
	{
		$this->account->verify_session('company');
		$this->db->delete('tbl_company', array('company_id' => $company_id));
		
		redirect(base_url() . 'Company');
	}
	function edit($company_id)
	{
		$this->account->verify_session('company');
		if(!isset($company_id))
		{
			redirect(base_url());
		}
		else
		{
			$data['company_details'] = $this->M_Company->get_company_by_id($company_id);
			$data['page_header'] = "Editting: {$data['company_details']->company_name}";
			$data['menu'] = 'company';
			$data['sub_menu'] = 0;
			$data['content_view'] = 'Company/edit_company_v';
			$this->template->call_dashboard_template($data);
		}
	}
}