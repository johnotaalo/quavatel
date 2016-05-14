<?php
//error_reporting(1);
ini_set('memory_limit', '-1');
date_default_timezone_set('Africa/Nairobi');
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends MX_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->project_types = array("isp", "osp", "fat", "mss");
		$this->load->module("Account");
		$this->load->module("Template");
		$this->load->module("Export");
		$this->load->module("Mail");
	}

	function get_structure_units()
	{
		$sql = "SELECT DISTINCT wagestructure_unit FROM tbl_wagestructure WHERE wagestructure_unit IS NOT NULL";

		$wagestructure_units=$this->db->query($sql)->result();

		$sanitized_structure_units = array();

		foreach ($wagestructure_units as $key => $value) {
			$sanitized_structure_units[] = $value->wagestructure_unit;
		}

		return json_encode($sanitized_structure_units);
	}
	
}