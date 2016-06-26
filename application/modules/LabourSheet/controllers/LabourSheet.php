<?php

class LabourSheet extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("M_Laboursheet");
	}
	
	function index()
	{
		$this->account->verify_session('laboursheet');
		redirect(base_url() . "LabourSheet/daily");
	}
	function daily($date = NULL, $type = "raw")
	{
		$this->account->verify_session('laboursheet');
		$date = ($date == NULL) ? date('Y-m-d') : $date;
		$raw_day_data = $this->M_Laboursheet->get_day_data($date);
		// echo "<pre>";print_r($raw_day_data);die;
		$day_data = $this->clean_data($raw_day_data, "html");
		//echo "<pre>";print_r($day_data);die;
		$data['date'] = $date;
		$data['daily_table'] = $this->create_daily_data($day_data);
		$data['daily_total'] = $this->M_Laboursheet->get_day_total($date);
		$data['page_header'] = "{$date}";
		$data['menu'] = 'laboursheet';
		$data['sub_menu'] = 1;
		$data['content_view'] = 'LabourSheet/daily_v';
		$this->template->call_dashboard_template($data);
	}
	
	function export($report_type, $type, $from = null, $to = null, $id = null)
	{
		// echo "<pre>";print_r($this->session->userdata);die;
		
		$data = array();
		$this->load->model("M_Account");
		$user = $this->M_Account->get_user_by_id('user_id', $this->session->userdata['user_id']);
		switch($report_type)
		{
			case "daily":
				$this->account->verify_session('laboursheet');
				$date = ($from == NULL) ? date('Y-m-d') : $from;
				$raw_day_data = $this->M_Laboursheet->get_day_data($date);
				$day_data = $this->clean_data($raw_day_data, "pdfandexcel");
				//echo "<pre>";print_r($day_data);die;
				if($type == "pdf")
				{
					$data['title'] = 'Laboursheet-' . date("d_m_y_h_i_s");
					$data["orientation"] = "landscape";
					$data['perpared_by'] = $user->user_firstname . " " . $user->user_lastname;
					$data["type"] = $type;
					$data['view'] = "LabourSheet/daily_table_v";
					
					$data['daily_total'] = $this->M_Laboursheet->get_day_total($date);
					$data['date'] = $date;
					$data['daily_table'] = $this->create_daily_data($day_data, "pdf");
					//echo $data['daily_table'];die;
					$this->export->export_report($data, $type);
				}
				
				else if($type == "excel")
				{
					$title = "LABOUR SHEET FOR: " . date("d-m-Y", strtotime($date));
					$excel_data[0] = array('NO.', 'NAME', 'IDNO', 'MOBILENO', 'PURPOSE', 'PROJECT', 'SUPERVISOR', 'AMOUNT');
					if(count($day_data ) > 0){
						$counter = 1;
						foreach($day_data as $key => $value)
						{
							$excel_data[] = array(
								$counter,
								$day_data[$key]->labourer_firstname . " " . $day_data[$key]->labourer_lastname,
								$value->labourer_idno,
								$value->labourer_mobileno,
								$value->task,
								$value->project_name,
								$value->supervisor_name,
								$value->wage_amount
							);
							$counter++;
						}
					}
					$this->export->export_report($excel_data, $type, $title);
				}
				break;
			case "weekly":
				$this->account->verify_session('laboursheet');
				//echo "<pre>";print_r($this->clean_range_data($from, $to));die;
				$pre_excel_data = $this->clean_range_data($from, $to);
				$data = $this->generate_excel_data($pre_excel_data);
				//echo "<pre>";print_r($data);die;
				$this->export->export_report($data, $type, "LIQUID TELECOM WAGE BILL FROM " . date('jS F Y', strtotime($from)) . " TO " . date('jS F Y', strtotime($to)));
				break;
			
			case "wagestructure":
				$this->account->verify_session('wagestructure');
				if($type == "pdf")
				{
					$data['title'] = "Wage Structure As At " . date("d/m/y");
					$data['name'] = "WageStructure-" . date("d_m_y_h_i_s");
					$data['type'] = 'pdf';
					$data['view'] = "LabourSheet/wage_structure_table_v";
					$data['wage_structure_table'] = $this->create_wage_structure_table($this->M_Laboursheet->get_wage_structure(), 'pdf');
					$this->export->export_report($data, $type);
				}
				else if($type == "excel")
				{
					$data['title'] = "Wage Structure As At " . date("d m y");
					$pre_excel_data = $this->M_Laboursheet->get_wage_structure();
					$number = count($pre_excel_data);
					
					$cleaned_data[0] = array("NO.", "TASK", "RATE");
					
					if($number > 0)
					{
						$counter = 1;
						foreach($pre_excel_data as $excel_data)
						{
							$cleaned_data[] = array(
								'0' => $counter,
								'1' => $excel_data->wagestructure_task,
								'2' => $excel_data->wagestructure_rate
							);
							$counter++;
						}
					}
					
					$this->export->export_report($cleaned_data, $type, "Wage Structure As At " . date("d m y"));
					
				}
				break;
			case "labourdetails":
				$this->account->verify_session('laboursheet');
				$labourer_details = $this->M_Laboursheet->get_labourer_details($id, $from, $to);
				$cleaned_data = $this->clean_labourer_wage_data($id, $labourer_details, $from, $to);
				// echo "<pre>";print_r($cleaned_data);die;
				if($type == "pdf")
				{
					$pdf_data = array();
					$pdf_data = array(
						'name' => $cleaned_data[$id]['last_name'] . ", " . $cleaned_data[$id]['first_name'] . " " . $cleaned_data[$id]['other_name'],
						'idno' => $cleaned_data[$id]['idno'],
						'mobileno' => $cleaned_data[$id]['mobileno'],
						'from' => $from,
						'to' => $to,
						'week_details' => $cleaned_data[$id]['daily_totals'],
						'total_wage' =>  $cleaned_data[$id]['wage_total']
					);
					// echo "<pre>";print_r($pdf_data);die;
					$data["pdf_data"] = $pdf_data;
					$data["css"] = "custom";
					$title = "Wage Details For {$pdf_data['name']}";
					$data['title'] = $title;
					$data["view"] = "LabourSheet/details_pdf_v";
					$this->export->export_report($data, $type, $title);
				}
				else if($type == "excel")
				{
					// echo "here";die;
					$excel_data = array();
					$excel_data[0][] = "NAME";
					$excel_data[0][] = $cleaned_data[$id]['last_name'] . ", " . $cleaned_data[$id]['first_name'] . " " . $cleaned_data[$id]['other_name'];

					$excel_data[1][] = "ID NUMBER";
					$excel_data[1][] = $cleaned_data[$id]['idno'];

					$excel_data[2][] = "MOBILE NO";
					$excel_data[2][] = $cleaned_data[$id]['mobileno'];

					$excel_data[3][] = "DAYS WORKED";
					$excel_data[4][] = "RANGE: " . date("d/m/Y", strtotime($from)) . " TO: " . date("d/m/Y", strtotime($to));

					$excel_data[5] = array("NO.", "DATE", "SUPERVISORS","PURPOSE", "PROJECT", "AMOUNT");
					$counter = 1;
					foreach ($cleaned_data[$id]["daily_totals"] as $key => $value) {
						$excel_data[] = array($counter, date('d/m/Y', strtotime($key)), $value["supervisors"],$value['purpose'], $value['project'], $value['total_wage']);
						$counter++;
					}
					$title = "Wage Details For {$excel_data[0][1]}";
					$this->export->export_special_excel($excel_data, $title);
					
				}
				else
				{
					echo "here";die;
				}
				break;
		}
	}
	
	function generate_excel_data($pre_excel_data)
	{
		$this->account->verify_session('laboursheet');
		$data[0] = array('NO.', 'NAME', 'IDNO', 'MOBILENO', 'PURPOSE', 'PROJECT', 'SUPERVISOR');
		foreach($pre_excel_data as $key => $value)
		{
			if($key == "days")
			{
				//count($value);die;
				$range_string = "";
				foreach($value as $day){
					$data[0][] = strtoupper($day);
				}
			}
			else if($key == "labourer_data")
			{
				$counter = 1;
				foreach($value as $labourer)
				{
					$data[$counter][] = $counter;
					$data[$counter][] = $labourer["name"];
					$data[$counter][] = $labourer["idno"];
					$data[$counter][] = $labourer["mobileno"];
					$data[$counter][] = $labourer["tasks"];
					$data[$counter][] = $labourer["project"];
					$data[$counter][] = $labourer["supervisor"];
					foreach($labourer['wage_data'] as $wages)
					{
						//$data[$counter][] = ($wages != "-") ? number_format((double)$wages) : $wages;
						$data[$counter][] = $wages;
					}

					$data[$counter][] = $labourer["deductions"];
					$data[$counter][] = $labourer["total_wages"];
					$data[$counter][] = $labourer["total_wages"];
					$counter++;
				}
			}
		}
		
		$data[0][] = 'DEDUCTIONS';
		$data[0][] = 'SUB TOTAL';
		$data[0][] = 'TOTAL';
		return $data;
	}
	
	function clean_data($raw_day_data, $type = "html")
	{
		$this->account->verify_session('laboursheet');
		$cleaned_data = array();
		if($type == "html")
		{
			$cleaned_data = $raw_day_data;
		}
		else
		{
			// echo "<pre>";print_r($raw_day_data);die;
			foreach($raw_day_data as $value)
			{
				$cleaned_data[$value->labourer_id]["tasks"][] = array('task' => $value->wagestructure_task, 'length' => $value->wage_structure_length, 'unit' => $value->wagestructure_unit);
				$cleaned_data[$value->labourer_id]["purpose"][] = $value->wage_purpose;
				$cleaned_data[$value->labourer_id]["wage_amount"][] = $value->wage_amount;
				$cleaned_data[$value->labourer_id]["projects"][] = $value->project_name;
				$cleaned_data[$value->labourer_id]["supervisors"][] = $value->user_lastname . " " . $value->user_firstname;
			}
			// echo "<pre>";print_r($cleaned_data);die;
			$temporary_array = array();
			foreach($cleaned_data as $key => $value)
			{
				//echo "<pre>";print_r($value);die;
				$projects = $supervisors = "";
				$projects = implode('/', $value['projects']);
				$supervisors = implode('/', $value['supervisors']);
				$wage_purpose = "";
				$keys = count($value["purpose"]);
				if($keys > 1)
				{
					if($keys == 2)
					{
						$wage_purpose = implode(" and ", $value["purpose"]);
					}
					else
					{
						$arrays = $value["purpose"];
						$last_purpose = end($value["purpose"]);
						array_pop($value["purpose"]);
						$wage_purpose = implode(", ", $value["purpose"]) . " and " . $last_purpose;
					}
				}
				else
				{
					$wage_purpose = $value["purpose"][0];
				}
				
				$task = "";
				$cleaned_tasks = array();
				foreach ($value["tasks"] as $k => $v) {
					if($v["length"] != "" && $v["length"] != NULL && $v["length"] != "NULL" && $v["length"] != 0)
					{
						if(strpos($v['task'], "in m") != false){
							$v['task'] = str_replace("in m",$v['length']." " .$v['unit'],$v['task']);
						}
						else
						{
							$v['task'] = $v['task'] . " " . $v['length'] . " " . $v['unit'];
						}
					}

					$cleaned_tasks[] = $v['task'];
				}

				$numberoftasks = count($cleaned_tasks);
				if($numberoftasks > 1)
				{
					if($numberoftasks == 2)
					{
						$task = implode(" and ", $cleaned_tasks);
					}
					else
					{
						$arrays = $cleaned_tasks;
						$last_task = end($cleaned_tasks);
						array_pop($cleaned_tasks);
						$task = implode(", ", $cleaned_tasks) . " and " . $last_task;
					}
				}
				else
				{
					$task = $cleaned_tasks[0];
				}
				$total_wage = array_sum($value["wage_amount"]);
				$temporary_array[$key]["tasks"] = $task;
				$temporary_array[$key]["supervisor_name"] = $supervisors;
				$temporary_array[$key]["wage_purpose"] = $wage_purpose;
				$temporary_array[$key]["wage_amount"] = $total_wage;
			}
			
			$cleaned_data =array();
			
			foreach($raw_day_data as $key => $value)
			{
				$cleaned_data[$value->labourer_id] = (object)array(
					'labourer_id' => $value->labourer_id,
					'labourer_firstname' => $value->labourer_firstname,
					'labourer_lastname' => $value->labourer_lastname,
					'labourer_othername' => $value->labourer_othername,
					'labourer_idno' => $value->labourer_idno,
					'labourer_mobileno' => $value->labourer_mobileno,
					'project_name' => $projects,
					'wage_purpose' => $temporary_array[$value->labourer_id]["wage_purpose"],
					'wage_amount' => $temporary_array[$value->labourer_id]["wage_amount"],
					'supervisor_name' => $temporary_array[$value->labourer_id]["supervisor_name"],
					'task' => $temporary_array[$value->labourer_id]["tasks"]
				);
			}
			
		}
		// echo "<pre>";print_r($cleaned_data);die;
		asort($cleaned_data);
		return $cleaned_data;
	}
	
	function create_daily_data($day_data, $type = NULL)
	{
		$permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));
		$this->account->verify_session('laboursheet');
		$day_table = "";
		if(count($day_data) > 0)
		{
			$counter = 1;
			foreach($day_data as $pay)
			{
				// echo "<pre>";print_r($pay);die;
				$supervisor_name = ($type == NULL) ? $pay->user_firstname . " " . $pay->user_lastname : $pay->supervisor_name;
				$day_table .= "<tr>";
				$day_table .= "<td>{$counter}</td>";
				$day_table .= "<td>". $pay->labourer_lastname . ", " . $pay->labourer_firstname . " ". $pay->labourer_othername."</td>";
				$day_table .= "<td>{$pay->labourer_idno}</td>";
				$day_table .= "<td>{$pay->project_name}</td>";
				$quantity = ($pay->wage_structure_length != "") ? "<b>[{$pay->wage_structure_length} {$pay->wagestructure_unit}]</b>" : "";
				$day_table .= ($type == NULL) ? "<td>{$pay->wagestructure_task}<br/>{$quantity}</td>" : "<td>{$pay->task}</td>";
				$day_table .= "<td>{$pay->labourer_mobileno}</td>";
				$day_table .= "<td>{$supervisor_name}</td>";
				$day_table .= "<td>Ksh. ". number_format($pay->wage_amount) ."</td>";
				if ($type != "pdf" && ($permission == "admin" || $permission == "finance")) {
					$day_table .= "<td><center><a class = 'call_modal' href = '#' data-href = '".base_url()."LabourSheet/edit/{$pay->wage_id}'><i class = 'zmdi zmdi-edit'></i></a> | <a href = '#' data-href = '".base_url()."LabourSheet/delete/{$pay->wage_id}/{$pay->wage_date}' class = 'delete_labour_wage'><i class = 'zmdi zmdi-delete'></i></a></center></td>";
				}
				$day_table .= "</tr>";
				$counter++;
			}
		}
		
		return $day_table;
	}

	function edit($wage_id)
	{
		if(!$_POST){
			$wage_data = $this->M_Laboursheet->get_wage_by_id($wage_id);
			
			$data['details']['title'] = "Editting Laboursheet Details for: {$wage_data->labourer_lastname}, {$wage_data->labourer_firstname} on {$wage_data->wage_date}";

			$wage_data = (array)$wage_data;
			$data['view'] = $this->load->view('edit_laboursheet_v', $wage_data, TRUE);

			echo json_encode($data);
		}else{
			unset($_POST['form_action']);
			if (isset($_POST['wagestructure_rate'])) {
				unset($_POST['wagestructure_rate']);
			}
			$this->M_Laboursheet->edit_laboursheet($wage_id);

			redirect(base_url() . "LabourSheet");
		}
	}

	function weekly($from = null, $to = null)
	{
		$this->account->verify_session('laboursheet');
		if($from == null && $to == null)
		{
			$from = date('Y-m-d', time() + (60 * 60 * 24 * - 7));
			$to = date('Y-m-d');
		}
		$data['labourers_table'] = $this->create_labour_table($from, $to);
		$data['total_payment'] = $this->total_payment($from, $to);
		$data['from'] = $from;
		$data['to'] = $to;
		$data['page_header'] = "Labour Sheet";
		$data['menu'] = 'laboursheet';
		$data['sub_menu'] = 1;
		$data['content_view'] = 'LabourSheet/weekly_v';
		$this->template->call_dashboard_template($data);
	}

	function create_labour_table($from, $to)
	{
		$this->account->verify_session('laboursheet');
		$table = "";

		$data = $this->weekdata($from, $to);
		$counter = 0;
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$counter++;
				$table .= '<tr>';
				$table .= "<td>{$counter}</td>";
				$table .= "<td>{$value->labourer_lastname}, {$value->labourer_firstname} {$value->labourer_othername}</td>";
				$table .= "<td>{$value->labourer_idno}</td>";
				$table .= "<td>{$value->labourer_mobileno}</td>";
				$table .= "<td>Ksh. " . number_format($value->amount) . "</td>";
				$table .= "<td>{$value->user_firstname} {$value->user_lastname}</td>";
				$table .= "<td><a href = '".base_url()."LabourSheet/details/labourer/{$value->labourer_id}/{$from}/{$to}'>Details <small> & editting</small></a></td>";
				$table .= '</tr>';
			}
		}

		return $table;
	}

	function weekdata($from, $to)
	{
		$this->account->verify_session('laboursheet');
		$weekdata = $this->M_Laboursheet->get_week_data($from, $to);
		// echo "<pre>";print_r($weekdata);die;
		return $weekdata;
	}

	function total_payment($from, $to)
	{
		$this->account->verify_session('laboursheet');
		$data = $this->M_Laboursheet->get_total_payment($from, $to);
		return number_format($data->total_amount);
	}
	
	function create_projects_combo()
	{
		$this->account->verify_session('laboursheet');
		$project_combo = "";
		$projects = $this->M_Laboursheet->get_projects();
		if(count($projects) > 0)
		{
			foreach($projects as $project)
			{
				$project_combo .= "<option value = '{$project->project_id}'>{$project->project_name}</option>";
			}
		}
		
		return $project_combo;
	}
	
	function filter()
	{
		$this->account->verify_session('laboursheet');
		$data = array();
		if(!$this->input->post())
		{
			$start_date = gmdate('Y-m-d', time() + (60 * 60 * 24 * - 7));
			$end_date = gmdate('Y-m-d');
		}
		else
		{
			$start_date = gmdate('Y-m-d',strtotime($_POST['date_from']));
			$end_date = gmdate('Y-m-d',strtotime($_POST['date_to']));
		}
		
		
		$data['daily_total'] = $cleaned_daily_total;
		//echo "<pre>";print_r($data['daily_total']);die;
		$data['labourer_data'] = $cleaned_labourer_data;
		$data['actual_columns'] = $actual_columns;
		$data['projects_dropdown'] = $this->create_projects_combo();
		$data['page_header'] = "Advanced Filter";
		$data['menu'] = 'laboursheet';
		$data['sub_menu'] = 0;
		$data['content_view'] = 'LabourSheet/advanced_v';
		$this->template->call_dashboard_template($data);
		
	}
	
	function clean_range_data($from, $to)
	{
		$this->account->verify_session('laboursheet');
		$actual_start_date = $from;
		$actual_end_date = $to;
		$_days = array();
		$_dates = array();
		if($from <= $to)
		{
			while($from <= $to)
			{
				//echo date("D", strtotime($start_date)) . '=>' .$start_date. '<br/>' ;
				$day_week = date("D", strtotime($from));
				$data['day_columns'][] = array('day' => $day_week,
							'date' => $from);
				$_days[] = $day_week;
				$_dates[] = $from;
				$from = date("Y-m-d", strtotime($from . " +1 day"));
			}
			//echo "<pre>";print_r($data['day_columns']);die;
		}
		else
		{
			die("There was a problem with the dates. Go back and fix it");
		}
		$actual_columns = '';
		foreach($data['day_columns'] as $key => $value)
		{
			$actual_columns .= "<th>{$value['day']}</th>";
		}
		
		//echo $actual_start_date;die;
		$labourer_data = $this->M_Laboursheet->get_range_data($actual_start_date, $actual_end_date);
		//echo "<pre>";print_r($labourer_data);die;
		
		//precleaning array
		$precleaned = array();
		foreach($labourer_data as $value)
		{
			$precleaned[$value->labourer_id][] = $value;
		}
		//echo "<pre>";print_r($precleaned);die;
		//end of precleaning
		//cleaning labourer_data
		$cleaned_labourer_data = array();
		//$wage_data = array();
		foreach($precleaned as $value)
		{
			// echo "<pre>";print_r($value);die;
			foreach($value as $v){
				$cleaned_labourer_data[$v->labourer_id]["name"] = $v->labourer_lastname .", ". $v->labourer_firstname . " " . $v->labourer_othername;
				$cleaned_labourer_data[$v->labourer_id]["idno"] = $v->labourer_idno;
				$cleaned_labourer_data[$v->labourer_id]["mobileno"] = $v->labourer_mobileno;
				$cleaned_labourer_data[$v->labourer_id]["purpose"][] = $v->wage_purpose;
				$cleaned_labourer_data[$v->labourer_id]["tasks"][] = array('task' => $v->wagestructure_task, 'length' => $v->wage_structure_length, 'unit' => $v->wagestructure_unit);
				$cleaned_labourer_data[$v->labourer_id]["projects"][] = $v->project_name;
				$cleaned_labourer_data[$v->labourer_id]["wages"][$v->wage_date][] = array('amount'=> $v->wage_amount, 'status' => $v->wage_status);
				$cleaned_labourer_data[$v->labourer_id]["total_wages"] = 0;
				$cleaned_labourer_data[$v->labourer_id]["wage_dates"][] = $v->wage_date;
				$cleaned_labourer_data[$v->labourer_id]["supervisors"][] = $v->user_lastname . " " . $v->user_firstname;
				$cleaned_labourer_data[$v->labourer_id]["deductions"][] = $v->wage_deduction;
			}
			//$wage_data[$value->labourer_id][$value->wage_date] = array('amount'=> $value->wage_amount, 'status' => $value->wage_status);
		}
		
		//clean purposes
		foreach($cleaned_labourer_data as $key => $value)
		{
			$wage_purpose = "";
			$keys = count($value["purpose"]);
			if($keys > 1)
			{
				if($keys == 2)
				{
					$wage_purpose = implode(" and ", $value["purpose"]);
				}
				else
				{
					$arrays = $value["purpose"];
					$last_purpose = end($value["purpose"]);
					array_pop($value["purpose"]);
					$wage_purpose = implode(", ", $value["purpose"]) . " and " . $last_purpose;
				}
			}
			else
			{
				$wage_purpose = $value["purpose"][0];
			}

			$deductions = array_filter($value["deductions"]);

			$cleaned_labourer_data[$key]["deductions"] = implode(', ', $deductions);
			$cleaned_tasks = array();
			foreach ($value["tasks"] as $k => $v) {
				if($v["unit"] != ""){
					if($v["length"] != "")
					{
						if(strpos($v['task'], "in m") != false){
							$v['task'] = str_replace("in m",$v['length']." " .$v['unit'],$v['task']);
						}
						else
						{
							$v['task'] = $v['task'] . " " . $v['length'] . " " . $v['unit'];
						}
					}
				}

				$cleaned_tasks[] = $v['task'];
			}

			$task = "";
			$numberoftasks = count($cleaned_tasks);
			if($numberoftasks > 1)
			{
				if($numberoftasks == 2)
				{
					$task = implode(" and ", $cleaned_tasks);
				}
				else
				{
					$arrays = $cleaned_tasks;
					$last_task = end($cleaned_tasks);
					array_pop($cleaned_tasks);
					$task = implode(", ", $cleaned_tasks) . " and " . $last_task;
				}
			}
			else
			{
				$task = $cleaned_tasks[0];
			}
			$cleaned_labourer_data[$key]["project"] = implode('/', $value['projects']);
			$cleaned_labourer_data[$key]["supervisor"] = implode('/', $value['supervisors']);
			$cleaned_labourer_data[$key]["tasks"] = $task;
			$cleaned_labourer_data[$key]["wage_purpose"] = $wage_purpose;
		}
		//echo "<pre>";print_r($cleaned_labourer_data);die;
	foreach($cleaned_labourer_data as $key => $value)
		{
			foreach($value["wages"] as $k => $v)
			{
				foreach($v as $w){
					$cleaned_labourer_data[$key]["total_wages"] += $w["amount"];
				}
			}
		}
		//echo "<pre>";print_r($cleaned_labourer_data);die;
		foreach($cleaned_labourer_data as $key => $value)
		{
			foreach($value['wage_dates'] as $wage_date){
				$date_amount[$key][$wage_date] = 0;
				foreach($value["wages"][$wage_date] as $k => $wage)
				{
					//echo "<pre>";print_r($wage);die;
					$date_amount[$key][$wage_date] += $wage["amount"];
				}
			}
			$cleaned_labourer_data[$key]["date_amounts"] = $date_amount[$key];
		}
	
		//make data visible
		$daily_total = $this->M_Laboursheet->get_daily_total($actual_start_date, $actual_end_date);
		$cleaned_daily_total = array();
		foreach($daily_total as $key => $value)
		{
			$cleaned_daily_total[$value->wage_date] = $value->daily_total;
		}
		$refined_wage_date = array();
		foreach($cleaned_labourer_data as $key => $value){
			//echo "<pre>";print_r($value['wage_dates']);die;
			//echo "Labourer: " . $key . "<br>";
			foreach($data['day_columns'] as $v)
			{
				//echo in_array($v['date'], $value['wage_dates']) . "=> ".$v['date']."<br/>";
				if(in_array($v['date'], $value['wage_dates']))
				{
					//echo $v['date'] . " = " . $value["date_amounts"][$v['date']] . "<br/>";
					//echo "<pre>";print_r($value["wages"]);die;
					//$refined_wage_data[$key][$v['date']] = $value['wages'][$v['date']]["amount"];
					$cleaned_labourer_data[$key]["wage_data"][$v['date']] = $value['date_amounts'][$v['date']];
				}
				else
				{
					//echo $v['date'] . " = -" . "<br/>";
					//$refined_wage_data[$key][$v['date']] = "-";
					$cleaned_labourer_data[$key]["wage_data"][$v['date']] = "-";
				}
			}
			//echo "<hr>";
		}//die;
		
		// echo "<pre>";print_r($cleaned_labourer_data);die;
		return array('labourer_data' => $cleaned_labourer_data,
		'days' => $_days,
		'dates' => $_dates);
	}
	
	function details($type = "labourer", $id, $from = null, $to = null)
	{
		$this->account->verify_session('laboursheet');
		switch($type)
		{
			case "labourer":
				$from = ($from == null) ? gmdate('Y-m-d', time() + (60 * 60 * 24 * - 7)) : $from;
				$to = ($to == null) ? gmdate('Y-m-d') : $to;
				$labourer_details = $this->M_Laboursheet->get_labourer_details($id, $from, $to);
				$cleaned_data = $this->clean_labourer_wage_data($id, $labourer_details, $from, $to);
				break;
			
			default:
				// echo "I am different";
				break;
		}
		$data['labourer_details'] = $cleaned_data[$id];
		$data["from"] = $from;
		$data["to"] = $to;
		$data["id"] = $id;
		$data['page_header'] = "Labourer Details";
		$data['menu'] = 'laboursheet';
		$data['sub_menu'] = 0;
		$data['content_view'] = 'LabourSheet/details_v';
		$this->template->call_dashboard_template($data);
	}
	
	function clean_labourer_wage_data($id, $labourer_details, $from, $to)
	{
		$this->account->verify_session('laboursheet');
		$cleaned_data = array();
		// echo "<pre>";print_r($labourer_details);die;
		if(count($labourer_details) > 0)
		{
			$counter = 0;
			foreach($labourer_details as $key => $value)
			{
				$cleaned_data[$value->labourer_id]['first_name'] = $value->labourer_firstname;
				$cleaned_data[$value->labourer_id]['last_name'] = $value->labourer_lastname;
				$cleaned_data[$value->labourer_id]['other_name'] = $value->labourer_othername;
				$cleaned_data[$value->labourer_id]["idno"] = $value->labourer_idno;
				$cleaned_data[$value->labourer_id]["mobileno"] = $value->labourer_mobileno;
				$cleaned_data[$value->labourer_id]["latest"] = $this->M_Laboursheet->get_latest_work($id)->latest;
				$cleaned_data[$value->labourer_id]["wages"][$counter]["date"] = $value->wage_date;
				$cleaned_data[$value->labourer_id]["wages"][$counter]["amount"] = $value->wage_amount;
				$cleaned_data[$value->labourer_id]["wages"][$counter]["project"] = $value->project_name;
				$cleaned_data[$value->labourer_id]["wages"][$counter]["purpose"] = $value->wage_purpose;
				$cleaned_data[$value->labourer_id]["wages"][$counter]["supervisor"] = $value->user_firstname . " " . $value->user_lastname;
				if($value->wage_structure_length != "")
				{
					if(strpos($value->wagestructure_task, "in m") != false){
						$cleaned_data[$value->labourer_id]["wages"][$counter]["task"] = str_replace("in m", $value->wage_structure_length . " " . $value->wagestructure_unit,$value->wagestructure_task);
					}
					else
					{
						$cleaned_data[$value->labourer_id]["wages"][$counter]["task"] = $value->wagestructure_task . " " . $value->wage_structure_length . " " . $value->wagestructure_unit;
					}
				}
				else
				{
					$cleaned_data[$value->labourer_id]["wages"][$counter]["task"] = $value->wagestructure_task;
				}
				$cleaned_data[$value->labourer_id]['wage_total'] = $this->M_Laboursheet->get_labourer_total_wage($id, $from, $to)->labourer_total;
				$cleaned_data[$value->labourer_id]['purposes'][$value->wage_date][] = $value->wage_purpose;
				$cleaned_data[$value->labourer_id]['tasks'][$value->wage_date][] = array('task' => $value->wagestructure_task, 'unit' => $value->wagestructure_unit, 'length' => $value->wage_structure_length);
				//$cleaned_data[$value->labourer_id]['projects'][$value->wage_date][] = $value->project_name;
				$counter++;
			}
			// echo "<pre>";print_r($cleaned_data);die;
			//combine purposes
			foreach($cleaned_data as $key => $value)
			{
				$purposes = $value["purposes"];
				$wages = $value["tasks"];
				$daily_data = array();
				foreach($purposes as $date => $purpose)
				{
					$keys = count($purpose);
					$wage_purpose = "";
					if($keys > 1)
					{
						if($keys == 2)
						{
							$wage_purpose = implode(" and ", $purpose);
						}
						else
						{
							$arrays = $purpose;
							$last_purpose = end($purpose);
							array_pop($purpose);
							$wage_purpose = implode(", ", $purpose) . " and " . $last_purpose;
						}
					}
					else
					{
						$wage_purpose = $purpose[0];
					}
					$daily_data["purposes"][$date] = $wage_purpose;
				}
				$cleaned_tasks = array();
				$counter = 0;
				foreach ($wages as $date => $wage) {
					foreach ($wage as $w) {
						if($w["unit"] != ""){
							if($w["length"] != "" && $w["length"] > 0)
							{
								if(strpos($w['task'], "in m") != false){
									$w['task'] = str_replace("in m",$w['length']." " .$w['unit'],$w['task']);
								}
								else
								{
									$w['task'] = $w['task'] . " " . $w['length'] . " " . $w['unit'];
								}
							}
						}
						else
						{
							$w['task'] = $w['task'];
						}
						$cleaned_tasks[$date][] = $w['task'];
					}
					$counter++;
				}
				//echo "<pre>";print_r($cleaned_tasks);die;
				$task = "";
				foreach ($cleaned_tasks as $date => $cleaned_task) {
					$numberoftasks = count($cleaned_task);
					if($numberoftasks > 1)
					{
						if($numberoftasks == 2)
						{
							$task = implode(" and ", $cleaned_task);
						}
						else
						{
							$arrays = $cleaned_task;
							$last_task = end($cleaned_task);
							array_pop($cleaned_task);
							$task = implode(", ", $cleaned_task) . " and " . $last_task;
						}
					}
					else
					{
						$task = $cleaned_task[0];
					}

					$daily_data["tasks"][$date] = $task;
				}
				$cleaned_data[$key]['combined_tasks'] = $daily_data["tasks"];
				$cleaned_data[$key]['combined_wage_purposes'] = $daily_data["purposes"];
				//echo "<pre>";print_r($cleaned_data);die;
			}
			
			$daily_totals = $this->M_Laboursheet->get_daily_total_by_labourer($id, $from, $to);
			$supervisors = $this->M_Laboursheet->get_daily_supervisors($id, $from, $to);
			$supervisor_data = array();

			if(count($supervisors) > 0)
			{
				foreach ($supervisors as $supervisor) {
					$supervisor_data[$supervisor->wage_date][] = $supervisor->user_firstname . " " . $supervisor->user_lastname;
				}

				foreach ($supervisor_data as $date => $data) {
					if(count($data) > 1)
					{
						$supervisor_data[$date] = implode(' / ', $data);
					}
					else
					{
						$supervisor_data[$date] = $data[0];
					}
				}
			}
			$cleaned_totals = $wage_projects = array();
			foreach ($cleaned_data[$id]['wages'] as $k => $value) {
				$wage_projects[$value['date']] = $value['project'];
			}

			foreach($daily_totals as $total)
			{
				$cleaned_totals[$total->wage_date] = array('total_wage' => $total->daily_total, 'purpose' => $cleaned_data[$key]['combined_wage_purposes'][$total->wage_date], 'project' => $wage_projects[$total->wage_date], 'task' => $cleaned_data[$key]['combined_tasks'][$total->wage_date], 'supervisors' => $supervisor_data[$total->wage_date]);
			}
			$cleaned_data[$id]["daily_totals"] = $cleaned_totals;
		}
		else
		{
			echo "nothing";die;
		}

		// echo "<pre>";print_r($cleaned_data);die;
		return $cleaned_data;
	}
	function get_excel_data()
	{
		$this->account->verify_session('laboursheet');
		//echo "<pre>";print_r($_FILES);die;
		$filename = basename($_FILES["excel_file"]["name"]);
		$target_dir = "uploads/";
		$target_file = $target_dir.basename($_FILES["excel_file"]["name"]);
		$response = move_uploaded_file($_FILES["excel_file"]["tmp_name"], $target_file);
		$excel_data = $this->export->read_excel_data($target_file);
		$sliced_excel_data = array_slice($excel_data, 2);
		
		$keys = array("wagestructure_id", "wagestructure_task", "wagestructure_rate", "wagestructure_hasmetres");

		$cleaned_array = array();
		foreach($sliced_excel_data as $key => $excel_data)
		{
			foreach ($excel_data as $k => $v) {
				$cleaned_array[$key][$keys[$k]] = $v;
				if($keys[$k] == "wagestructure_hasmetres"){
					if (strpos($excel_data[1],'in m') !== false) {
						$cleaned_array[$key][$keys[$k]] = 1;
					}
					else
					{
						$cleaned_array[$key][$keys[$k]] = 0;
					}
				}
			}
		}
		$this->M_Laboursheet->insert_wagestructure($cleaned_array);
	}
	
	function wagestructure()
	{
		$this->account->verify_session('wagestructure');
		$data['page_header'] = "Wage Structure";
		$data['wage_structure_table'] = $this->create_wage_structure_table($this->M_Laboursheet->get_wage_structure());
		$data['type'] = 'html';
		$data['menu'] = 'wage-structure';
		$data['structure_units'] = $this->get_structure_units();
		$data['sub_menu'] = 1;
		$data['content_view'] = 'LabourSheet/wage_structure_v';
		$this->template->call_dashboard_template($data);
	}

	function upload_excel()
	{
		$this->account->verify_session('wagestructure');
		$this->load->view('LabourSheet/upload_labour_structure_v');		
	}
	
	function create_wage_structure_table($wage_structure, $type = "html")
	{
		$this->account->verify_session('wagestructure');
		$table = "";
		if(count($wage_structure) > 0)
		{
			$counter = 1;
			foreach($wage_structure as $wage_item)
			{
				$table .= "<tr>";
				$table .= "<td>{$counter}</td>";
				$table .= "<td>{$wage_item->wagestructure_task}</td>";
				$table .= "<td>{$wage_item->wagestructure_rate} ";
				if($wage_item->wagestructure_unit != "" && substr($wage_item->wagestructure_unit, -1) === "s")
				{
					$table .= "Per ". substr($wage_item->wagestructure_unit, 0, -1);
				}
				else if($wage_item->wagestructure_unit != "")
				{
					$table .= "Per " . $wage_item->wagestructure_unit;
				}
				$table .= "</td>";
				if($type == "html"){
					$permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));
					if($permission == "admin")
					{
						$table .= "<td><a class = 'call_modal' data-url = '".base_url()."LabourSheet/editstruct/{$wage_item->wagestructure_id}'>Edit</a> | <a href = '".base_url()."LabourSheet/deletestruct/{$wage_item->wagestructure_id}' class = 'delete_task'>Delete</a></td>";
					}
					
				}
				$table .= "</tr>";
				$counter++;
			}
		}
		
		return $table;
	}
	
	function editstruct($struct_id = NULL)
	{
		$this->account->verify_session('wagestructure_edit');
		if($struct_id == NULL && $_POST)
		{
			$struct_id = $this->input->post('wagestructure_id');
			unset($_POST['form_action']);
			unset($_POST['wagestructure_id']);
			
			$_POST["wagestructure_hasmetres"] = (isset($_POST["wagestructure_hasmetres"]) && $this->input->post("wagestructure_hasmetres") == "Yes") ? 1 : 0;
			
			$data = $this->input->post();
			//echo "<pre>";print_r($this->input->post());die;
			$this->M_Laboursheet->update_wagestructure($struct_id, $data);
			
			redirect(base_url() . 'LabourSheet/wagestructure' );
		}
		else if($struct_id != NULL)
		{
			$main_data["structure_details"] = $this->M_Laboursheet->get_structure_details($struct_id);
			$main_data["structure_units"] = $this->get_structure_units();
			$data['details'] = $main_data["structure_details"];
			$data['view'] = $this->load->view('LabourSheet/edit_add_structure_v', $main_data, TRUE);
			echo json_encode($data);
		}
	}
	
	function delete($wage_id, $date)
	{
		$data['is_active'] = 0;
		$this->M_Laboursheet->edit_laboursheet($wage_id, $data);
		redirect(base_url() . "LabourSheet/daily/{$date}");
	}

	function deleted()
	{
		$data['page_header'] = "Deleted Laboursheet";
		$data['menu'] = 'laboursheet';
		$data['sub_menu'] = 1;
		$data['laboursheet_table'] = $this->create_deleted_laboursheets_table();
		$data['content_view'] = 'LabourSheet/deleted_laboursheet_v';
		$this->template->call_dashboard_template($data);
	}

	function restore_laboursheet($wage_id)
	{
		$data['is_active'] = 1;
		$this->M_Laboursheet->edit_laboursheet($wage_id, $data);
		redirect(base_url() . "LabourSheet/deleted");
	}
	function create_deleted_laboursheets_table()
	{
		$laboursheet_table = "";
		$deleted_items = $this->M_Laboursheet->get_deleted_laboursheet_items();

		if (count($deleted_items) > 0) {
			$counter = 1;
			foreach ($deleted_items as $key => $value) {
				$laboursheet_table .= "<tr>";
				$laboursheet_table .= "<td>{$counter}</td>";
				$laboursheet_table .= "<td>{$value->labourer_lastname}, {$value->labourer_firstname} {$value->labourer_othername}</td>";
				$laboursheet_table .= "<td>{$value->labourer_idno}</td>";
				$laboursheet_table .= "<td>{$value->wage_date}</td>";
				$laboursheet_table .= "<td>{$value->project_name}</td>";
				$laboursheet_table .= "<td>{$value->wagestructure_task}</td>";
				$laboursheet_table .= "<td>{$value->wage_amount}</td>";
				$laboursheet_table .= "<td><a class = 'restore_laboursheet_item' href = '".base_url()."LabourSheet/restore_laboursheet/{$value->wage_id}'>Restore Entry</a></td>";
				$laboursheet_table .= "</tr>";
				$counter++;
			}
		}

		return $laboursheet_table;
	}

	function deletestruct($struct_id)
	{
		$this->account->verify_session('wagestructure_edit');
		$data['is_active'] = 0;
		$this->M_Laboursheet->update_wagestructure($struct_id, $data);
		redirect(base_url() . 'LabourSheet/wagestructure' );
	}
	
	function addstruct()
	{
		$this->account->verify_session('wagestructure');
		if($_POST)
		{
			unset($_POST['form_action']);
			unset($_POST['wagestructure_id']);
			
			$_POST["wagestructure_hasmetres"] = (isset($_POST["wagestructure_hasmetres"]) && $this->input->post("wagestructure_hasmetres") == "Yes") ? 1 : 0;
			
			$data[0] = $this->input->post();
			$this->M_Laboursheet->insert_wagestructure($data);
			redirect(base_url() . 'LabourSheet/wagestructure' );
		}
		else
		{
			$data["structure_units"] = $this->get_structure_units();
			$data['view'] = $this->load->view('LabourSheet/edit_add_structure_v', $data, true);
			echo json_encode($data);
		}
	}
	
}