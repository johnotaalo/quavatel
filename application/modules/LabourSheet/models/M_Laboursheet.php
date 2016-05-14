<?php

class M_Laboursheet extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function get_day_data($date)
	{
		$sql = "SELECT l.*, w.*, p.project_name, ws.wagestructure_task, ws.wagestructure_unit, u.user_firstname, u.user_lastname FROM tbl_labourer l
		LEFT JOIN tbl_wages w ON l.labourer_id = w.wage_labourerid
		JOIN tbl_projects p ON p.project_id = w.wage_projectid
		JOIN tbl_wagestructure ws ON ws.wagestructure_id = w.wage_purpose_id
		JOIN tbl_users u ON u.user_id = w.wage_supervisorid
		WHERE w.wage_date = '{$date}'
		AND p.project_isactive = 1
		AND w.is_active = 1
		ORDER BY wage_id";
		
		return $this->db->query($sql)->result();
	}
	
	function get_day_total($date)
	{
		$sql = "SELECT SUM(wage_amount) as daily_total FROM tbl_wages
		WHERE wage_date = '{$date}'
		AND wage_projectid != 0
		AND is_active = 1 
		AND wage_projectid IN (SELECT project_id FROM tbl_projects WHERE project_isactive = 1)
		AND wage_labourerid IN (SELECT labourer_id FROM tbl_labourer)";
		return $this->db->query($sql)->row()->daily_total;
	}
	function get_week_data($from, $to)
	{
		$sql = "SELECT l.*, u.*,SUM(w.wage_amount)as amount FROM tbl_labourer l
				JOIN tbl_wages w ON w.wage_labourerid = l.labourer_id
				JOIN tbl_projects p ON p.project_id = w.wage_projectid
				JOIN tbl_wagestructure ws ON ws.wagestructure_id = w.wage_purpose_id
				JOIN tbl_users u ON u.user_id = w.wage_supervisorid
				WHERE w.wage_date BETWEEN '{$from}' AND '{$to}'
				AND w.wage_status = 0
				AND p.project_isactive = 1
				AND w.is_active = 1
				GROUP BY l.labourer_id
				ORDER BY w.wage_id DESC";
		$query = $this->db->query($sql);
		$result = $query->result();

		return $result;
	}
	
	function get_range_data($from, $to)
	{
		$sql = "SELECT l.*, w.*, p.project_name, w.wage_purpose, ws.wagestructure_task, ws.wagestructure_unit, u.* FROM tbl_labourer l
			JOIN tbl_wages w ON w.wage_labourerid = l.labourer_id
			JOIN tbl_projects p ON p.project_id = w.wage_projectid
			JOIN tbl_wagestructure ws ON ws.wagestructure_id = w.wage_purpose_id
			JOIN tbl_users u ON u.user_id = w.wage_supervisorid
			WHERE w.wage_date BETWEEN '{$from}' AND '{$to}'
			AND p.project_isactive = 1
			AND w.is_active = 1
			ORDER BY w.wage_id DESC";

		// echo $sql;die;
		
		return $this->db->query($sql)->result();
	}
	function get_total_payment($from, $to)
	{
		$sql = "SELECT SUM(wage_amount) as total_amount FROM tbl_wages
				WHERE wage_date BETWEEN '{$from}' AND '{$to}'
				AND wage_status = 0
				AND is_active = 1
				AND wage_projectid != 0 AND wage_projectid IN (SELECT project_id FROM tbl_projects WHERE project_isactive = 1)
				AND wage_labourerid IN (SELECT labourer_id FROM tbl_labourer)";
		$query = $this->db->query($sql);
		$result = $query->row();

		return $result;
	}
	
	function get_daily_total($from, $to)
	{
		$sql = "SELECT wage_date, SUM(wage_amount) as daily_total  FROM  `tbl_wages` WHERE wage_date BETWEEN '{$from}' AND '{$to}' AND wage_projectid != 0 AND is_active = 1 AND wage_projectid IN (SELECT project_id FROM tbl_projects WHERE project_isactive = 1) AND wage_labourerid IN (SELECT labourer_id FROM tbl_labourer) GROUP BY wage_date";
		$query = $this->db->query($sql);
		$result = $query->result();

		return $result;
	}
	
	function get_daily_total_by_labourer($id, $from, $to)
	{
		$sql = "SELECT wage_date, SUM(wage_amount) as daily_total  FROM  `tbl_wages` WHERE wage_labourerid = {$id} AND wage_date BETWEEN '{$from}' AND '{$to}' AND wage_projectid != 0 AND is_active = 1 AND wage_projectid IN (SELECT project_id FROM tbl_projects WHERE project_isactive = 1) GROUP BY wage_date";
		
		return $this->db->query($sql)->result();
	}
	function get_latest_work($id)
	{
		$sql = "SELECT max(wage_date) as latest FROM tbl_wages WHERE wage_labourerid = {$id} AND is_active = 1 AND wage_projectid IN (SELECT project_id FROM tbl_projects WHERE project_isactive = 1)";
		return $this->db->query($sql)->row();
	}
	function get_labourer_details($id, $from, $to)
	{
		$sql = "SELECT l.*, w.*, p.project_name, ws.wagestructure_task, ws.wagestructure_unit, u.user_firstname, u.user_lastname FROM tbl_labourer l, tbl_wages w, tbl_projects p, tbl_wagestructure ws, tbl_users u
		WHERE l.labourer_id = w.wage_labourerid
		AND w.wage_labourerid = {$id}
		AND w.is_active = 1
		AND p.project_id = w.wage_projectid 
		AND ws.wagestructure_id = w.wage_purpose_id
		AND u.user_id = w.wage_supervisorid
		AND w.wage_projectid IN (SELECT project_id FROM tbl_projects)
		AND w.wage_date BETWEEN '{$from}' AND '{$to}'
		AND p.project_isactive = 1";
		
		// echo $sql;die;
		
		return $this->db->query($sql)->result();
	}
	
	function get_projects()
	{
		$sql = "SELECT * FROM tbl_projects";
		$result = $this->db->query($sql)->result();
		
		return $result;
	}
	
	function get_labourer_total_wage($id, $from, $to)
	{
		$sql = "SELECT SUM(wage_amount) as labourer_total FROM tbl_wages
		WHERE wage_labourerid = {$id}
		AND is_active = 1
		AND wage_date BETWEEN '{$from}' AND '{$to}'
		AND wage_projectid != 0 AND wage_projectid IN (SELECT project_id FROM tbl_projects WHERE project_isactive = 1)";
		
		return $this->db->query($sql)->row();
	}

	function insert_wagestructure($wage_structure)
	{
		$this->db->insert_batch('tbl_wagestructure', $wage_structure);
	}
	
	function get_wage_structure()
	{
		$sql = "SELECT * FROM tbl_wagestructure WHERE is_active = 1";
		
		return $this->db->query($sql)->result();
	}
	
	function get_structure_details($struct_id)
	{
		$sql = "SELECT * FROM tbl_wagestructure WHERE wagestructure_id = {$struct_id} AND is_active = 1";
		
		return $this->db->query($sql)->row();
	}
	
	function update_wagestructure($struct_id, $data)
	{
		$this->db->where('wagestructure_id', $struct_id);
		$this->db->update('tbl_wagestructure', $data);
	}

	function get_daily_supervisors($id, $from, $to)
	{
		$sql = "SELECT u.user_firstname, u.user_lastname, w.wage_date FROM tbl_wages w
				JOIN tbl_users u ON u.user_id = w.wage_supervisorid
				WHERE w.wage_date BETWEEN '{$from}' AND '{$to}'
				AND w.is_active = 1
				AND w.wage_labourerid = {$id}";


		return $this->db->query($sql)->result();
	}

	function get_wage_by_id($wage_id)
	{
		$sql = "SELECT w.*, l.*, s.* FROM tbl_wages w
			JOIN tbl_labourer l ON l.labourer_id = w.wage_labourerid
			JOIN tbl_wagestructure s ON s.wagestructure_id = w.wage_purpose_id
			WHERE w.wage_id = {$wage_id} AND w.is_active = 1";

		return $this->db->query($sql)->row();
	}

	function edit_laboursheet($wage_id, $data = NULL)
	{
		$this->db->where('wage_id', $wage_id);
		$update_data = ($data == NULL) ? $_POST : $data;
		
		$this->db->update('tbl_wages', $update_data);
	}

	function get_deleted_laboursheet_items()
	{
		$sql = "SELECT l.*, w.*, p.project_name, ws.wagestructure_task, ws.wagestructure_unit, u.user_firstname, u.user_lastname FROM tbl_labourer l
		LEFT JOIN tbl_wages w ON l.labourer_id = w.wage_labourerid
		JOIN tbl_projects p ON p.project_id = w.wage_projectid
		JOIN tbl_wagestructure ws ON ws.wagestructure_id = w.wage_purpose_id
		JOIN tbl_users u ON u.user_id = w.wage_supervisorid
		WHERE p.project_isactive = 1
		AND w.is_active = 0
		ORDER BY wage_id";
		
		return $this->db->query($sql)->result();
	}
}