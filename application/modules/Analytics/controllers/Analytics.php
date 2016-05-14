<?php

class Analytics extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("M_Analytics");
	}
	
	function get_project_count()
	{
		$project_count = $this->M_Analytics->get_project_count();
		//echo "<pre>";print_r($project_count);die;
		$clean_data = array();
		foreach($project_count as $project)
		{
			$clean_data[$project->project_type] = $project->projects;
		}
		
		$found = array_keys($clean_data);
		$not_found = array_diff($this->project_types, $found);
		
		foreach($not_found as $project)
		{
			$clean_data[$project] = 0;
		}
		
		
		return $clean_data;
	}
	function get_project_response_data($project_type, $question)
	{
		$questions = $this->get_project_questions($project_type);
		$question = ($question !== "null") ? str_replace("_", " ", $question) : $questions[0];
		$responses = $this->M_Analytics->get_project_responses_by_question_type($project_type, $question);
		
		//sanitizing to match pie data
		$sanitized = array();
		foreach($responses as $response)
		{
			$d[0] = $response->response_answer;
			$d[1] = $response->projects;
			
			array_push($sanitized, $d);
		}
		

		echo json_encode($sanitized, JSON_NUMERIC_CHECK);
	}
	
	function get_project_questions($project_type)
	{
		$questions = $this->M_Analytics->get_response_questions_by_project_type($project_type);
		$cleaned_questions = array();
		foreach($questions as $question)
		{
			$cleaned_questions[] = $question->response_question;
		}
		
		return $cleaned_questions;
	}
	
	function latest_project()
	{
		$project = $this->M_Analytics->get_latest_project();
		if(count($project) == 1)
		{
			return $project;
		}
	}
	
	function get_most_collaborative_user()
	{
		$collaborative_user = $this->M_Analytics->get_most_collaborative_user();
		if(count($collaborative_user) == 1)
		{
			return $collaborative_user;
		}
	}
}