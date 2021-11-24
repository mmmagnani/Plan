<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plan extends CI_Controller {


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Plan_model','',TRUE);
        $this->load->helper('formater');
    }

    public function index() {
        if( (!session_id()) || (!$this->session->userdata('logado'))){
            redirect('plan/login');
        }
		
        $mes = $this->input->get('mes');
        $mes2 = $this->input->get('mes2');
        if ($mes)
        {
            $filter = " AND EXTRACT(MONTH FROM registros.`data`) BETWEEN 1 AND " . $mes;
        }
        if ($mes2)
        {
            $filter2 = " WHERE EXTRACT(MONTH FROM execucoes.data_empenho) BETWEEN 1 AND " .
                $mes2;
        }
        if (!$mes2)
        {
            $this->data['estatisticas_orcamento'] = $this->Plan_model->
                getEstatisticasByOrcamento();
        } else
        {
            $this->data['estatisticas_orcamento'] = $this->Plan_model->
                getEstatisticasByOrcamento($filter2);
        }
        if (!$mes)
        {
            $this->data['graph'] = $this->Plan_model->getGraph();
        } else
        {
            $this->data['graph'] = $this->Plan_model->getGraph($filter);
        }
		$this->data['totalobjetivos'] = $this->Plan_model->count('objetivos');
		$this->data['totalprojetos'] = $this->Plan_model->count('projetos');
		$this->data['totaltarefas'] = $this->Plan_model->countTar('tarefas');
		$this->data['totalindicadores'] = $this->Plan_model->count('indicadores');
        $this->data['menuPainel'] = 'Painel';
        $this->data['view'] = 'plan/painel';
        $this->load->view('tema/topo', $this->data);      
    }

    public function conta() {
        if( (!session_id()) || (!$this->session->userdata('logado'))){
            redirect('plan/login');
        }

        $this->data['usuario'] = $this->Plan_model->getById($this->session->userdata('id'));
        $this->data['view'] = 'plan/minhaConta';
        $this->load->view('tema/topo',  $this->data);
     
    }

    public function alterarSenha() {
        if( (!session_id()) || (!$this->session->userdata('logado'))){
            redirect('plan/login');
        }

        $oldSenha = $this->input->post('oldSenha');
        $senha = $this->input->post('novaSenha');
        $result = $this->Plan_model->alterarSenha($senha,$oldSenha,$this->session->userdata('id'));
        if($result){
            $this->session->set_flashdata('success','Senha Alterada com sucesso!');
            redirect(base_url() . 'index.php/plan/conta');
        }
        else{
            $this->session->set_flashdata('error','Ocorreu um erro ao tentar alterar a senha!');
            redirect(base_url() . 'index.php/plan/conta');
            
        }
    }

    public function pesquisar() {
        if( (!session_id()) || (!$this->session->userdata('logado'))){
            redirect('plan/login');
        }
        
        $termo = $this->input->get('termo');

        $data['results'] = $this->Plan_model->pesquisar($termo);
        $this->data['objetivos'] = $data['results']['objetivos'];
        $this->data['projetos'] = $data['results']['projetos'];
        $this->data['tarefas'] = $data['results']['tarefas'];
        $this->data['view'] = 'plan/pesquisa';
        $this->load->view('tema/topo', $this->data);
      
    }
	public function changeyear(){
		if( (!session_id()) || (!$this->session->userdata('logado'))){
            redirect('plan/login');
        }	
		$novoano = $this->input->get('novoano');
		$session_data = array('anofiscal' => $novoano);
		$this->session->set_userdata($session_data);
		redirect('plan');	
	}

    public function login(){
        
        $this->load->view('plan/login');
        
    }
    public function sair(){
        $this->session->sess_destroy();
        redirect('plan/login');
    }


    public function verificarLogin(){
        
        header('Access-Control-Allow-Origin: '.base_url());
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type');
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'E-mail', 'valid_email|required|trim');
        $this->form_validation->set_rules('senha', 'Senha', 'required|trim');
        //$this->form_validation->set_rules('anofiscal', 'Ano Fiscal', 'required|trim');
        if ($this->form_validation->run() == false) {
            $json = array('result' => false, 'message' => validation_errors());
            echo json_encode($json);
        }
        else {
            $email = $this->input->post('email');
            $password = $this->input->post('senha');
            $anofiscal = date('Y');//$this->input->post('anofiscal');
            $this->load->model('Plan_model');
            $user = $this->Plan_model->check_credentials($email);

            if($user){
				$apoiadora = $this->Plan_model->checkApoiadora($user->om_id)->om_id_apoiadora;
				if(empty($apoiadora))
				{
				  $apoiadora = $user->om_id;
				}
                if(password_verify($password, $user->senha)){
                    $session_data = array(
                        'nome' => $user->nome,
                        'email' => $user->email,
                        'id' => $user->IdUsuarios,
                        'permissao' => $user->permissoes_id,
                        'om_id' => $user->om_id,
                        'setor_id' => $user->setor_id,
                        'logado' => true,
                        'anofiscal' => $anofiscal,
						'apoiadora' => $apoiadora);
						
                    $this->session->set_userdata($session_data);
                    $json = array('result' => true);
                    echo json_encode($json);
                }
                else{
                    $json = array('result' => false, 'message' => 'Os dados de acesso estão incorretos.');
                    echo json_encode($json);
                }
            }
            else{
                $json = array('result' => false, 'message' => 'Usuário não encontrado, verifique se suas credenciais estão corretas.');
                echo json_encode($json);
            }
        }
        die();
    }


    public function backup(){

        if( (!session_id()) || (!$this->session->userdata('logado'))){
            redirect('plan/login');
        }

        if(!$this->permission->checkPermission($this->session->userdata('permissao'),'cBackup')){
           $this->session->set_flashdata('error','Você não tem permissão para efetuar backup.');
           redirect(base_url());
        }

        
        
        $this->load->dbutil();
        $prefs = array(
                'format'      => 'zip',
                'foreign_key_checks' => false,
                'filename'    => 'backup'.date('d-m-Y').'.sql'
              );

        $backup = $this->dbutil->backup($prefs);

        $this->load->helper('file');
        write_file(base_url().'backup/backup.zip', $backup);

        $this->load->helper('download');
        force_download('backup'.date('d-m-Y H:m:s').'.zip', $backup);
    }
	
}
