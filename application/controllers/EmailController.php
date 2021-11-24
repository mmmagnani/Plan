<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EmailController extends CI_Controller {

    public function __construct() {
        parent:: __construct();

        $this->load->helper('url');
    }

    public function send() {
		$filename = 'assets/planilhas/base_certames.xlsx';
		if (file_exists($filename)) {
		  $datefile = date("d/m/Y", filemtime($filename));
		  $datenow = date("d/m/Y", strtotime("-1 day"));
			if($datefile == $datenow) {
				$this->load->library('email');
				$this->load->config('email');
				
				$from = $this->config->item('smtp_user');
				$bcc = 'marcelommagnani@gmail.com';
				$to = 'chefe.gapnt@gmail.com';

				$subject = 'Planilha Base dos Certames';
				$message = 'Planilha do dia ' . $datefile;
				
				$this->email->set_newline("\r\n");
				$this->email->from($from);
				$this->email->to($to);
				//$this->email->bcc($bcc);
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->attach($filename);
				
				if (!$this->email->send()) {
					show_error($this->email->print_debugger());
				} else {
					echo "E-mail enviado.";
				}
			} else {
				echo utf8_encode("Planilha não foi alterada");
			}	
		} else {
		  echo utf8_encode("Arquivo não existe");
		}
				
    }
}