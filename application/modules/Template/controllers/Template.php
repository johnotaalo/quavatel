<?php

class Template extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	function call_dashboard_template($data = NULL)
	{
		// echo "<pre>";print_r($this->session->userdata);die;
		$this->load->model('Account/M_Account');
		$data['sidebar_details'] = $this->M_Account->get_user_by_id('user_id', $this->session->userdata('user_id'));
		$this->output->set_header("HTTP/1.0 200 OK");
		$this->output->set_header("HTTP/1.1 200 OK");
		$this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->load->view('Template/dashboard_v', $data);
	}
}