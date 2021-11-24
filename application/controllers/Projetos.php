<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */
	 
class Projetos extends CI_Controller
{
	 
    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Projetos_model');
        $this->load->library('form_validation');
		$this->load->helper('formater');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'vProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }

        $data['view'] = 'projetos/projetos_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable($id=NULL)
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'vProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }

        $this->load->model('Projetos_model');
        $result_data = $this->Projetos_model->get_datatables($id);
        $data = array();

        foreach ($result_data as $row) {
            $line = array();
			if ($this->permission->check($this->session->userdata('permissao'), 'dProjetos')) {
              $line[] = '<input type="checkbox" class="remove" name="item_id[]" value="'.$row->IdProjeto.'">';
			} else {
			  $line[] = "";
			}
            $line[] = $row->IdProjeto;
            $line[] = $row->titulo;
            $line[] = $row->descricao;
			$line[] = number_format($row->valor_projeto, 2, ',', '.');
			$line[] = '<b>' . 'Custeio - ' . number_format($row->valor_aut_projeto, 2, ',', '.') . '<br />' . 'Investimento - ' . number_format($row->valor_aut_projetop, 2, ',', '.') . '</b>';
			
			if ($this->permission->check($this->session->userdata('permissao'), 'vProjetos')) {		
            	$view = '<a href="' . site_url('projetos/read/' . $row->IdProjeto) . '" class="btn btn-dark" title="' . $this->lang->line('app_view') . '"><i class="fa fa-eye"></i> </a>';
			} else {
			    $view = '';
			}
			if ($this->permission->check($this->session->userdata('permissao'), 'eProjetos')) {
                $edit = '<a href="' . site_url('projetos/update/' . $row->IdProjeto) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>';
			} else {
				$edit = '';
			}
			if ($this->permission->check($this->session->userdata('permissao'), 'dProjetos')) {	
                $del = '<a href="' . site_url('projetos/delete/' . $row->IdProjeto) . '" class="btn btn-danger delete" title="' . $this->lang->line('app_delete') . '"><i class="fa fa-window-close"></i></a>';
			} else {
				$del = '';
			}
			
			$line[] = $view . ' ' . $edit . ' ' . $del;
			
            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $this->Projetos_model->get_all_data($id),
            'recordsFiltered' => $this->Projetos_model->get_filtered_data($id),
            'data' => $data,
        );
        echo json_encode($output);
    }

    public function read($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('projetos');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'vProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }

        $row = $this->Projetos_model->with('gersets')->get($id);
		$setorid = $row->gersets->setor_id;
		$this->load->model('Setores_model');
		$row2 = $this->Setores_model->where('IdSetor','=', $setorid)->get();
        if ($row) {
			if($row->situacao == 1) {
				$situacao = $this->lang->line('app_active');
			} else {
				$situacao = $this->lang->line('app_inactive');
			}
            $data = array(
                'IdProjeto' => $row->IdProjeto,
				'om_id' => $row->om_id,
				'objetivo_id' => $row->objetivo_id,
				'titulo' => $row->titulo,
                'descricao' => $row->descricao,
                'situacao' => $situacao,
            );
			if(!is_null($row->gerset_id)){
				$data['gerset'] = $row2->sigla;
			} else {
				$data['gerset'] = "";
			}			
			
            $data['view'] = 'projetos/projetos_read';
            $this->load->view('tema/topo', $data, false);
        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('projetos'));
        }
    }

    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'aProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }
		
		$this->load->model('Gersets_model');
        $gersets = $this->Gersets_model->as_dropdown('descricao')->get_all_by_om();
		$gersets[0] = '';
		asort($gersets);
		
		$this->load->model('Objetivos_model');
		$objetivos = $this->Objetivos_model->as_dropdown('descricao')->get_all_by_om();
		$objetivos[0] = '';
		asort($objetivos);
			
        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('projetos/create_action'),
			'IdProjeto' => set_value('IdProjeto'),
			'om_id' => set_value('om_id'),
			'objetivo_id' => set_value('objetivo_id'),
			'objetivos' => $objetivos,
			'titulo' => set_value('titulo'),
            'descricao' => set_value('descricao'),
            'gerset_id' => set_value('gerset_id'),
			'gersets' => $gersets,
			'abrangencia' => set_value('abrangencia'),
			'calendario' => set_value('calendario'),
			'obras' => set_value('obras'),
            'situacao' => set_value('situacao'),
        );

        $data['view'] = 'projetos/projetos_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'aProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }

        	$this->_rules();

        	if ($this->form_validation->run() == false) {
            	$this->create();
        	} else {
			
            	$data = array(
                	'IdProjeto' => $this->input->post('IdProjeto', true),
					'om_id' => $this->input->post('om_id', true),
                	'objetivo_id' => $this->input->post('objetivo_id', true),
					'titulo' => $this->input->post('titulo', true),
            		'descricao' => $this->input->post('descricao', true),
            		'gerset_id' => $this->input->post('gerset_id', true),
					'abrangencia' => $this->input->post('abrangencia', true),
					'calendario' => $this->input->post('calendario', true),
					'obras' => $this->input->post('obras', true),
            		'situacao' => $this->input->post('situacao', true),		
            	);

            	$this->Projetos_model->insert($data);
            	$this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            	redirect(site_url('projetos'));
        	}
    }

    public function update($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('projetos');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'eProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }
		
		$this->load->model('Gersets_model');
        $gersets = $this->Gersets_model->as_dropdown('descricao')->get_all_by_om();
		$gersets[0] = '';
		asort($gersets);
		
		$this->load->model('Objetivos_model');
		$objetivos = $this->Objetivos_model->as_dropdown('descricao')->get_all_by_om();
		$objetivos[0] = '';
		asort($objetivos);

        $row = $this->Projetos_model->get($id);

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('projetos/update_action'),
				'IdProjeto' => set_value('IdProjeto', $row->IdProjeto),
				'om_id' => set_value('om_id', $row->om_id),
				'objetivo_id' => set_value('objetivo_id', $row->objetivo_id),
				'objetivos' => $objetivos,
				'titulo' => set_value('titulo', $row->titulo),
            	'descricao' => set_value('descricao', $row->descricao),
            	'gerset_id' => set_value('gerset_id', $row->gerset_id),
				'gersets' => $gersets,
				'abrangencia' => set_value('abrangencia', $row->abrangencia),
				'calendario' => set_value('calendario', $row->calendario),
				'obras' => set_value('obras', $row->obras),
            	'situacao' => set_value('situacao', $row->situacao),
            );
            $data['view'] = 'projetos/projetos_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('projetos'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'eProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }
        	$this->_rules();

        	if ($this->form_validation->run() == false) {
            	$this->update($this->input->post('IdProjeto', true));
        	} else {
            	$data = array(
					'om_id' => $this->input->post('om_id', true),
                	'objetivo_id' => $this->input->post('objetivo_id', true),
					'titulo' => $this->input->post('titulo', true),
            		'descricao' => $this->input->post('descricao', true),
            		'gerset_id' => $this->input->post('gerset_id', true),
					'abrangencia' => $this->input->post('abrangencia', true),
					'calendario' => $this->input->post('calendario', true),
					'obras' => $this->input->post('obras', true),
            		'situacao' => $this->input->post('situacao', true),
            	);

            	$this->Projetos_model->update($data, $this->input->post('IdProjeto', true));
            	$this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            	redirect(site_url('projetos'));
        	}		
    }

    public function delete($IdProjeto)
    {
        if (!is_numeric($IdProjeto)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('projetos');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'dProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_delete') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }
		$data = array('situacao' => 0);

        $row = $this->Projetos_model->get($IdProjeto);
        $ajax = $this->input->get('ajax');
        

        if ($row) {


            if ($this->Projetos_model->update($data, $IdProjeto)) {

                if ($ajax) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_delete_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_delete_message'));
                redirect(site_url('projetos'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('projetos'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('projetos'));
        }

    }

    public function delete_many()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'dProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_delete') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }

        $items = $this->input->post('item_id[]');

        if ($items) {

            $verify = implode('', $items);
            if (is_numeric($verify)) {

               // $this->Objetivos_model->delete_linked($items);

                $result = $this->Projetos_model->delete_many($items);
                if ($result) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_delete_message_many')));die();
                } else {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

            } else {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_data_not_supported')));die();
            }
        }

        echo json_encode(array('result' => false, 'message' => $this->lang->line('app_empty_data')));die();

    }
	
	
	function check_objetivo()
 	{
     	if ($this->input->post('objetivo_id')>0)
 		{
 			return TRUE;
 		}
 		else
 		{
 			$error = 'Escolha o <b>objetivo</b> do projeto.';
 			$this->form_validation->set_message('check_objetivo', $error);
 			return FALSE;
 		}
 	}

    public function _rules()
    {
        $this->form_validation->set_rules('descricao', '<b>' . $this->lang->line('description') . '</b>', 'trim|required');	
		$this->form_validation->set_rules('titulo', '<b>' . $this->lang->line('title') . '</b>', 'trim|required');
		$this->form_validation->set_rules('abrangencia', '<b>' . $this->lang->line('scope') . '</b>', 'trim|required');
		$this->form_validation->set_rules('situacao', '<b>' . $this->lang->line('situation') . '</b>', 'trim|required');
        $this->form_validation->set_rules('objetivo_id', '', 'callback_check_objetivo');		
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

    public function pesquisar() {
        if (!$this->permission->check($this->session->userdata('permissao'), 'vProjetos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('projects'));
            redirect(base_url());
        }
        
        $termo = $this->input->get('termo');

        $data['projetos'] = $this->Projetos_model->pesquisar($termo);

        $data['view'] = 'projetos/pesquisa';
        $this->load->view('tema/topo', $data);
      
    }

}

/* End of file Projetos.php */
/* Location: ./application/controllers/Projetos.php */
