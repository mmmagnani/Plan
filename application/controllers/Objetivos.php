<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */
	 
class Objetivos extends CI_Controller
{
	 
    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Objetivos_model');
        $this->load->library('form_validation');
		$this->load->helper('formater');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'vObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }

        $data['view'] = 'objetivos/objetivos_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'vObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }

        $this->load->model('Objetivos_model');
        $result_data = $this->Objetivos_model->get_datatables();
        $data = array();

        foreach ($result_data as $row) {
            $line = array();
			if ($this->permission->check($this->session->userdata('permissao'), 'dObjetivos')) {
              $line[] = '<input type="checkbox" class="remove" name="item_id[]" value="'.$row->IdObjetivo.'">';
			} else {
			  $line[] = '';
			}
            $line[] = $row->IdObjetivo;
            $line[] = $row->descricao;
            $line[] = $row->perspectiva;
			
			if ($this->permission->check($this->session->userdata('permissao'), 'vObjetivos')) {		
            	$view = '<a href="' . site_url('objetivos/read/' . $row->IdObjetivo) . '" class="btn btn-dark" title="' . $this->lang->line('app_view') . '"><i class="fa fa-eye"></i> </a>';
			} else {
			    $view = '';
			}
			if ($this->permission->check($this->session->userdata('permissao'), 'eObjetivos')) {
                $edit = '<a href="' . site_url('objetivos/update/' . $row->IdObjetivo) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>';
			} else {
				$edit = '';
			}
			if ($this->permission->check($this->session->userdata('permissao'), 'dObjetivos')) {	
                $del = '<a href="' . site_url('objetivos/delete/' . $row->IdObjetivo) . '" class="btn btn-danger delete" title="' . $this->lang->line('app_delete') . '"><i class="fa fa-window-close"></i></a>';
			} else {
				$del = '';
			}
			$line[] = $view . ' ' . $edit . ' ' . $del;

            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $this->Objetivos_model->get_all_data(),
            'recordsFiltered' => $this->Objetivos_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }

    public function read($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('objetivos');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'vObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }

        $row = $this->Objetivos_model->with('perspectiva')->get($id);
        if ($row) {
			if($row->situacao == 1) {
				$situacao = $this->lang->line('app_active');
			} else {
				$situacao = $this->lang->line('app_inactive');
			}
            $data = array(
                'IdObjetivo' => $row->IdObjetivo,
				'om_id' => $row->om_id,
                'descricao' => $row->descricao,
                'perspectiva_id' => $row->perspectiva_id,
                'situacao' => $situacao,
            );
			if(!is_null($row->perspectiva_id)){
				$data['perspectiva'] = $row->perspectiva->nome;
			} else {
				$data['perspectiva'] = "";
			}			
			
            $data['view'] = 'objetivos/objetivos_read';
            $this->load->view('tema/topo', $data, false);
        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('objetivos'));
        }
    }

    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'aObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }
		
		$this->load->model('Perspectiva_model');
        $perspectivas = $this->Perspectiva_model->as_dropdown('nome')->get_all();
		$perspectivas[0] = '';
		asort($perspectivas);
			

        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('objetivos/create_action'),
			'IdObjetivo' => set_value('IdObjetivo'),
			'om_id' => set_value('om_id'),
            'descricao' => set_value('descricao'),
            'perspectiva_id' => set_value('perspectiva_id'),
			'perspectivas' => $perspectivas,
            'situacao' => set_value('1'),
        );

        $data['view'] = 'objetivos/objetivos_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'aObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }

        	$this->_rules();

        	if ($this->form_validation->run() == false) {
            	$this->create();
        	} else {
			
            	$data = array(
                	'IdObjetivo' => $this->input->post('IdObjetivo', true),
					'om_id' => $this->input->post('om_id', true),
                	'descricao' => $this->input->post('descricao', true),
                	'perspectiva_id' => $this->input->post('perspectiva_id', true),
                	'situacao' => $this->input->post('situacao', true),			
            	);

            	$this->Objetivos_model->insert($data);
            	$this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            	redirect(site_url('objetivos'));
        	}
    }

    public function update($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('objetivos');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'eObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }
		
		$this->load->model('Perspectiva_model');
        $perspectivas = $this->Perspectiva_model->as_dropdown('nome')->get_all();
		$perspectivas[0] = '';
		asort($perspectivas);	

        $row = $this->Objetivos_model->get($id);

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('objetivos/update_action'),
				'IdObjetivo' => set_value('IdObjetivo', $row->IdObjetivo),
				'om_id' => set_value('om_id', $row->om_id),
           	 	'descricao' => set_value('descricao', $row->descricao),
            	'perspectiva_id' => set_value('perspectiva_id', $row->perspectiva_id),
				'perspectivas' => $perspectivas,
            	'situacao' => set_value('situacao', $row->situacao),
            );
            $data['view'] = 'objetivos/objetivos_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('objetivos'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'eObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }
        	$this->_rules();

        	if ($this->form_validation->run() == false) {
            	$this->update($this->input->post('IdObjetivo', true));
        	} else {
            	$data = array(
					'om_id' => $this->input->post('om_id', true),
                	'descricao' => $this->input->post('descricao', true),
                	'perspectiva_id' => $this->input->post('perspectiva_id', true),
                	'situacao' => $this->input->post('situacao', true) ? 1 : 0,
            	);

            	$this->Objetivos_model->update($data, $this->input->post('IdObjetivo', true));
            	$this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            	redirect(site_url('objetivos'));
        	}		
    }

    public function delete($IdObjetivo)
    {
        if (!is_numeric($IdObjetivo)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('objetivos');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'dObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_delete') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }
		$data = array('situacao' => 0);

        $row = $this->Objetivos_model->get($IdObjetivo);
        $ajax = $this->input->get('ajax');
        

        if ($row) {


            if ($this->Objetivos_model->update($data, $IdObjetivo)) {

                if ($ajax) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_delete_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_delete_message'));
                redirect(site_url('objetivos'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('objetivos'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('objetivos'));
        }

    }

    public function delete_many()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'dObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_delete') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }

        $items = $this->input->post('item_id[]');

        if ($items) {

            $verify = implode('', $items);
            if (is_numeric($verify)) {

               // $this->Objetivos_model->delete_linked($items);

                $result = $this->Objetivos_model->delete_many($items);
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

    public function _rules()
    {
        $this->form_validation->set_rules('descricao', '<b>' . $this->lang->line('description') . '</b>', 'trim|required');	
        $this->form_validation->set_rules('perspectiva_id', '<b>' . $this->lang->line('perspective') . '</b>', 'trim|required');		
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

    public function pesquisar() {
        if (!$this->permission->check($this->session->userdata('permissao'), 'vObjetivos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('targets'));
            redirect(base_url());
        }
        
        $termo = $this->input->get('termo');

        $data['objetivos'] = $this->Objetivos_model->pesquisar($termo);

        $data['view'] = 'objetivos/pesquisa';
        $this->load->view('tema/topo', $data);
      
    }

}

/* End of file Objetivos.php */
/* Location: ./application/controllers/Objetivos.php */
