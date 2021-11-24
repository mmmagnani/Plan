<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

class Medicoes extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Medicoes_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('records'));
            redirect(base_url());
        }
		$data['id'] = $this->input->get('id');

        $data['view'] = 'medicoes/medicoes_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable($id)
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('records'));
            redirect(base_url());
        }

        $this->load->model('Medicoes_model');
		$result_data = $this->Medicoes_model->get_datatables($id);
				
        $data = array();

        foreach ($result_data as $row) {
            $line = array();
			$line[] = '<input type="checkbox" class="remove" name="item_id[]" value="'.$row->IdRegistro . '">';
            $line[] = date('d/m/Y',strtotime($row->data));
			$line[] = $row->observacao;
			$line[] = number_format($row->medicao,2,',','.');
			
            $line[] = '<a href="' . site_url('medicoes/update/' . $row->IdRegistro) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>
					  <a href="' . site_url('medicoes/delete/' . $row->IdRegistro) . '" class="btn btn-danger delete" title="' . $this->lang->line('app_delete') . '"><i class="fa fa-window-close"></i></a>';
            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
			'recordsTotal' => $this->Medicoes_model->get_all_data($id),
            'recordsFiltered' => $this->Medicoes_model->get_filtered_data($id),
            'data' => $data,
        );
        echo json_encode($output);
    }


    public function create($id)
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('records'));
            redirect(base_url());
        }

        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('medicoes/create_action'),
            'IdRegistro' => set_value('IdRegistro'),
            'observacao' => set_value('observacao'),
			'indicador_id' => $id,
			'medicao' => set_value('medicao'),
			'data' => set_value('data'),
			'ano' => $this->session->userdata('anofiscal'),
        );

        $data['view'] = 'medicoes/medicoes_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('records'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->create();
        } else {
			$data_med = $this->input->post('data');
				if(!empty($this->input->post('data'))) {
					try
            		{
                		$data_med = explode('/', $data_med);
                		$data_med = $data_med[2] . '-' . $data_med[1] . '-' . $data_med[0];

            		}
            		catch (exception $e)
            		{
                		$data_med = date('Y-m-d');
            		}		
				} else {
					$data_med = NULL;
				}

            $data = array(
				'observacao' => $this->input->post('observacao', true),
                'indicador_id' => $this->input->post('indicador_id', true),
				'medicao' => $this->input->post('medicao', true),
				'data' => $data_med,
				'ano' => $this->input->post('ano', true),
            );

            $this->Medicoes_model->insert($data);
            $this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            redirect(site_url('medicoes?id=').$this->input->post('indicador_id'));
        }
    }

    public function update($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('medicoes?id='. $id) ;
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('records'));
            redirect(base_url());
        }
		
		$row = $this->Medicoes_model->get($id);

        if ($row) {
			$data_med = $row->data;
				if(!empty($data_med)) {
					try
            		{
                		$data_med = explode('-', $data_med);
                		$data_med = $data_med[2] . '/' . $data_med[1] . '/' . $data_med[0];

            		}
            		catch (exception $e)
            		{
                		$data_med = date('d/m/Y');
            		}		
				} else {
					$data_med = NULL;
				}
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
            	'action' => site_url('medicoes/create_action'),
            	'IdRegistro' => set_value('IdRegistro', $row->IdRegistro),
            	'observacao' => set_value('observacao', $row->observacao),
				'indicador_id' => set_value('indicador_id', $row->indicador_id),
				'medicao' => set_value('medicao', $row->medicao),
				'data' => set_value('data', $data_med),
				'ano' => set_value('ano', $row->ano),
            );
            $data['view'] = 'medicoes/medicoes_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('medicoes?id=').$id);
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('records'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->update($this->input->post('IdRegistro', true));
        } else {
			
            $data = array(
				'data' => $this->input->post('data', true),
				'observacao' => $this->input->post('observacao', true),
				'indicador_id' => $this->input->post('indicador_id', true),
				'medicao' => $this->input->post('medicao', true),
				'ano' => $this->input->post('ano', true),
            );

            $this->Setores_model->update($data, $this->input->post('IdRegistro', true));
            $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            redirect(site_url('medicoes?id=').$id);
        }
    }
	
	public function delete($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('medicoes?id='.$id);
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_delete') . ' ' . $this->lang->line('records'));
            redirect(base_url());
        }

        $row = $this->Medicoes_model->get($id);
        $ajax = $this->input->get('ajax');
        

        if ($row) {


            if ($this->Medicoes_model->delete($id)) {

                if ($ajax) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_delete_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_delete_message'));
                redirect(site_url('medicoes?id=').$id);
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('cadastros'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('medicoes?id=').$id);
        }

    }
	
	public function delete_many()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_delete') . ' ' . $this->lang->line('records'));
            redirect(base_url());
        }

        $items = $this->input->post('item_id[]');

        if ($items) {

            $verify = implode('', $items);
            if (is_numeric($verify)) {

                $this->Medicoes_model->delete_linked($items);

                $result = $this->Medicoes_model->delete_many($items);
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
        $this->form_validation->set_rules('data', '<b>' . $this->lang->line('date') . '</b>', 'trim|required');
		$this->form_validation->set_rules('medicao', '<b>' . $this->lang->line('mesasurement') . '</b>', 'trim|required');

        $this->form_validation->set_rules('IdRegistro', 'IdRegistro', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Medicoes.php */
/* Location: ./application/controllers/Medicoes.php */
