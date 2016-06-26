<?php

class M_Faultreporting extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function getFaultReports()
	{
		$sql = "SELECT r.id as report_id, r.*,t.* FROM tbl_fault_reporting r
		LEFT JOIN tbl_fault_reporting_technicians t ON t.fault_report_id = r.id
		GROUP BY r.id
		ORDER BY r.id DESC";

		$query = $this->db->query($sql);

		return $query->result();
	}

	function get_fault_counter()
	{
		$query = $this->db->query("SELECT status, count('id') as number FROM tbl_fault_reporting GROUP BY status");

		return $query->result();
	}

	function get_larget_ticket_no($user_initials)
	{
		$query = $this->db->query("SELECT MAX(`ticket_no`) as latest_ticket FROM tbl_fault_reporting WHERE ticket_no LIKE '{$user_initials}%'");

		return $query->row()->latest_ticket;
	}

	function save_report($post_data)
	{
		if (count($post_data)) {
			$this->db->insert('tbl_fault_reporting', $post_data);
			return $this->db->insert_id();
		}
		else
		{
			return false;
		}
	}

	function get_technical_staff()
	{
		$query = $this->db->query("SELECT * FROM tbl_users WHERE user_type = 'project_manager' OR user_type = 'acceptance'");

		return $query->result();
	}

	function addTechnician($data)
	{
		$this->db->insert('tbl_fault_reporting_technicians', $data);
	}

	function getReport($id)
	{
		$sql = "SELECT tfr.*, CONCAT(u.user_firstname, ' ', u.user_lastname) AS username FROM tbl_fault_reporting tfr
		JOIN tbl_users u ON tfr.reported_by = u.user_id 
		WHERE tfr.id = {$id}";		// $this->db->where('id', $id);
		$query = $this->db->query($sql);

		return $query->row();
	}

	function getStaffAssigned($id)
	{
		$sql = "SELECT CONCAT(u.user_firstname, ' ', u.user_lastname) AS username FROM tbl_fault_reporting_technicians tfrt
		JOIN tbl_users u ON u.user_id = tfrt.technician_id
		WHERE fault_report_id = {$id}";

		$query = $this->db->query($sql);

		return $query->result();
	}

	function get_question_responses($id)
	{
		$sql = "SELECT q . * , qr.report_id, qr.response, qr.fault_reporting_question as response_question
			FROM tbl_fault_reporting_questions q
			LEFT JOIN tbl_fault_reporting_question_responses qr ON qr.fault_reporting_question = q.id AND qr.report_id = {$id} ";

		$query = $this->db->query($sql);

		return $query->result();
	}

	function get_report_comments($id)
	{
		$this->db->where('report_id', $id);

		$query = $this->db->get('tbl_fault_reporting_technician_comments');

		return $query->result();
	}

	function get_report_clearance($id)
	{
		$this->db->select("*");
		$this->db->from("tbl_fault_reporting_clearance");
		$this->db->join("tbl_users", "tbl_users.user_id = tbl_fault_reporting_clearance.technician_id");
		$this->db->where('report_id', $id);

		$query = $this->db->get();

		return $query->row();
	}

	function get_materials_used($report_id)
	{
		$this->db->where('report_id', $report_id);

		$query = $this->db->get('tbl_fault_reporting_materials_used');

		return $query->result();
	}

	function get_event_log($id)
	{
		$this->db->where('id', $id);

		$query = $this->db->get('view_fault_reporting_event_log');

		return $query->row();
	}

	function confirmfault($id)
	{
		$data = ['status' => 'cleared'];

		$this->db->where('id', $id);
		$updated = $this->db->update('tbl_fault_reporting', $data);

		return $updated;
	}

	function get_technician_details($id)
	{
		$this->db->where('user_id', $id);

		$query = $this->db->get('tbl_users');

		return $query->row();
	}

	function addClearanceInformation($id)
	{
		$postData = [
			'date_time_cleared' => date('Y-m-d H:i:s'),
			'technician_id' => $this->session->userdata['user_id'],
			'report_id' => $id
		];

		$this->db->insert('tbl_fault_reporting_clearance', $postData);

		return true;
	}

	function getReportConfirmationInformation($report_id)
	{
		$this->db->where('fault_report_id', $report_id);

		$query = $this->db->get('tbl_fault_reporting_technicians');

		return $query->row();
	}

	function getReportPhotos($report_id)
	{
		$this->db->where('photo_faultid', $report_id);

		$this->db->from('tbl_faultphotos');

		$query = $this->db->get();

		return $query->result();
	}

	function get_clients()
	{
		$query = $this->db->get('tbl_company');

		return $query->result();
	}

	function getClientByFault($id)
	{
		$this->db->select('company_name, company_logo');
		$this->db->from('tbl_fault_reporting');
		$this->db->join('tbl_company', 'tbl_company.company_id = tbl_fault_reporting.company_id');
		$this->db->where('id', $id);

		$query = $this->db->get();

		return $query->row();
	}

	function getTimeCleared($id)
	{
		$this->db->from('tbl_fault_reporting_clearance');
		$this->db->where('report_id', $id);

		$query = $this->db->get();

		return $query->row();
	}

	function getQuestionResponsesByFault($fault_id)
	{
		$sql = "SELECT q.question, qr.response, q.question_category from tbl_fault_reporting_question_responses qr
		JOIN tbl_fault_reporting_questions q ON q.id = qr.fault_reporting_question
		WHERE qr.report_id = {$fault_id}";

		return $this->db->query($sql)->result();
	}
}