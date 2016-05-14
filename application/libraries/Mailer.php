<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailer {
    public function Mailer() {
    	include_once APPPATH.'/third_party/PHPMailer/PHPMailerAutoload.php';
    }
}