<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

class Indicadores extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Indicadores_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ((!$this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) && (!$this->permission->check($this->session->userdata('permissao'), 'vRegistros'))) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('indicators'));
            redirect(base_url());
        } 

        $data['view'] = 'indicadores/indicadores_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

		if ((!$this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) && (!$this->permission->check($this->session->userdata('permissao'), 'vRegistros'))) {	
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('indicators'));
            redirect(base_url());
        } 

        $this->load->model('Indicadores_model');
		$result_data = $this->Indicadores_model->get_datatables();
				
        $data = array();

        foreach ($result_data as $row) {
            $line = array();

            $line[] = $row->descricao;
		if ($this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) {
			$line[] = $row->formula;
		}
			$line[] = $row->objetivo;
		if ($this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) {
			$line[] = $row->situacao ? $this->lang->line('app_active') : $this->lang->line('app_inactive');
			
			$color = $row->situacao ? 'btn-danger' : 'btn-success';
            $icon = $row->situacao ? 'fa fa-window-close' : 'fa fa-check';
            $title = $row->situacao ? $this->lang->line('app_disable') : $this->lang->line('app_activate');
		}
		if (($this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) && ($this->permission->check($this->session->userdata('permissao'), 'vRegistros'))) {
			$line[] = '<a href="' . site_url('indicadores/read/' . $row->IdIndicador) . '" class="btn btn-dark" title="' . $this->lang->line('app_view') . '"><i class="fa fa-eye"></i></a>
					   <a href="' . site_url('indicadores/update/' . $row->IdIndicador) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>
					   <a href="'.site_url('indicadores/status/' . $row->IdIndicador) . '" class="btn '.$color.' delete" title="' . $title . '"><i class="'.$icon.'"></i></a>';
		}
		else if ($this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) {
			$line[] = '<a href="' . site_url('indicadores/update/' . $row->IdIndicador) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>
					   <a href="'.site_url('indicadores/status/' . $row->IdIndicador) . '" class="btn '.$color.' delete" title="' . $title . '"><i class="'.$icon.'"></i></a>';
		}
		else if ($this->permission->check($this->session->userdata('permissao'), 'vRegistros')) {	
			$line[] = '<a href="' . site_url('indicadores/read/' . $row->IdIndicador) . '" class="btn btn-dark" title="' . $this->lang->line('app_view') . '"><i class="fa fa-eye"></i></a>';
		} else {
			$line [] = '';
		}
            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
			'recordsTotal' => $this->Indicadores_model->get_all_data(),
            'recordsFiltered' => $this->Indicadores_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }
	
	public function read($id)
    {		
		$mes = $this->input->get('mes');

        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('indicadores');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'vRegistros')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('indicators'));
            redirect(base_url());
        }
		
		if ($mes)
        {
            $filter = " AND EXTRACT(MONTH FROM registros.`data`) = " . $mes;
        }


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
				'action' => site_url('indicadores/read/' . $id),
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
			
            $data['view'] = 'indicadores/indicadores_read';
            $this->load->view('tema/topo', $data, false);
        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('indicadores'));
        }
    }


    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('indicators'));
            redirect(base_url());
        }
			$this->load->model('Gersets_model');
			$gersets = $this->Gersets_model->as_dropdown('descricao')->get_all_by_om();
			$gersets[0] = '';
			asort($gersets);
			
			$this->load->model('Projetos_model');
			$projetos = $this->Projetos_model->where('situacao', 1)->where('om_id', $this->session->userdata('om_id'))->as_dropdown('titulo')->get_all();
			$projetos[0] = '';
			asort($projetos);

        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('indicadores/create_action'),
            'IdIndicador' => set_value('IdIndicador'),		
			'descricao' => set_value('descricao'),
			'formula' => set_value('formula'),
			'objetivo' => set_value('objetivo'),
			'origem_dados' => set_value('origem_dados'),
			'vantagem_sefa' => set_value('vantagem_sefa'),
			'vantagem_om' => set_value('vantagem_om'),
			'gerset_id' => set_value('gerset_id'),
			'gersets' => $gersets,
			'periodicidade_id' => set_value('periodicidade_id'),
			'tipoindicador_id' => set_value('tipoindicador_id'),
			'meta' => set_value('meta'),
			'meta2' => set_value('meta2'),
			'unidade_meta' => set_value('unidade_meta'),
			'projeto_id' => set_value('projeto_id'),
			'projetos' => $projetos,
			'situacao' => set_value('situacao'),
			'om_id' => set_value('om_id'),
        );

        $data['view'] = 'indicadores/indicadores_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('sectors'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->create();
        } else {

            $data = array(
				'descricao' => $this->input->post('descricao', true),
				'formula' => $this->input->post('formula', true),
				'objetivo' => $this->input->post('objetivo', true),
				'origem_dados' => $this->input->post('origem_dados', true),
				'vantagem_sefa' => $this->input->post('vantagem_sefa', true),
				'vantagem_om' => $this->input->post('vantagem_om', true),
				'gerset_id' => $this->input->post('gerset_id', true),
				'periodicidade_id' => $this->input->post('periodicidade_id', true),
				'tipoindicador_id' => $this->input->post('tipoindicador_id', true),
				'meta' => $this->input->post('meta', true),
				'meta2' => $this->input->post('meta2', true),
				'unidade_meta' => $this->input->post('unidade_meta', true),
				'projeto_id' => $this->input->post('projeto_id', true),
				'situacao' => $this->input->post('situacao', true),
				'om_id' => $this->input->post('om_id', true),
            );

            $this->Indicadores_model->insert($data);
            $this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            redirect(site_url('indicadores'));
        }
    }

    public function update($IdIndicador)
    {
        if (!is_numeric($IdIndicador)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('indicadores');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('indicators'));
            redirect(base_url());
        }

        $this->load->model('Gersets_model');
		$gersets = $this->Gersets_model->as_dropdown('descricao')->get_all_by_om();
		$gersets[0] = '';
		asort($gersets);
			
		$this->load->model('Projetos_model');
		$projetos = $this->Projetos_model->where('situacao', 1)->where('om_id', $this->session->userdata('om_id'))->as_dropdown('titulo')->get_all();
		$projetos[0] = '';
		asort($projetos);
		
		$row = $this->Indicadores_model->get($IdIndicador);

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('indicadores/update_action'),
                'IdIndicador' => set_value('IdIndicador', $row->IdIndicador),		
				'descricao' => set_value('descricao', $row->descricao),
				'formula' => set_value('formula', $row->formula),
				'objetivo' => set_value('objetivo', $row->objetivo),
				'origem_dados' => set_value('origem_dados', $row->origem_dados),
				'vantagem_sefa' => set_value('vantagem_sefa', $row->vantagem_sefa),
				'vantagem_om' => set_value('vantagem_om', $row->vantagem_om),
				'gerset_id' => set_value('gerset_id', $row->gerset_id),
				'gersets' => $gersets,
				'periodicidade_id' => set_value('periodicidade_id', $row->periodicidade_id),
				'tipoindicador_id' => set_value('tipoindicador_id', $row->tipoindicador_id),
				'meta' => set_value('meta', $row->meta),
				'meta2' => set_value('meta2', $row->meta2),
				'unidade_meta' => set_value('unidade_meta', $row->unidade_meta),
				'projeto_id' => set_value('projeto_id', $row->projeto_id),
				'projetos' => $projetos,
				'situacao' => set_value('situacao', $row->situacao),
				'om_id' => set_value('om_id', $row->om_id),
            );
            $data['view'] = 'indicadores/indicadores_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('indicadores'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('gerset'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->update($this->input->post('IdIndicador', true));
        } else {
			
            $data = array(
				'descricao' => $this->input->post('descricao', true),
				'formula' => $this->input->post('formula', true),
				'objetivo' => $this->input->post('objetivo', true),
				'origem_dados' => $this->input->post('origem_dados', true),
				'vantagem_sefa' => $this->input->post('vantagem_sefa', true),
				'vantagem_om' => $this->input->post('vantagem_om', true),
				'gerset_id' => $this->input->post('gerset_id', true),
				'periodicidade_id' => $this->input->post('periodicidade_id', true),
				'tipoindicador_id' => $this->input->post('tipoindicador_id', true),
				'meta' => $this->input->post('meta', true),
				'meta2' => $this->input->post('meta2', true),
				'unidade_meta' => $this->input->post('unidade_meta', true),
				'projeto_id' => $this->input->post('projeto_id', true),
				'situacao' => $this->input->post('situacao', true),
				'om_id' => $this->input->post('om_id', true),
            );

            $this->Indicadores_model->update($data, $this->input->post('IdIndicador', true));
            $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            redirect(site_url('indicadores'));
        }
    }

    public function status($IdIndicador)
    {
        if (!is_numeric($IdIndicador)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('indicadores');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('indicator'));
            redirect(base_url());
        }

        $row = $this->Indicadores_model->get($IdIndicador);
        $ajax = $this->input->get('ajax');

        if ($row) {
            if ($this->Indicadores_model->update(array('situacao' => !$row->situacao), $IdIndicador)) {

                if ($ajax) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_edit_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
                redirect(site_url('indicadores'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('indicadores'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('indicadores'));
        }

    }


    public function _rules()
    {
        $this->form_validation->set_rules('descricao', '<b>' . $this->lang->line('description') . '</b>', 'trim|required');
		$this->form_validation->set_rules('formula', '<b>' . $this->lang->line('formula') . '</b>', 'trim|required');
		$this->form_validation->set_rules('objetivo', '<b>' . $this->lang->line('indicator_target') . '</b>', 'trim|required');
		$this->form_validation->set_rules('origem_dados', '<b>' . $this->lang->line('data_source') . '</b>', 'trim|required');
		$this->form_validation->set_rules('periodicidade_id', '<b>' . $this->lang->line('frequency') . '</b>', 'trim|required');
		$this->form_validation->set_rules('tipoindicador_id', '<b>' . $this->lang->line('indicator_type') . '</b>', 'trim|required');
		$this->form_validation->set_rules('gerset_id', '<b>' . $this->lang->line('gerset') . '</b>', 'trim|required');
        $this->form_validation->set_rules('IdIndicador', 'IdIndicador', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Indicadores.php */
/* Location: ./application/controllers/Indicadores.php */
