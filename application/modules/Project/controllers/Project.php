<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('M_Project');
	}


	function index()
	{
		$this->account->verify_session('projects');
		$data["projects_table"] = $this->create_projects_table();
		$data['page_header'] = "Projects";
		$data['menu'] = 'project';
		$data['sub_menu'] = 0;
		$data['content_view'] = 'Project/manage_project_v';
		$this->template->call_dashboard_template($data);
	}

	function create_projects_table($type = null)
	{
		$this->account->verify_session('projects');
		$projects_table = "";
		$projects = $this->M_Project->get_all_projects();
		$date_today = time();
		if(count($projects))
		{
			$counter = 1;
			foreach ($projects as $key => $value) {
				if($value->project_approved != 0){
					$projects_table .= "<tr>";
					$projects_table .= "<td>{$counter}</td>";
					$projects_table .= "<td>{$value->project_name}</td>";
					// $projects_table .= "<td>" . strtoupper($value->project_type) . "</td>";
					$projects_table .= "<td>" . date("jS F Y", strtotime($value->project_startdate)) . "</td>";
					$projects_table .= "<td>" . date("jS F Y", strtotime($value->project_enddate)) . "</td>";
					$projects_table .= "<td>";
					$project_enddate = strtotime($value->project_enddate . " 23:59:59");
					if(($date_today) > $project_enddate)
					{
						$acceptance_exists = $this->project_acceptance_exists($value->project_id);
						if($acceptance_exists)
						{
							$projects_table .= '<div class = "dot completed"></div> Completed';
							
						}
						else
						{
							$projects_table .= '<div class = "dot waiting"></div> Awaiting Acceptance';
						}
					}
					else if(($date_today) < $project_enddate)
					{
						// echo "{$counter}. Ongoing <br/>";
						$projects_table .= '<div class = "dot ongoing"></div> Ongoing';
					}
					$projects_table .= "</td>";
					$user_permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));
					if($type != "pdf"){
						if($user_permission == "admin")
						{
							$projects_table .= "<td><center>";
							$projects_table .= "<a class = 'edit-button' data-url = '".base_url()."Project/editproject/{$value->project_id}' href = '#'><i class = 'zmdi zmdi-edit'></i></a> | ";
						
							$projects_table .= "<a href = '".base_url()."Project/deleteproject/{$value->project_id}' class = 'delete-button'><i class = 'zmdi zmdi-delete'></i></a>";
							$project_table .= "</td></center>";

						}
					}
					$projects_table .= "</tr>";
					$counter++;
				}
			}
			// die;
		}

		return $projects_table;
	}

	function project_acceptance_exists($project_id)
	{
		return $this->M_Project->project_acceptance_exists($project_id);
	}

	function addproject()
	{
		$this->account->verify_session('projects');
		if(!$_POST)
		{
			$data['view'] = $this->load->view("Project/addproject_v", null, TRUE);
			echo json_encode($data);
		}
		else
		{
			unset($_POST['form_action']);
			$_POST['project_startdate'] = date('Y-m-d', strtotime($_POST['project_startdate']));
			$_POST['project_enddate'] = date('Y-m-d', strtotime($_POST['project_enddate']));
			$_POST['project_createdby'] = $this->session->userdata('user_id');
			$user_persmission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));

			if ($user_persmission == "admin") {
				$_POST['project_approved'] = 1;
			}

			$this->db->insert('tbl_projects', $this->input->post());

			redirect(base_url() . 'Project');
		}
	}

	function editproject($project_id = NULL)
	{
		$this->account->verify_session('editprojects');
		if(!$_POST)
		{
			$data['project_details'] = $this->M_Project->get_project_by_id($project_id);
			$data['view'] = $this->load->view("Project/addproject_v", $data, TRUE);
			echo json_encode($data);
		}
		else
		{
			$project_id = $_POST['project_id'];
			$_POST['project_startdate'] = date('Y-m-d', strtotime($_POST['project_startdate']));
			$_POST['project_enddate'] = date('Y-m-d', strtotime($_POST['project_enddate']));
			unset($_POST['project_id']);
			unset($_POST['form_action']);

			$this->db->where('project_id', $project_id);
			$this->db->update('tbl_projects', $this->input->post());

			redirect(base_url() . 'Project');
		}
	}

	function deleteproject($project_id, $redirect = NULL)
	{
		$this->account->verify_session('projects');
		$this->M_Project->deleteproject($project_id);

		if($redirect == NULL)
		{
			redirect(base_url() . 'Project');
		}
		else
		{
			redirect(base_url() . 'Project/' . $redirect);
		}
	}

	function data($project_type, $project = NULL)
	{
		$this->account->verify_session('acceptance');
		$permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));
		if($project == NULL)
		{
			
			$project_data = $this->M_Project->get_project_by_type($project_type);
			$project_table = $this->create_project_table($project_data);
	
			$data['table_data'] = $project_table;
			$data['project_type'] = $project_type;
			$data['page_header'] = $project_type . " Data";
			$data['menu'] = $project_type;
			if($project_type != "mss"){$data['sub_menu'] = 1;}
			$data['content_view'] = 'Project/project_v';
			
		}
		else
		{
			$project_responses = $this->M_Project->get_project_responses($project_type, $project);
			//echo "<pre>";print_r($project_responses);die;
			$data['project_images'] = $this->get_project_images($project);
			$data['project_responses'] = $this->sanitize_project_responses($project_responses, $project_type);
			$data['project_data'] = $project_responses;
			$data['page_header'] = 'Specific Project';
			$data['menu'] = $project_type;
			$data['sub_menu'] = 1;
			$data['content_view'] = 'Project/'.$project_type.'_v';
		}
		
		$this->template->call_dashboard_template($data);
	}

	function create_project_table($project_data)
	{
		$this->account->verify_session('acceptance');
		$permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));
		$project_table = "";
		if ($project_data) {
			$counter = 1;
			foreach ($project_data as $key => $value) {
				$project_table .= "<tr>";
				$project_table .= "<td>{$counter}</td>";
				$project_table .= "<td>{$value->project_name}</td>";
				$project_table .= "<td>{$value->company_name}</td>";
				$project_table .= "<td>{$value->site_name}</td>";
				/*$start_date = date('j-F-Y', strtotime($value->project_startdate));
				$end_date = date('j-F-Y', strtotime($value->project_enddate));*/
				$date_added = date('j-F-Y', strtotime($value->project_startdate));
				$time_added = date('H:i:s', strtotime($value->project_startdate));
				$date_time_added = $date_added . " at ". $time_added;
				$project_table .= "<td>{$date_time_added}</td>";
				$project_table .= "<td><a href = '".base_url()."Project/data/{$value->project_type}/{$value->project_acceptanceid}'>View Data</a>";
				if($permission == 'admin')
				{
					$project_table .= " | <a class = 'delete-acceptance' href = '".base_url()."Project/deletereport/{$value->project_type}/{$value->project_acceptanceid}'>Delete</a>";
				}
				$project_table .= "</td>";
				$project_table .= "</tr>";
				$counter++;
			}
		}

		return $project_table;
	}

	function deletereport($project_type, $project_acceptanceid)
	{
		$this->account->verify_session('deletedreports');
		$this->M_Project->deletereport($project_type, $project_acceptanceid);

		redirect(base_url() . "Project/data/{$project_type}");
	}

	function deletedreports($project_type)
	{
		$this->account->verify_session('deletedreports');
		$data["project_type"] = $project_type;
		$data['deleted_reports_table'] = $this->create_deleted_reports_table($project_type);
		$data['page_header'] = "Deleted Reports for " . strtoupper($project_type);
		$data['menu'] = $project_type;
		$data['sub_menu'] = 1;
		$data['content_view'] = 'Project/deleted_reports_v';
		$this->template->call_dashboard_template($data);
	}

	function create_deleted_reports_table($project_type)
	{
		$this->account->verify_session('deletedreports');
		$deletedreports = $this->M_Project->get_deleted_reports($project_type);
		$deleted_reports_table = "";

		if(count($deletedreports) > 0)
		{
			$counter = 1;
			foreach ($deletedreports as $key => $value) {
				$deleted_reports_table .= "<tr>";
				$deleted_reports_table .= "<td>".$counter++."</td>";
				$deleted_reports_table .= "<td>{$value->project_name}</td>";
				$deleted_reports_table .= "<td>{$value->company_name}</td>";
				$deleted_reports_table .= "<td>{$value->project_startdate}</td>";
				$deleted_reports_table .= "<td><a class = 'restore_button' href = '".base_url()."Project/restore_report/{$project_type}/{$value->project_acceptanceid}'>Restore</a></td>";
				$deleted_reports_table .= "</tr>";
			}
		}

		return $deleted_reports_table;
	}
	
	function restore_report($project_type, $project_acceptanceid)
	{
		$this->account->verify_session('deletedreports');
		$this->M_Project->restore_report($project_type, $project_acceptanceid);

		redirect(base_url(). "Project/deletedreports/{$project_type}");
	}

	function get_project_images($project_id, $type = NULL)
	{
		$this->account->verify_session('acceptance');
		$image_list = "";
		$images = $this->M_Project->get_project_images($project_id);
		if(count($images) >= 1)
		{
			$counter = 1;
			$image_count = count($images);
			foreach($images as $image)
			{
				$image_class = "";
				$size = getimagesize("quavatelapi/media/".$image->photo_name);
				$width = $size[0];
				$height = $size[1];

				if($height > 800)
				{
					$ration = 800/$height;

					$height = $ration * $height;
					$width = $ration * $width;
				}
				if($type == "pdf"){$image_list .= "<center><h3>{$image->image_title}</h3></center>";
				$image_style = "max-height: 100%"; }
					$image_list .= "<div data-src='".base_url()."quavatelapi/media/".$image->photo_name."' class='col-sm-2 col-xs-6'>
	                                    <div class='lightbox-item'>";
	                $image_list .= ($type != "pdf") ? "<img class = 'pdf_image' src='".base_url()."quavatelapi/media/{$image->photo_name}' alt='' style = 'width: 137px; height:137px;'/>" : "<img class = 'pdf_image' src='".base_url()."quavatelapi/media/{$image->photo_name}' alt='' style = 'width: ".$width."; height: ".$height."'/>";
	                $image_list .= "</div>
	                                </div>";
	                                if($image_count != $counter){
	                               if($type == "pdf"){$image_list .= "<pagebreak />";}
	                           }
                                
                                $counter++;
			}
		}else{
			$image_list = "<div class = 'col-sm-12'>No images to display</div>";
		}
		return $image_list;
	}
	function sanitize_project_responses($responses, $project_type)
	{
		$this->account->verify_session('acceptance');
		$sanitized = array();
		if($project_type != "fat" && $project_type != "mss"){
			$questions = array();
			foreach ($responses as $key => $value) {
				$sanitized['project_id'] = $value->project_id;
				$sanitized['project_name'] = $value->project_name;
				$sanitized['company_name'] = $value->company_name;
				$sanitized['site_name'] = $value->site_name;
				$sanitized['location'] = $value->project_location;
				$sanitized['project_type'] = $value->project_type;
				$sanitized['start_date'] = $value->project_startdate;
				$sanitized['end_date'] = $value->project_enddate;
				$sanitized['company_logo'] = $value->company_logo;
				$sanitized['user_firstname'] = $value->user_firstname;
				$sanitized['user_lastname'] = $value->user_lastname;
				$sanitized['user_image'] = $value->user_image;
				$sanitized['project_acceptanceid'] = $value->project_acceptanceid;
				$value = (array)$value;
				foreach ($value as $k => $v) {
					if($k == "response_question"){
						$questions[$v]["response"] = $value['response_answer'];
						$questions[$v]["remark"] = $value['response_remarks'];
					}
				}
				
			}
			$sanitized['questions'] = $questions;
		}
		else if($project_type == "fat"){
			$redundant_data = array('fat_fibrenumber', 'fat_ab', 'fat_ba');
			$wanted_data = array('project_acceptanceid', 'project_id', 'project_name', 'company_name', 'company_logo', 'site_name', 'project_location', 'project_type', 'project_startdate', 'project_enddate', 'fat_closures', 'fat_splices', 'fat_cablespecs', 'fat_maxspiceloss', 'fat_connectorloss', 'fat_cabletype', 'fat_wavelength', 'fat_cablelength', 'fat_fibrelength', 'fat_testdate', 'fat_clientengineer', 'fat_clientengineer_remarks', 'fat_accepted_date', 'user_firstname', 'user_lastname', 'user_image');
			foreach($responses as $key => $value){
				$value = (array)$value;
				foreach($value as $k => $v)
				{
					if(in_array($k, $wanted_data) && !in_array($k, $redundant_data))
					{
						$sanitized[$k] = $v;
					}
					else if(in_array($k, $redundant_data))
					{
						$sanitized['Attenuation'][$value['fat_responseid']] = array(
							$redundant_data[0] => $value[$redundant_data[0]],
							$redundant_data[1] => $value[$redundant_data[1]],
							$redundant_data[2] => $value[$redundant_data[2]]
						);
					}
				}
				
			}
		}
		else
		{
			//echo "<pre>";print_r($responses);die;
			$sanitized = $responses;
		}
		return $sanitized;
}

