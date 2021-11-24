<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
    'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
	'smtp_host' => 'mail.intraer',
    'smtp_port' => 587,
    'smtp_user' => 'magnanimmm@fab.mil.br',
    'smtp_pass' => 'Mmm072364@',
    'smtp_crypto' => 'starttls', //can be 'ssl' or 'tls' or starttls for example
    'mailtype' => 'html', //plaintext 'text' mails or 'html'
    'smtp_timeout' => '4', //in seconds
    'charset' => 'utf-8',
    'wordwrap' => TRUE
);
