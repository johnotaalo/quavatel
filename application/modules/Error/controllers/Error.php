<?php 

class Error extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	function access_denied()
	{
		$data['heading'] = "Access Denied";
		$data['message'] = "You do not have sufficient permissions to access this page! Persistent access may lead to you being blocked. For any queries contact us via email: app@quavatel.co.ke";
		$this->load->view('Error/error_401', $data);
	}
}