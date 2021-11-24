<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

class Registros extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Registros_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('indicators'));
            redirect(base_url());
        } 

        $data['view'] = 'registros/registros_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

		if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {	
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('indicators'));
            redirect(base_url());
        } 

        $this->load->model('Registros_model');
		$result_data = $this->Registros_model->get_datatables();
				
        $data = array();

        foreach ($result_data as $row) {
            $line = array();

            $line[] = $row->descricao;
			$line[] = $row->objetivo;
		
		
			$line[] = '<a href="' . site_url('registros/read/' . $row->IdIndicador) . '" class="btn btn-dark" title="' . $this->lang->line('app_view') . '"><i class="fa fa-eye"></i></a>
					   <a href="' . site_url('medicoes?id=' . $row->IdIndicador) . '" class="btn btn-info" title="' . $this->lang->line('app_records') . '"><i class="fa fa-edit"></i> ' . $this->lang->line('app_records') . '</a>';

            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
			'recordsTotal' => $this->Registros_model->get_all_data(),
            'recordsFiltered' => $this->Registros_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function read($id)
    {		
		$mes = $this->input->get('mes');

        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('registros');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('indicators'));
            redirect(base_url());
        }
		
		if ($mes)
        {
            $filter = " AND EXTRACT(MONTH FROM registros.`data`) = " . $mes;
        }

		$this->load->model('Indicadores_model');
        $row = $this->Indicadores_model->with('projeto')->with('gerset')->get($id);
        if ($row) {
			if($row->situacao == 1) {
				$situacao = $this->lang->line('app_active');
			} else {
				$situacao = $this->lang->line('app_inactive');
			}
			
			switch($row->periodicidade_id){
				case 1: $periodicidade_id = ucfirst($this->lang->line('monthly'));
				break;
				case 2: $periodicidade_id = ucfirst($this->lang->line('bimonthly'));
				break;
				case 3: $periodicidade_id = ucfirst($this->lang->line('semiannualy'));
				break;
				case 4: $periodicidade_id = ucfirst($this->lang->line('annualy'));
				break;
				default: $periodicidade_id = '';
			}
			
			switch($row->tipoindicador_id){
				case 1: $tipoindicador_id = ucfirst($this->lang->line('effectiveness'));
				break;
				case 2: $tipoindicador_id = ucfirst($this->lang->line('efficiency'));
				break;
				case 3: $tipoindicador_id = ucfirst($this->lang->line('effect'));
				break;
				case 4: $tipoindicador_id = ucfirst($this->lang->line('economy'));
				break;
				case 5: $tipoindicador_id = ucfirst($this->lang->line('execution'));
				break;
				default: $tipoindicador_id = '';
			}
			
			$this->load->model('Setores_model');
			if(isset($row->gerset->setor_id)) {
				$row2 = $this->Setores_model->get($row->gerset->setor_id);
				$gerset = $row2->descricao;
			} else {
				$gerset = '';
			}
				
            $data = array(
				'action' => site_url('registros/read/' . $id),
                'IdIndicador' => $row->IdIndicador,
                'descricao' => $row->descricao,
				'formula' => $row->formula,
				'objetivo' => $row->objetivo,
				'origem_dados' => $row->origem_dados,
				'vantagem_sefa' => $row->vantagem_sefa,
				'vantagem_om' => $row->vantagem_om,
				'gerset' => $gerset,
				'periodicidade' => $periodicidade_id,
				'tipoindicador' => $tipoindicador_id,
				'meta' => $row->meta,
				'meta2' => $row->meta2,
				'unidade_meta' => $row->unidade_meta,
				'projeto' => $row->projeto->titulo,
                'situacao' => $situacao,
            );		
			
			if (!$mes)
        	{
            	$data['graph'] = $this->Indicadores_model->getGraph($id);
        	} else
        	{
            	$data['graph'] = $this->Indicadores_model->getGraph($id,
                $filter);
        	}	
			
            $data['view'] = 'registros/registros_read';
            $this->load->view('tema/topo', $data, false);
        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('registros'));
        }
    }

}

/* End of file Registros.php */
/* Location: ./application/controllers/Registros.php */