function export($project_type, $project_id, $export_type)
{
	$this->account->verify_session('acceptance');
	$document_names = array(
		'fat' => "FIBRE OPTICS ACCEPTANCE TEST",
		'isp' => "ISP ACCEPTANCE FORM",
		'osp' => "OSP ACCEPTANCE FORM",
		'mss' => "MANHOLE SURVEY SHEET"
	);

	$project_responses = $this->M_Project->get_project_responses($project_type, $project_id);
	$data['project_responses'] = $this->sanitize_project_responses($project_responses, $project_type);
	$data['view_type'] = 'pdf';
	$data['view'] = 'Project/' . $project_type . '_table_v';
	$data['project_images'] = $this->get_project_images($project_id, 'pdf');
	switch($export_type)
	{
		case 'pdf':
			$this->export->export_pdf($data, $project_type);
			break;
		case 'excel':
			$data['excel_data']['unique_title'] = $project_type . date('dFyhis');
			if(!is_array($data['project_responses']))
			{
				$data['project_responses'] = (array)$data['project_responses'];
			}
			//echo "<pre>";print_r($data);
			$data['excel_data']['Title'] = ($project_type == "fat") ? 'OTDR Results for: ' . $data['project_responses']['project_name'] : "Project Name: " . $data['project_responses']['project_name'];
			$data['excel_data']['document_name'] = $document_names[$project_type];
			$data['excel_data']['actual_data'] = $this->create_project_response_data_excel($project_type, $data['project_responses']);
			$this->export->export_excel($data, $project_type);
			break;
		
	}
	
}

