<?php
date_default_timezone_set('Africa/Nairobi');
class M_Project extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function get_all_projects()
	{
		$sql = "SELECT * FROM tbl_projects WHERE project_isactive = 1 ORDER BY project_id DESC";

		return $this->db->query($sql)->result();
	}

	function get_project_by_type($project_type = "isp")
	{
		$sql = "SELECT p.*, c.*, u.user_firstname, u.user_lastname, u.user_image FROM tbl_projects_acceptance p
		JOIN tbl_company c ON p.project_companyid = c.company_id
		JOIN tbl_users u ON u.user_id = p.project_userid 
		JOIN tbl_projects pt ON pt.project_id = p.project_id
		WHERE p.project_type = '{$project_type}'
		AND p.project_acceptance_active = 1 
		ORDER BY p.project_acceptanceid DESC";

		$query = $this->db->query($sql);

		$result = $query->result();

		return $result;
	}

	function get_project_responses($project_type, $project)
	{
		$table = array();
		if($project_type == "osp" || $project_type == "isp")
		{
			$table['name'] = "tbl_responses";
			$table['link_column'] = "response_projectid";
		}
		else if($project_type == "fat")
		{
			$table['name'] = "tbl_fatresponses";
			$table['link_column'] = "fat_projectid";
		}
		else
		{
			$table['name'] = "tbl_responses";
			$table['link_column'] = "response_projectid";
		}

		if($project_type != "fat" && $project_type != "mss"){
			$sql = "SELECT p.*, c.*, x.*,u.* FROM tbl_projects_acceptance p
			JOIN tbl_company c ON c.company_id = p.project_companyid
			JOIN {$table['name']} x ON x.{$table['link_column']} = p.project_acceptanceid
			JOIN tbl_users u ON p.project_userid = u.user_id
			WHERE p.project_acceptanceid = {$project}";
		}else if($project_type == "fat"){
			$sql = "SELECT p.*,c.*,fd.*, fr.*, u.* FROM tbl_projects_acceptance p
			JOIN tbl_company c ON c.company_id = p.project_companyid
			JOIN tbl_fatdetails fd ON fd.fat_projectid = p.project_acceptanceid
			JOIN tbl_fatresponses fr ON fr.fat_projectid = p.project_acceptanceid
			JOIN tbl_users u ON p.project_userid = u.user_id
			WHERE p.project_acceptanceid = {$project}";
		}else{
			$sql = "SELECT p.*, m.*,u.*,c.* FROM tbl_projects_acceptance p
			JOIN tbl_mss m ON m.mss_projectid = p.project_acceptanceid
			JOIN tbl_users u ON p.project_userid = u.user_id
			JOIN tbl_company c ON c.company_id = p.project_companyid
			WHERE p.project_acceptanceid = {$project}";
		}
		//echo $sql;die;

		$query = $this->db->query($sql);
		//echo "<pre>";print_r($query);die;

		$result = ($project_type != 'mss') ? $query->result() : $query->row();
		// echo "<pre>";print_r($result);die;
		return $result;
	}
	
	function get_project_images($project_id)
	{
		$sql = "SELECT * FROM tbl_photos WHERE photo_projectid = {$project_id}";
		$query = $this->db->query($sql);
		
		$result = $query->result();
		return $result;
	}

	function get_project_by_id($project_id)
	{
		$sql = "SELECT * FROM tbl_projects WHERE project_id = {$project_id} AND project_isactive = 1";

		return $this->db->query($sql)->row();
	}

	function deleteproject($project_id)
	{
		$deactivated_on = date('Y-m-d H:i:s');
		$this->db->query("UPDATE tbl_projects SET project_isactive = 0, project_deactivatedon = '{$deactivated_on}' WHERE project_id = {$project_id}");
	}

	function get_inactive_projects()
	{
		$sql = "SELECT * FROM tbl_projects WHERE project_isactive = 0 ORDER BY project_deactivatedon DESC";

		return $this->db->query($sql)->result();
	}

	function restore_project($project_id)
	{
		$this->db->query("UPDATE tbl_projects SET project_isactive = 1, project_deactivatedon = NULL WHERE project_id = {$project_id}");
	}

	function get_projects_awaiting()
	{
		$sql = "SELECT p.*, CONCAT(u.user_firstname, ' ' ,u.user_lastname) as created_by FROM tbl_projects p
		JOIN tbl_users u ON u.user_id = p.project_createdby 
		WHERE p.project_approved = 0 AND p.project_isactive = 1";

		return $this->db->query($sql)->result();
	}

	function approve_project($project_id)
	{
		$this->db->query("UPDATE tbl_projects SET project_approved = 1 WHERE project_id = {$project_id}");
	}

	function deletereport($project_type, $project_acceptanceid)
	{
		$this->db->query("UPDATE tbl_projects_acceptance SET project_acceptance_active = 0 WHERE project_acceptanceid = {$project_acceptanceid} AND project_type = '{$project_type}'");
	}

	function get_deleted_reports($project_type)
	{
		return $this->db->query("SELECT p.*, c.*, u.user_firstname, u.user_lastname, u.user_image FROM tbl_projects_acceptance p
		JOIN tbl_company c ON p.project_companyid = c.company_id
		JOIN tbl_users u ON u.user_id = p.project_userid 
		WHERE p.project_type = '{$project_type}'
		AND p.project_acceptance_active = 0
		ORDER BY p.project_startdate DESC")->result();
	}

	function restore_report($project_type, $project_acceptanceid)
	{
		$this->db->query("UPDATE tbl_projects_acceptance SET project_acceptance_active = 1 WHERE project_acceptanceid = {$project_acceptanceid} AND project_type = '{$project_type}'");
	}

	function project_acceptance_exists($project_id)
	{
		$this->db->where('project_id', $project_id);
		$this->db->where_in('project_type', ['fat', 'osp', 'isp']);
		$query = $this->db->get('tbl_projects_acceptance');

		if ($query->num_rows() > 0) {
			return true;
		}
		else
		{
			return false;
		}
	}

	function get_mss_data()
	{
		$sql = "SELECT tbl_projects.*
				FROM tbl_projects, tbl_projects_acceptance
				WHERE tbl_projects.project_isactive =1
				AND tbl_projects.project_approved =1
				AND tbl_projects.project_id NOT
				IN (

				SELECT tbl_projects_acceptance.project_id
				FROM tbl_projects_acceptance WHERE project_type != 'mss'
				)
				AND tbl_projects.project_enddate <= CURDATE( ) - INTERVAL 1 
				DAY GROUP BY tbl_projects.project_id
				ORDER BY tbl_projects.project_id DESC";
		return $this->db->query($sql)->result();
	}
	
	function mark_project_completion($project_id, $complete_flag)
	{
		$this->db->where('project_id', $project_id);
		$this->db->update('tbl_projects', ['project_completed' => $complete_flag]); 
	}
}