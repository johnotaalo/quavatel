<?php

class M_Analytics extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function get_project_count()
	{
		$sql = "SELECT COUNT(p.project_acceptanceid) as projects, p.project_type FROM tbl_projects_acceptance p
		JOIN tbl_company c ON p.project_companyid = c.company_id
		JOIN tbl_users u ON u.user_id = p.project_userid
		JOIN tbl_projects pt ON pt.project_id = p.project_id
		WHERE p.project_acceptance_active = 1
		GROUP BY project_type";
		
		$query = $this->db->query($sql);
		
		$result = $query->result();

		return $result;
	}
	
	function get_project_responses_by_question_type($project_type, $question)
	{
		$sql = "SELECT COUNT(r.response_projectid) as projects, r.response_answer FROM tbl_responses r
		JOIN tbl_projects p ON p.project_id = r.response_projectid
		WHERE p.project_type = '{$project_type}'
		AND r.response_question = '{$question}'
		GROUP BY r.response_answer";
		
		return $this->db->query($sql)->result();
	}
	
	function get_response_questions_by_project_type($project_type)
	{
		$not_needed = array('"Client Engineer"', '"Client Engineer Remarks"', '"Date Accepted"');
		$statement = implode(' AND r.response_question != ', $not_needed);
		
		$sql = "SELECT DISTINCT(r.response_question) FROM tbl_responses r
		JOIN tbl_projects p ON p.project_id = r.response_projectid
		WHERE r.response_question != {$statement}
		AND p.project_type = '$project_type'";
		
		return $this->db->query($sql)->result();
	}
	
	function get_latest_project()
	{
		return $this->db->query("SELECT MAX(project_id) as project_id, project_name, project_type FROM tbl_projects")->row();
	}
	
	function get_most_collaborative_user()
	{
		$sql = "
			SELECT MAX(y.projects) as projects, y.user_firstname, y.user_lastname
			FROM (SELECT count(p.project_id) as projects, u.user_firstname, u.user_lastname FROM tbl_projects p JOIN tbl_users u ON u.user_id = p.project_userid GROUP BY u.user_id) y
		";
		
		return $this->db->query($sql)->row();
	}
}