function export_projects($type)
{
	$this->account->verify_session('projects_export');

	if ($type == "pdf") {
		$data['table_data'] = $this->create_projects_table($type);
		$data['title'] = "Quavatel-Project-List-" . date('his');
		$data['view'] = 'Project/project_pdf';
		$data['orientation'] = "landscape";

		$this->export->export_report($data, $type);
	}
	else if ($type == "excel") {
		$projects = $this->M_Project->get_all_projects();

		$excel_data = array();
		$excel_data[0] = [
			'No.',
			'Project Name',
			'Start Date',
			'End Date',
			'Status'
		];
		if ($projects) {
			$counter = 1;
			foreach ($projects as $project) {
				if ($project->project_approved != 0) {
					$project_status = "";
					$date_today = time();
					$project_enddate = strtotime($project->project_enddate . " 23:59:59");
					if(($date_today) > $project_enddate)
					{
						$acceptance_exists = $this->project_acceptance_exists($project->project_id);
						if($acceptance_exists)
						{
							$project_status = 'Completed';
							
						}
						else
						{
							$project_status = 'Awaiting Acceptance';
						}
					}
					else if(($date_today) < $project_enddate)
					{
						$project_status .= 'Ongoing';
					}
					$excel_data[] = [
						$counter,
						$project->project_name,
						date("jS F Y", strtotime($project->project_startdate)),
						date("jS F Y", strtotime($project->project_enddate)),
						$project_status
					];

					$counter++;			
				}
			}

			$this->export->quick_array_excel($excel_data, "QUAVATEL PROJECT LIST");
		}
	}
	

	$project_pdf_table = "";
	$excel_export = array();

}
function quick_edit()
{
	//$this->db->insert('tbl_users', array('user_firstname' => 'David', 'user_lastname' => 'Kyarie', 'user_emailaddress' => 'david.kyarie@quavatel.co.ke', 'user_password' => md5('123456'), 'user_type' => 'user'));
	// echo "<pre>";print_r($this->db->query("SELECT * FROM tbl_projects WHERE project_id = 254")->result());die;
}

	function create_project_response_data_excel($project_type, $project_responses)
	{
		$this->account->verify_session('acceptance');
		// echo "<pre>";print_r($project_responses);die;
		$excel_data = array();
		if($project_type == "ISP" || $project_type == "OSP" || $project_type == "isp" || $project_type == "osp")
		{
			$excel_data[] = array(
				'No.',
				'Question',
				'Answer',
				'Remark'
			);

			$counter = 1;
			foreach ($project_responses['questions'] as $key => $value) {
				if($key != 'Date Accepted' && $key != 'Client Engineer Remarks' & $key != 'Client Engineer'){
					$excel_data[] = array(
						$counter,
						$key,
						$value['response'],
						$value['remark']
					);
				}
				else
				{
					//$excel_data[] = array();
					$excel_data[] = array(
						$key,
						$value['response']
					);
				}

				
				$counter++;
			}
		}
		

		return $excel_data;
	}

	function deleted()
	{
		$this->account->verify_session('deletedprojects');
		$data["projects_table"] = $this->create_deleted_projects_table();
		$data['page_header'] = "Deleted Projects";
		$data['menu'] = 'project';
		$data['sub_menu'] = 0;
		$data['content_view'] = 'Project/deleted_projects_v';
		$this->template->call_dashboard_template($data);
	}

	function create_deleted_projects_table()
	{
		$deleted_projects = $this->M_Project->get_inactive_projects();
		// echo "<pre>";print_r($deleted_projects);die;
		$projects_table = "";
		if(count($deleted_projects) > 0)
		{
			$counter = 1;
			foreach ($deleted_projects as $key => $value) {
				$projects_table .= "<tr>";
				$projects_table .= "<td>{$counter}</td>";
				$projects_table .= "<td>{$value->project_name}</td>";
				$projects_table .= "<td>".date('jS F Y', strtotime($value->project_deactivatedon))." at " . date('H:i:s', strtotime($value->project_deactivatedon)) . "</td>";
				$projects_table .= "<td><a href = '".base_url()."Project/restore/{$value->project_id}' class = 'restore_project'>Restore this Project</a></td>";
				$projects_table .= "</tr>";

				$counter++;
			}
		}

		return $projects_table;
	}

	function restore($project_id)
	{
		$this->M_Project->restore_project($project_id);

		redirect(base_url() . "Project");
	}

	/*function move_projects_to_project_responses()
	{
		$sql = "SELECT * FROM tbl_projects WHERE project_id NOT IN (SELECT project_id FROM tbl_projects_acceptance)";

		$projects = $this->db->query($sql)->result();
		$insertable = array();
		foreach ($projects as $key => $value) {
			if($value->project_type != NULL || $value->project_type != "")
			{
				$insertable[] = $value;
			}
		}
		$this->db->insert_batch("tbl_projects_acceptance", $insertable);
	}

	function update_mss_table()
	{
		$sql = "SELECT mss_id, mss_projectid FROM tbl_mss WHERE mss_projectid IN (SELECT project_id FROM tbl_projects_acceptance)";

		$details = $this->db->query($sql)->result();
		$mss_project_details = array();

		foreach ($details as $key => $value) {
			if($value->mss_projectid != 521)
			{
				$sql = "SELECT project_acceptanceid FROM tbl_projects_acceptance WHERE project_type = 'mss' AND project_id = {$value->mss_projectid}";

				$mss_project_details[$value->mss_id] = $this->db->query($sql)->result();
			}
			
		}

		foreach ($mss_project_details as $key => $value) {
			foreach ($value as $k => $v) {
				// $sanitized_mss_project_details[$key][] = $v->project_acceptanceid;
				$sql = "UPDATE tbl_mss SET mss_projectid = {$v->project_acceptanceid} WHERE mss_id = {$key}";
				$this->db->query($sql);
			}
		}

	}

	function update_images_table()
	{
		$sql = "SELECT photo_id, photo_projectid FROM tbl_photos WHERE photo_projectid IN (SELECT project_id FROM tbl_projects_acceptance)";

		$details = $this->db->query($sql)->result();
		$photo_projectdetails = array();
		foreach ($details as $key => $value) {
			if($value->photo_projectid != 521)
			{
				$sql = "SELECT project_acceptanceid FROM tbl_projects_acceptance WHERE project_id = {$value->photo_projectid}";

				$photo_projectdetails[$value->photo_id] = $this->db->query($sql)->result();
			}
			
		}
		foreach ($photo_projectdetails as $key => $value) {
			foreach ($value as $k => $v) {
				// $sanitized_mss_project_details[$key][] = $v->project_acceptanceid;
				$sql = "UPDATE tbl_photos SET photo_projectid = {$v->project_acceptanceid} WHERE photo_id = {$key}";
				$this->db->query($sql);
			}
		}
	}*/

	function awaiting()
	{
		$this->account->verify_session('awaiting_projects');
		$data['awaiting_table'] = $this->create_awaiting_table();
		$data['page_header'] = "Awaiting Approval";
		$data['menu'] = 'project';
		$data['content_view'] = 'Project/awaiting_project_v';
		$this->template->call_dashboard_template($data);
	}

	function create_awaiting_table()
	{
		$this->account->verify_session('awaiting_projects');
		$awaiting = $this->M_Project->get_projects_awaiting();
		$awaiting_table = "";

		if(count($awaiting) > 0)
		{
			$counter = 1;
			foreach ($awaiting as $key => $value) {
				$awaiting_table .= "<tr>";
				$awaiting_table .= "<td>".$counter++."</td>";
				$awaiting_table .= "<td>".$value->project_name."</td>";
				$awaiting_table .= "<td>".date('d-m-Y', strtotime($value->project_startdate))."</td>";
				$awaiting_table .= "<td>".date('d-m-Y', strtotime($value->project_enddate))."</td>";
				$awaiting_table .= "<td>".$value->created_by."</td>";
				$awaiting_table .= "<td><a href = '".base_url()."Project/approveproject/{$value->project_id}' class = 'approve_project'>Approve</a></td>";
				$awaiting_table .= "<td><a href = '".base_url()."Project/deleteproject/{$value->project_id}/awaiting' class = 'delete_project'>Delete</a></td>";
				$awaiting_table .= "</tr>";
			}
		}

		return $awaiting_table;
	}

	function approveproject($project_id)
	{
		$this->account->verify_session('awaiting_projects');

		$this->M_Project->approve_project($project_id);

		redirect(base_url() . "Project/awaiting");
	}
	
	function completion($project_id, $action)
	{
		if($action != NULL && $project_id != NULL)
		{
			$complete_flag = 0;
			switch($action)
			{
				case 'mark':
					$complete_flag = 1;
				break;
				case 'unmark':
					$complete_flag = 0;
				break;
				default:
					echo "<h2>You are not allowed to view this page</h2>";die;
				break;
			}
			
			$this->M_Project->mark_project_completion($project_id, $complete_flag);
			
			redirect(base_url() . "Project");
		}
		else
		{
			echo "<h2>You are not allowed to view this page</h2>";die;
		}
	}
}