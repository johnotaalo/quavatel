<?php
error_reporting(E_ALL);
class FaultReporting extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('M_Faultreporting');
	}

	function index()
	{
		$this->faultList();
	}

	function faultList()
	{
		$this->account->verify_session('fault_reporting');
		$faults = $this->M_Faultreporting->getFaultReports();
		$faults_table = "";
		if ($faults) {
			$counter = 1;
			foreach ($faults as $key => $value) {
				$faults_table .= "<tr>";
				$faults_table .= "<td>{$counter}</td>";
				$faults_table .= "<td>{$value->ticket_no}</td>";
				$faults_table .= "<td>{$value->station_name}</td>";
				$faults_table .= "<td>".date('d-m-Y', strtotime($value->date_time_reported)). " at ". date('h:i:s a', strtotime($value->date_time_reported)) . "</td>";

				$ticket_confirmation_date = ($value->ticket_confirmation_time) ? date('d-m-Y', strtotime($value->ticket_confirmation_time)) . " at " . date('h:i:s a', strtotime($value->ticket_confirmation_time)) : "Awaiting Confirmation";
				$faults_table .= "<td>{$ticket_confirmation_date}</td>";
				$faults_table .= "<td>";
				if($value->status == "open")
				{
					$faults_table .= "<a href = '#' class = 'label label-danger'>Open</a>";
				}
				else if($value->status == "confirmed")
				{
					$faults_table .= "<a href = '#' class = 'label label-warning'>Confirmed</a>";
				}

				else if($value->status == "submitted")
				{
					$faults_table .= "<a href = '#' class = 'label label-warning'>Submitted for Clearance</a>";
				}
				else if($value->status == "cleared" || $value->status == "closed")
				{
					$faults_table .= "<a href = '#' class = 'label label-success'>Cleared</a>";
				}
				$faults_table .= "</td>";
				$faults_table .= "<td><a href = '".base_url()."FaultReporting/detail/{$value->report_id}' class = 'label label-info'>View Report</a></td>";
				$faults_table .= "</tr>";

				$counter++;
			}
		}
		
		$data['faults_table'] = $faults_table;
		$count_data = $this->M_Faultreporting->get_fault_counter();
		$status_counts = array();
		$overall_keys = array('open', 'cleared', 'confirmed');

		$status_counts['all'] = count($faults);
		if($count_data)
		{
			foreach ($count_data as $key => $value) {
				$status_counts[$value->status] = $value->number;
			}

			$status_counts_keys = array_keys($status_counts);

			foreach ($overall_keys as $key => $value) {
				if (!in_array($value, $status_counts_keys)) {
					$status_counts[$value] = 0;
				}
			}
		}
		else
		{
			foreach ($overall_keys as $key => $value) {
				$status_counts[$value] = 0;
			}
		}
		$data['fault_counter'] = $status_counts;
		$data['content_view'] = 'FaultReporting/faults_v';
		$data['sub_menu'] = 0;
		$data['menu'] = 'fault_reporting';
		$data['page_header'] = 'Fault Reporting';
		$this->template->call_dashboard_template($data);
	}

	function AddReport()
	{
		$this->account->verify_session('fault_reporting');

		if($this->input->post())
		{
			$user = $this->M_Account->get_user_by_id('user_id', $this->session->userdata('user_id'));

			$user_initials = substr($user->user_firstname, 0 ,1) . substr($user->user_lastname, 0, 1);

			$ticket_no = $this->create_ticket_no($user_initials);

			$technician_details = $this->M_Faultreporting->get_technician_details($this->input->post('technician_id'));

			$station_name = $this->input->post('station_name');
			$html_message = $this->load->view('Mail/ticket_email', ['ticket' => $ticket_no, 'station' => $station_name], true);

			if($station_name !== ""){
				$post_data = [
					'station_name'	=> $station_name,
					'ticket_no'		=> $ticket_no,
					'company_id'	=> $this->input->post('company_id'),
					'reported_by'	=> $this->session->userdata('user_id')
				];

				$this->send_mail($technician_details->user_emailaddress, $technician_details->user_firstname ." " . $technician_details->user_lastname, "New Fault Report: Ticket Number" , $html_message);

				$insertion = $this->M_Faultreporting->save_report($post_data);
				if ($insertion) {
					$technician_assigned = $this->M_Faultreporting->addTechnician(['technician_id' => $this->input->post('technician_id')]);
					redirect(base_url() . "FaultReporting");
				}else{
					echo "There was an error adding your report! Please try again later";
				}
			}
			else
				die("Something went wrong. Please try again");

			
		}
		else
		{
			$technical_staff = $this->M_Faultreporting->get_technical_staff();
			$clients = $this->M_Faultreporting->get_clients();
			$technical_staff_data = $clients_array = array();
			if ($technical_staff) {
				$counter = 1;
				foreach ($technical_staff as $staff) {
					$technical_staff_data[] = [
						'id' => $staff->user_id,
						'text' => $staff->user_firstname ." " . $staff->user_lastname
					];
					$counter++;
				}
				
			}

			if ($clients) {
				foreach ($clients as $client) {
					$clients_array[] = [
						'id'	=>	$client->company_id,
						'text'	=>	$client->company_name
					];
				}
			}

			$data['technical_staff'] = json_encode($technical_staff_data, JSON_NUMERIC_CHECK);
			$data['clients'] = json_encode($clients_array, JSON_NUMERIC_CHECK);
			$data['content_view'] = 'FaultReporting/add_faults_v';
			$data['sub_menu'] = 0;
			$data['menu'] = 'fault_reporting';
			$data['page_header'] = 'Add Fault Reporting';
			$this->template->call_dashboard_template($data);
		}
	}

	function create_ticket_no($user_initials)
	{
		$latest_ticket_number = $this->M_Faultreporting->get_larget_ticket_no($user_initials);
		if(!$latest_ticket_number)
		{
			$new_ticket_number = 1;
		}
		else
		{
			$frags = explode('_', $latest_ticket_number);

			$new_ticket_number = (int)$frags[1] + 1;
		}

		$actual_ticket_number = str_pad($new_ticket_number, 8, '0', STR_PAD_LEFT);

		$ticket_no = $user_initials ."_". $actual_ticket_number;

		return $ticket_no;
	}

	function assignstaff($report_id)
	{
		$this->account->verify_session('fault_reporting');
		if($this->input->post())
		{
			echo "<pre>";print_r($this->input->post());die;
		}
		else
		{
			
			$data['report_id'] = $report_id;
			$data['content_view'] = 'FaultReporting/assign_staff_faults_v';
			$data['sub_menu'] = 0;
			$data['menu'] = 'fault_reporting';
			$data['page_header'] = 'Assign Staff';
			$this->template->call_dashboard_template($data);
		}
	}

	function detail($id)
	{
		$report_detail = $this->M_Faultreporting->getReport($id);
		if($report_detail)
		{
			$data['status'] = $report_detail->status;
			$data['report_id'] = $id;
			$data['staff_assigned'] = $this->M_Faultreporting->getStaffAssigned($id);
			$confirmation_information = $this->M_Faultreporting->getReportConfirmationInformation($id);
			$location = "";
			if ($confirmation_information->location_coordindates != "" || $confirmation_information->location_coordindates != NULL) {
				$location = $this->getLocation($confirmation_information->location_coordindates);
			}

			$data['client'] = $this->M_Faultreporting->getClientByFault($id);

			$time_cleared = $this->M_Faultreporting->getTimeCleared($id);

			if ($time_cleared) {
				$data['time_cleared'] = date('d-m-Y', strtotime($time_cleared->date_time_cleared));
			}
			else
			{
				$data['time_cleared'] = "Not yet cleared";
			}
			
			$data['photos'] = $this->create_fault_report_photo_list($id);
			$data['location'] = $location;
			$data['confirmation_information'] = $confirmation_information;
			$data['question_responses'] = $this->create_report_responses_section($id);
			$data['comments'] = $this->sanitize_comments($id);
			$data['clearance_information'] = $this->M_Faultreporting->get_report_clearance($id);
			$data['materials_used'] = $this->sanitize_materials_used($id);
			$data['report_detail'] = $report_detail;
			$data['event_log'] = $this->sanitize_event_log($id);
			$data['content_view'] = 'FaultReporting/detail_v';
			$data['sub_menu'] = 0;
			$data['menu'] = 'fault_reporting';
			$data['page_header'] = 'Report Detail';
			$this->template->call_dashboard_template($data);
		}
	}

	function create_fault_report_photo_list($report_id, $type = "html")
	{
		$photos = $this->M_Faultreporting->getReportPhotos($report_id);

		$photos_string = "";

		if ($photos) {
			if ($type == "html") {
				foreach ($photos as $photo) {
					if($photo->photo_type == 1)
					{
						$photos_string['before'] .= "<img src = '".base_url()."quavatelapi/media/{$photo->photo_name}'/>";
					}
					else
					{
						$photos_string['after'] .= "<img src = '".base_url()."quavatelapi/media/{$photo->photo_name}'/>";
					}
				}
			}
			else if ($type == "pdf") {
				foreach ($photos as $photo) {
					if($photo->photo_type == 1)
					{
						$photos_string['before'] .= "<img class = 'pdf_image' src = '".base_url()."quavatelapi/media/{$photo->photo_name}' alt = 'No image' style = 'width: 100px;' />";
					}
					else
					{
						$photos_string['after'] .= "<img class = 'pdf_image' src = '".base_url()."quavatelapi/media/{$photo->photo_name}' alt = 'No image' style = 'width: 100px;'/>";
					}
				}
			}
		}
		else
		{
			$photos_string = "<h2 style = 'text-align: center;'>There are no images to be displayed for this report</h2>";
		}
		
		return $photos_string;
	}

	function create_report_responses_section($id, $type = NULL)
	{
		$question_responses = $this->M_Faultreporting->get_question_responses($id);
		$response_arr = array();
		$nature_of_fault = $severity_of_fault = "";
		if ($question_responses) {
			$nature_counter = $severity_counter = 1;
			foreach ($question_responses as $response) {
				$explanation = "";
				if (strpos($response->response, "=>") !== false) {
					$explanation_frags = explode('=>', $response->response);

					$response->response = $explanation_frags[0];
					$explanation = $explanation_frags[1];
				} else if (strpos($response->response, "|") !== false)
				{
					$responses_frag = explode("|", $response->response);
					$response->response = "";
					$choices = $this->QuestionChoices($response->response_question);
					foreach ( $choices as $choice) {
						if (in_array($choice, $responses_frag)) {
							if ($type == "pdf") {
								$response->response .= "{$choice}<br/>";
							}
							else
							{
								$response->response .= "<input type = 'checkbox' checked = 'checked' disabled readonly/> {$choice}<br/>";
							}
							
						}
						else
						{
							if ($type == "pdf") {
								$response->response .= "";
							}
							else
							{
								$response->response .= "<input type = 'checkbox' disabled readonly/> {$choice}<br/>";
							}
						}


					}
				}

				if ($response->question_category == "nature_of_fault") {
					if ($response->response == "N/A" || $response->response == "No" || $response->response == "no" || $response->response == NULL || $response->response == "" || $response->response == "NULL") {
						$response->response = "Not Answered";
					}
					$nature_of_fault .= '<tr>';
					$nature_of_fault .= "<td>{$nature_counter}</td>";
					$nature_of_fault .= "<td colspan = '2'>{$response->question}</td>";

					$ex_colspan = ($explanation == "") ? 3 : 1;
					if (strpos($response->response, "<input") !== false || strpos($response->response, "<br/>") !== false) {
						$nature_of_fault .= "<td colspan = '{$ex_colspan}'>{$response->response}</td>";
					}
					else
					{
						$choice = $this->QuestionChoices($response->response_question)[0];

						if ($choice == $response->response) {
							if ($type == "pdf") {
								$nature_of_fault .= "<td colspan = '{$ex_colspan}'>{$choice}</td>";
							}
							else
							{
								$nature_of_fault .= "<td colspan = '{$ex_colspan}'><input type = 'checkbox' checked = 'checked' disabled readonly/> {$choice}</td>";
							}
							
						}
						else
						{
							if($type != "pdf"){
								$nature_of_fault .= "<td colspan = '{$ex_colspan}'><input type = 'checkbox' disabled readonly/> {$choice}</td>";
							}
							else{
								$nature_of_fault .= "<td colspan = '{$ex_colspan}'></td>";
							}
						}
					}
					if ($explanation != "") {
						$nature_of_fault .= "<td colspan = '2'>{$explanation}</td>";
					}

					else if ($explanation == "" && $type == "pdf") {
						$nature_of_fault .= "<td></td>";
					}
					
					$nature_of_fault .= '</tr>';

					$nature_counter++;
				}
				else if($response->question_category == "severity_of_fault"){
					$ex_colspan = ($explanation == "") ? 3 : 1;
					$severity_of_fault .= '<tr>';
					$severity_of_fault .= "<td>{$severity_counter}</td>";
					$severity_of_fault .= "<td colspan = '2'>{$response->question}</td>";
					$severity_of_fault .= "<td colspan = '{$ex_colspan}'>{$response->response}</td>";
					if ($explanation != "") {
						$severity_of_fault .= "<td colspan = '2'>{$explanation}</td>";
					}
					if ($type == "pdf") {
						$severity_of_fault .= "<td></td>";
					}
					$severity_of_fault .= '</tr>';

					$severity_counter++;
				}
				else
				{
					echo "Nothing";die;
				}
			}

			$response_arr['nature_of_fault'] = $nature_of_fault;
			$response_arr['severity_of_fault'] = $severity_of_fault;
		}
		return $response_arr;
	}

	function sanitize_comments($id)
	{
		$comments = $this->M_Faultreporting->get_report_comments($id);

		$sanitized_comments = array();

		if ($comments) {
			foreach ($comments as $comment) {
				if ($comment->type == "detailed") {
					$sanitized_comments['detailed'] = $comment->comment;
				}
				else
				{
					$sanitized_comments['remedial'] = $comment->comment;
				}
			}
		}
		else
		{
			$sanitized_comments['detailed'] = "Not Answered";
			$sanitized_comments['remedial'] = "Not Answered";
		}

		return $sanitized_comments;
	}

	function sanitize_materials_used($id, $type = "html")
	{
		$all_materials = $this->db->get('tbl_fault_reporting_materials')->result();

		$materials_used = $this->clean_materials_used($id);

		// echo "<pre>";print_r($materials_used);die();
		$materials_used_table = '';
		$counter = 1;
		foreach ($all_materials as $key => $value) {
			$quantity = "";
			if(count($materials_used)){
				foreach ($materials_used as $material_id => $material_quantity) {
					if (stripos($material_id, '[') !== false) {
						$id_frags = explode('[', $material_id);

						if ($value->id == $id_frags[0]) {
							$material_key = str_replace(']', "", $id_frags[1]);
							$quantity[$material_key] = $material_quantity['quantity'];
						}

					}
					else
					{
						if ($value->id == $material_id) {
							$quantity = $material_quantity['quantity'];
						}
					}
				}
			}
			if ($value->options == NULL) {

				$colspan = ($type == "pdf") ? 2 : 4;
				$materials_used_table .= "<tr>";
				$materials_used_table .= "<td>{$counter}</td>";
				$materials_used_table .= "<td colspan = '{$colspan}'>{$value->material}</td>";
				$materials_used_table .= "<td>{$quantity}</td>";
				$materials_used_table .= "</tr>";
			}
			else
			{
				$options_frags = explode('|', $value->options);
				if (count($options_frags) > 1) {
					$colspan = ($type == "pdf") ? "" : 3;
					$number = ($type != "pdf") ? count($options_frags) : "";
					$materials_used_table .= "<tr>";
					if($type !=="pdf"){
						$materials_used_table .= "<td rowspan = '{$number}'>{$counter}.</td>";
						$materials_used_table .= "<td rowspan = '{$number}' colspan = '{$colspan}' style = 'width: 25%;'>{$value->material}</td>";
					}
					else
					{
						$materials_used_table .= "<td>{$counter}.</td>";
						$materials_used_table .= "<td>{$value->material}</td>";
					}
					$inner_counter = 1;
					foreach ($options_frags as $option) {
						if ($inner_counter > 1) {
							$materials_used_table .= "<tr>";
							if ($type == "pdf") {
								$materials_used .= "<td></td><td></td>";
							}
						}
						$materials_used_table .= "<td>{$option}</td>";
						$materials_used_table .= "<td>{$quantity[$option]}</td>";
						$materials_used_table .= "</tr>";
						$inner_counter++;
					}
				}
			}

			// echo "{$materials_used_table}";die;	

			$counter++;
		}

		return $materials_used_table;
	}

	function clean_materials_used($id)
	{
		$materials_used = $this->M_Faultreporting->get_materials_used($id);
		$clean_materials_used = array();

		if (count($materials_used)) {
			foreach ($materials_used as $material) {
				$clean_materials_used[$material->material] = ['quantity' => $material->quantity];
			}
		}

		return $clean_materials_used;
	}

	function sanitize_event_log($id)
	{
		$event_array_map = [
			'time_fault_reported'			=>	"Time the Fault was Reported",
			'ticket_confirmation_time'		=>	"Time the technician confirms the ticket",
			'location_confirmation_time'	=>	"Time the technician confirms his location",
			'time_fault_identified'			=>	"Time the Fault was identified",
			'time_restoration'				=>	"Time the restoration was started",
			'time_submitted'				=>	"Time the information was submitted to NOC for confirmation",
			'time_cleared'					=>	"Time the NOC confirmed and cleared the fault",
			'commented_on'					=>	"Time the Fault Technician commented on the fault Reporting"
		];

		$event_log = $this->M_Faultreporting->get_event_log($id);

		$event_log_table = "";

		if ($event_log) {
			$counter = 1;
			foreach ($event_array_map as $key => $value) {
				$formatted_time = "Not Available";
				if ($event_log->$key) {
					$formatted_time = date('dS M Y', strtotime($event_log->$key)) . " at " . date('H:i:s', strtotime($event_log->$key));
				}
				$event_log_table .= "<tr>";
				$event_log_table .= "<td>{$counter}.</td>";
				$event_log_table .= "<td colspan = '2'>{$formatted_time}</td>";
				$event_log_table .= "<td colspan = '3'>{$value}</td>";
				$event_log_table .= "</tr>";

				$counter++;
			}
		}

		return $event_log_table;
	}

	function ConfirmFault($id)
	{
		$confirmed = $this->M_Faultreporting->confirmfault($id);
		if ($confirmed == true) {

			$clearance = $this->M_Faultreporting->addClearanceInformation($id);
			redirect(base_url() . "FaultReporting/detail/{$id}");
		}
		else
		{
			die("An unexpected error occured");
		}
	}

	function send_mail($email, $name, $subject, $message)
	{
		$url = 'http://www.symatechlabs.com/sendmail/sendmail.php';
		//open connection
		$ch = curl_init();

		$_POST = [
			'mail_to' => ['email' => $email, 'name' => $name],
			'subject' => $subject,
			'message' => $message
		];

		$fields_string = http_build_query($_POST);
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($_POST));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//execute post
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);
	}

	function export($id, $type = "pdf")
	{
		$report_detail = $this->M_Faultreporting->getReport($id);
		if($report_detail)
		{
			$data['status'] = $report_detail->status;
			$data['report_id'] = $id;
			$data['staff_assigned'] = $this->M_Faultreporting->getStaffAssigned($id);
			// $data['question_responses'] = $this->create_report_responses_section($id, "pdf");
			$data['question_responses'] = $this->create_pdf_report_responses($id);
			$data['comments'] = $this->sanitize_comments($id);
			$data['clearance_information'] = $this->M_Faultreporting->get_report_clearance($id);
			$data['materials_used_pdf'] = $this->sanitize_materials_used($id, 'pdf');
			$data['report_detail'] = $report_detail;
			$data['event_log'] = $this->sanitize_event_log($id);
			$data['client'] = $this->M_Faultreporting->getClientByFault($id);
			$confirmation_information = $this->M_Faultreporting->getReportConfirmationInformation($id);
			$location = "";
			if ($confirmation_information->location_coordindates != "" || $confirmation_information->location_coordindates != NULL) {
				$location = $this->getLocation($confirmation_information->location_coordindates);
			}
			$data['location'] = $location;
			$data['confirmation_information'] = $confirmation_information;
			$time_cleared = $this->M_Faultreporting->getTimeCleared($id);

			if ($time_cleared) {
				$data['time_cleared'] = date('d-m-Y', strtotime($time_cleared->date_time_cleared));
			}
			else
			{
				$data['time_cleared'] = "Not yet cleared";
			}
			
			$data['photos'] = $this->create_fault_report_photo_list($id, "pdf");
			// echo "<pre>";print_r($data);die;
			
			$user = $this->M_Account->get_user_by_id('user_id', $this->session->userdata['user_id']);
			if ($type == "pdf") {
				$data['title'] = 'FaultReport-' . date("d_m_y_h_i_s");
				$data["orientation"] = "portrait";
				$data['perpared_by'] = $user->user_firstname . " " . $user->user_lastname;
				$data["type"] = $type;
				$data['view'] = 'FaultReporting/pdf_detail_v';
				$this->export->export_report($data, $type);
			}
		}
	}

	function create_pdf_report_responses($id)
	{
		$responses = $this->M_Faultreporting->getQuestionResponsesByFault($id);

		if ($responses) {
			$sanitized_responses = array();
			foreach ($responses as $response) {
				$explanation = "";
				if (strpos($response->response, '|') !== false) {
					$response_frags = explode('|', $response->response);

					foreach ($response_frags as $frag) {
						if ($frag != "NULL" && $frag != NULL && $frag != "") {
							$sanitized_responses[$response->question_category][$response->question][] = $frag;
						}
					}
				}
				else if(strpos($response->response, ":") !== false){
					$response_frags = explode(':', $response->response);

					if ($response_frags[0] != "NULL" && $response_frags[0] != NULL && $response_frags[0] != "") {
						$sanitized_responses[$response->question_category][$response->question][] = $response_frags[0] . "<br/><b>Explanation:</b><br/>" . $response_frags[1];
					}
					
				}
				else
				{
					$sanitized_responses[$response->question_category][$response->question][0] = $response->response;
				}
				
			}
			
			$table = array();
			
			foreach ($sanitized_responses as $key => $value) {
				$counter = 1;
				foreach ($value as $k => $v) {
					$table[$key] .= '<tr>';
					$table[$key] .= "<td>{$counter}</td>";
					$table[$key] .= "<td>{$k}</td>";
					$table[$key] .= '<td>'.implode('<br/>', $v).'</td>';
					$table[$key] .= '</tr>';
					$counter++;
				}
				
			}

			return $table;
		}
	}

	function QuestionChoices($question_id = NULL)
	{
		$choices = array(
			1 	=> ['In ODF', 'In Closure', 'Along the Line', 'Fire', 'Vandalism', 'Construction'],
			2 	=> ['At Hub', 'At Client Site'],
			3 	=> ['Faulty SFP', 'Faulty Card', 'Misconfiguration'],
			4 	=> ['In Closure'],
			5 	=> ['Accidental Disconnection', 'Wrongly spliced Fibers'],
			6 	=> ['High Loss'],
			7 	=> ['Interchanged Patch Cords', 'Disconnected Patch Cords'],
			8 	=> ['Other'],
			9 	=> ['Yes'],
			10	=> ['Yes'],
			11	=> ['In all Cables', 'In One Cable', 'In affected fiber(s)/tube(s)'],
			12	=> ['In all Cables', 'In One Cable', 'In affected fiber(s)/tube(s)'],
			13	=> ['In all Cables', 'In One Cable', 'In affected fiber(s)/tube(s)'],
			14	=> ['Yes']
		);

		if ($question_id !== NULL) {
			$data = ($choices[$question_id]) ? $choices[$question_id] : FALSE;
			if ($data == FALSE) {
				echo "The question {$question_id} you asked for could not be found";die;
			}else
			{
				return $data;
			}
		}
		else
		{
			return $choices;
		}
	}

	function confirmationLInk($ticket_no)
	{
		header("Location: my.special.scheme://other/{$ticket_no}");
		exit();
	}

	function getLocation($coordinates)
	{
		$coordinates_frags = explode("*", $coordinates);

		$latitude = $coordinates_frags[0];
		$longitude = $coordinates_frags[1];

		$geolocation = $latitude . ',' . $longitude;
		// echo $geolocation;die;

		$request = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=false'; 
		$file_contents = file_get_contents($request);
		$json_data = json_decode($file_contents);

		$location = "";
		if (isset($json_data->results[0])) {
			foreach ($json_data->results[0] as $key => $value) {
				if ($key == "formatted_address") {
					$location =  $value;
					break;
				}
			}
		}

		return $location . "<br/><b>[$geolocation]</b>";
	}
}