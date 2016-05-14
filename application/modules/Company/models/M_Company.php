<?php

class M_Company extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function get_all_companies()
	{
		$query = $this->db->get('tbl_company');
		$result = $query->result();
		
		return $result;
	}
	
	function add_company()
	{
		$query = $this->db->insert('tbl_company', $this->input->post());
	}
	
	function get_project_by_company($project_type, $company_id)
	{
		$sql = "SELECT c.company_id, COUNT(p.project_id) AS projects FROM tbl_company c JOIN tbl_projects p ON c.company_id = p.project_companyid WHERE c.company_id = {$company_id} AND p.project_type = '{$project_type}'";
		//echo $sql;die;
		$query = $this->db->query($sql);
		
		$result = $query->row();
		
		return $result;
	}
	
	function get_total_projects_by_company($company_id)
	{
		$sql = "SELECT c.company_id, COUNT(p.project_id) AS projects FROM tbl_company c JOIN tbl_projects p ON c.company_id = p.project_companyid WHERE c.company_id = {$company_id}";
		$query = $this->db->query($sql);
		
		$result = $query->row();
		
		return $result;
	}
	
	function get_company_by_id($company_id)
	{
		$SQL = "SELECT * FROM tbl_company WHERE company_id = {$company_id}";
		$query = $this->db->query($SQL);
		
		return $query->row();
	}
	
	function update_details($company_id)
	{
		$this->db->where('company_id', $company_id);
		$this->db->update('tbl_company', $this->input->post()); 
	}
}