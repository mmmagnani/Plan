<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */
	 
class Empenhos extends CI_Controller
{
	 
    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Empenhos_model');
        $this->load->library('form_validation');
		$this->load->helper('formater');
    }

    public function datatable($id=NULL)
    {

        $this->load->model('Empenhos_model');
        $result_data = $this->Empenhos_model->get_datatables($id);
        $data = array();
        foreach ($result_data as $row) {
            $line = array();
			if ($this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) {
              $line[] = '<input type="checkbox" class="remove" name="item_id[]" value="'.$row->IdExecucao.'">';
			} else {
			  $line[] = "";
			}
            $line[] = $row->empenho;
            $line[] = date('d/m/Y', strtotime($row->data_empenho));
            $line[] = number_format($row->valor_empenho, 2, ',', '.');

			if ($this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) {
            	$line[] = '<a href="' . site_url('empenhos/update/' . $row->IdExecucao) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>
                       <a href="' . site_url('empenhos/delete/' . $row->IdExecucao) . '" class="btn btn-danger delete" title="' . $this->lang->line('app_delete') . '"><i class="fa fa-window-close"></i></a>';
			} else {
				$line[] = '';
			}
            $data[] = $line;
        }
		
        $output = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $this->Empenhos_model->get_all_data($id),
            'recordsFiltered' => $this->Empenhos_model->get_filtered_data($id),
            'data' => $data,
        );
        echo json_encode($output);
    }


    public function create($id)
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('commitments'));
            redirect(base_url());
        }
			
        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('empenhos/create_action'),
			'IdExecucao' => set_value('IdExecucao'),
			'om_id' => set_value('om_id'),
			'tarefa_id' => $id,
			'empenho' => set_value('empenho'),
            'data_empenho' => set_value('data_empenho'),
            'valor_empenho' => set_value('valor_empenho'),
        );

        $data['view'] = 'empenhos/empenhos_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('commitments'));
            redirect(base_url());
        }

        	$this->_rules();

        	if ($this->form_validation->run() == false) {
            	$this->create();
        	} else {
				$id = $this->input->post('tarefa_id');
				$data_empenho = $this->input->post('data_empenho');
				if(!empty($this->input->post('data_empenho'))) {
					try
            		{
                		$data_empenho = explode('/', $data_empenho);
                		$data_empenho = $data_empenho[2] . '-' . $data_empenho[1] . '-' . $data_empenho[0];

            		}
            		catch (exception $e)
            		{
                		$data_empenho = date('Y-m-d');
            		}		
				} else {
					$data_empenho = NULL;
				}
            	$data = array(
                	'IdExecucao' => $this->input->post('IdExecucao', true),
					'om_id' => $this->input->post('om_id', true),
                	'tarefa_id' => $this->input->post('tarefa_id', true),
					'empenho' => $this->input->post('empenho', true),
            		'data_empenho' => $data_empenho,
            		'valor_empenho' => str_replace(',','.', str_replace('.','',$this->input->post('valor_empenho', true))),	
            	);

            	$this->Empenhos_model->insert($data);
				$this->load->model('Tarefas_model');
				$resulta = $this->Tarefas_model->somaEmpenhosByTarefa($this->input->post('tarefa_id'));
                $valor_executado = $resulta->valor_executado;
                $data = array('valor_executado' => $valor_executado);
                $this->Tarefas_model->update($data, $this->input->post('tarefa_id'));
            	$this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            	redirect(site_url('tarefas/read/' . $id));
        	}
    }

    public function update($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('tarefas');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('commitments'));
            redirect(base_url());
        }

        $row = $this->Empenhos_model->get($id);
		
		$data_empenho = $row->data_empenho;
		try
            {

                $data_empenho = explode('-', $data_empenho);
                $data_empenho = $data_empenho[2] . '/' . $data_empenho[1] . '/' . $data_empenho[0];
            }
            catch (exception $e)
            {
                $data_empenho = date('d/m/Y');
            }

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('empenhos/update_action'),
				'IdExecucao' => set_value('IdExecucao', $row->IdExecucao),
				'om_id' => set_value('om_id', $row->om_id),
				'tarefa_id' => set_value('tarefa_id', $row->tarefa_id),
				'empenho' => set_value('empenho', $row->empenho),
            	'data_empenho' => set_value('data_empenho', $data_empenho),
            	'valor_empenho' => set_value('valor_empenho', $row->valor_empenho),
            );
            $data['view'] = 'empenhos/empenhos_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('tarefas'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('commitments'));
            redirect(base_url());
        }
        	$this->_rules();

        	if ($this->form_validation->run() == false) {
            	$this->update($this->input->post('IdExecucao', true));
        	} else {
				$id = $this->input->post('tarefa_id');
				$data_empenho = $this->input->post('data_empenho');
				if(!empty($this->input->post('data_empenho'))) {
					try
            		{
                		$data_empenho = explode('/', $data_empenho);
                		$data_empenho = $data_empenho[2] . '-' . $data_empenho[1] . '-' . $data_empenho[0];

            		}
            		catch (exception $e)
            		{
                		$data_empenho = date('Y-m-d');
            		}		
				} else {
					$data_empenho = NULL;
				}
            	$data = array(
					'om_id' => $this->input->post('om_id', true),
                	'tarefa_id' => $this->input->post('tarefa_id', true),
					'empenho' => $this->input->post('empenho', true),
            		'data_empenho' => $data_empenho,
            		'valor_empenho' => str_replace(',','.', str_replace('.','',$this->input->post('valor_empenho', true))),
            	);

            	$this->Empenhos_model->update($data, $this->input->post('IdExecucao', true));
				$this->load->model('Tarefas_model');
				$resulta = $this->Tarefas_model->somaEmpenhosByTarefa($this->input->post('tarefa_id'));
                $valor_executado = $resulta->valor_executado;
                $data = array('valor_executado' => $valor_executado);
                $this->Tarefas_model->update($data, $this->input->post('tarefa_id'));
            	$this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            	redirect(site_url('tarefas/read/' . $id));
        	}		
    }

    public function delete($IdExecucao)
    {
        if (!is_numeric($IdExecucao)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('tarefas');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_delete') . ' ' . $this->lang->line('commitments'));
            redirect(base_url());
        }

        $row = $this->Empenhos_model->get($IdExecucao);
        $ajax = $this->input->get('ajax');
        

        if ($row) {

			$tarefa_id = $row->tarefa_id;
            if ($this->Empenhos_model->delete($IdExecucao)) {
				$this->load->model('Tarefas_model');
				$resulta = $this->Tarefas_model->somaEmpenhosByTarefa($tarefa_id);
                $valor_executado = $resulta->valor_executado;
                $data = array('valor_executado' => $valor_executado);
                $this->Tarefas_model->update($data, $tarefa_id);
                if ($ajax) {
					$this->session->set_flashdata('success', $this->lang->line('app_delete_message'));
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_delete_message')));die();
                }
				
                $this->session->set_flashdata('success', $this->lang->line('app_delete_message'));
                redirect(site_url('tarefas'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('tarefas'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('tarefas'));
        }

    }

    public function delete_many($IdTarefa)
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_delete') . ' ' . $this->lang->line('commitments'));
            redirect(base_url());
        }

        $items = $this->input->post('item_id[]');

        if ($items) {

            $verify = implode('', $items);
            if (is_numeric($verify)) {

               // $this->Objetivos_model->delete_linked($items);

                $result = $this->Empenhos_model->delete_many($items);
                if ($result) {
					$this->load->model('Tarefas_model');
					$resulta = $this->Tarefas_model->somaEmpenhosByTarefa($IdTarefa);
                	$valor_executado = $resulta->valor_executado;
                	$data = array('valor_executado' => $valor_executado);
                	$this->Tarefas_model->update($data, $IdTarefa);
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
        $this->form_validation->set_rules('empenho', '<b>' . $this->lang->line('commitment') . '</b>', 'trim|required');	
		$this->form_validation->set_rules('data_empenho', '<b>' . $this->lang->line('date_commitment') . '</b>', 'trim|required');
		$this->form_validation->set_rules('valor_empenho', '<b>' . $this->lang->line('commitment_val') . '</b>', 'trim|required');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

    public function pesquisar() {
                
        $termo = $this->input->get('termo');

        $data['empenhos'] = $this->Empenhos_model->pesquisar($termo);

        $data['view'] = 'empenhos/pesquisa';
        $this->load->view('tema/topo', $data);
      
    }

}

/* End of file Empenhos.php */
/* Location: ./application/controllers/Empenhos.php */
