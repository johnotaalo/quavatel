<?php

class M_Account extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function auth($username, $password)
	{
		$sql = "SELECT user_id, user_emailaddress FROM tbl_users WHERE user_emailaddress = '{$username}' AND user_password = '".md5($password)."' AND user_status = 1";
		
		$query = $this->db->query($sql);
		$result = $query->row();
		
		return $result;
	}
	function get_users()
	{
		$query = $this->db->query("SELECT * FROM tbl_users WHERE user_type != 'admin' ORDER BY user_createdon DESC");
		$result = $query->result();
		
		return $result;
	}
	
	function get_user_by_id($identifier, $value)
	{
		$query = $this->db->query("SELECT * FROM tbl_users WHERE {$identifier} = '{$value}'");
		$result = $query->row();
		
		return $result;
	}
	
	function activation($status, $user_id)
	{
		$this->db->query("UPDATE tbl_users SET user_status = {$status} WHERE user_id = {$user_id}");
	}
	
	function adduser()
	{
		$_POST['user_password'] = md5($this->input->post('user_password'));
		$added = $this->db->insert("tbl_users", $this->input->post());
		return $added;
	}
	
	function reset_password($password, $user_id)
	{
		$reset = $this->db->query("UPDATE tbl_users SET user_password = '".md5($password)."' WHERE user_id = {$user_id}");
		return $reset;
	}

	function update_user_data($user_data, $user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->update('tbl_users', $user_data);
	}
	
	function delete_user($user_id)
	{
		$this->db->query("DELETE FROM tbl_users WHERE user_id = {$user_id}");
	}

	function get_user_permission($identifier, $value)
	{
		$query = $this->db->query("SELECT user_type FROM tbl_users WHERE {$identifier} = '{$value}'");
		$result = $query->row();
		
		return $result->user_type;
	}
}