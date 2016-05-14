<?php

class Dashboard extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->module('Analytics');
	}

	function index()
	{
		$this->account->verify_session('home');
		$data['project_count'] = $this->analytics->get_project_count();
		$data['latest_project'] = $this->analytics->latest_project();
		$data['collaborative_user'] = $this->analytics->get_most_collaborative_user();
		$least_project = array_keys($this->analytics->get_project_count(), min($this->analytics->get_project_count()));
		$data['least_project_type'] = array('project_type' => $least_project[0], 'count' => min($this->analytics->get_project_count()));
		//$data['questions'] = $this->create_questions_dropdown('isp', 'raw');
		$data['menu'] = 'home';
		$data['sub_menu'] = 0;
		$data['page_header'] = "Home Dashboard";
		$data['content_view'] = "Dashboard/home_v";
		$this->template->call_dashboard_template($data);
	}
	
	function create_questions_dropdown($project_type, $nature)
	{
		$questions = $this->analytics->get_project_questions($project_type);
		//var_dump($questions);die;
		$raw = "";
		if(count($questions) > 0){
			foreach($questions as $question){
				$segmented_question = str_replace(" ", "_", $question);
				$raw .= "<option value = '{$segmented_question}'>{$question}</option>";
				
			}
		}
		
		echo ($nature == "raw") ? $raw : json_encode($questions);
	}
}