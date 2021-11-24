<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

class Setores extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Setores_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cSetores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('sectors'));
            redirect(base_url());
        }

        $data['view'] = 'setores/setores_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'cSetores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('sectors'));
            redirect(base_url());
        }

        $this->load->model('Setores_model');
		$result_data = $this->Setores_model->get_datatables();
				
        $data = array();

        foreach ($result_data as $row) {
            $line = array();

            $line[] = $row->sigla;
			$line[] = $row->descricao;
			$line[] = $row->situacao ? $this->lang->line('app_active') : $this->lang->line('app_inactive');
			
			$color = $row->situacao ? 'btn-danger' : 'btn-success';
            $icon = $row->situacao ? 'fa fa-window-close' : 'fa fa-check';
            $title = $row->situacao ? $this->lang->line('app_disable') : $this->lang->line('app_activate');
			
            $line[] = '<a href="' . site_url('setores/update/' . $row->IdSetor) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>
					  <a href="'.site_url('setores/status/' . $row->IdSetor) . '" class="btn '.$color.' delete" title="' . $title . '"><i class="'.$icon.'"></i></a>';
            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
			'recordsTotal' => $this->Setores_model->get_all_data(),
            'recordsFiltered' => $this->Setores_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }


    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cSetores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('sectors'));
            redirect(base_url());
        }
		if ($this->session->userdata('om_id') == 1)
		{
			$this->load->model('Om_model');
			$oms = $this->Om_model->where('situacao', 1)->as_dropdown('sigla')->get_all();
			$oms[0] = '';
			asort($oms);
		} else {
			$oms = $this->session->userdata('om_id');
		}

        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('setores/create_action'),
            'IdSetor' => set_value('IdSetor'),
            'sigla' => set_value('sigla'),
			'descricao' => set_value('descricao'),
			'situacao' => set_value('situacao'),
			'om_id' => set_value('om_id'),
			'oms' => $oms,
        );

        $data['view'] = 'setores/setores_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cSetores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('sectors'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->create();
        } else {

            $data = array(
				'om_id' => $this->input->post('om_id', true),
                'sigla' => $this->input->post('sigla', true),
				'descricao' => $this->input->post('descricao', true),
				'situacao' => $this->input->post('situacao', true),
            );

            $this->Setores_model->insert($data);
            $this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            redirect(site_url('setores'));
        }
    }

    public function update($IdSetor)
    {
        if (!is_numeric($IdSetor)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('setores');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cSetores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('sectors'));
            redirect(base_url());
        }

        if ($this->session->userdata('om_id') == 1)
		{
			$this->load->model('Om_model');
			$oms = $this->Om_model->where('situacao', 1)->as_dropdown('sigla')->get_all();
			$oms[0] = '';
			asort($oms);
		} else {
			$oms = $this->session->userdata('om_id');
		}
		
		$row = $this->Setores_model->get($IdSetor);

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('setores/update_action'),
                'IdSetor' => set_value('IdSetor', $row->IdSetor),
                'sigla' => set_value('sigla', $row->sigla),
				'descricao' => set_value('descricao', $row->descricao),
				'situacao' => set_value('situacao', $row->situacao),
				'om_id' => set_value('om_id', $row->om_id),
				'oms' => $oms,
            );
            $data['view'] = 'setores/setores_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('setores'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cSetores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('sectors'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->update($this->input->post('IdSetor', true));
        } else {
			
            $data = array(
				'sigla' => $this->input->post('sigla', true),
				'descricao' => $this->input->post('descricao', true),
				'situacao' => $this->input->post('situacao', true),
				'om_id' => $this->input->post('om_id', true),
            );

            $this->Setores_model->update($data, $this->input->post('IdSetor', true));
            $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            redirect(site_url('setores'));
        }
    }

    public function status($IdSetor)
    {
        if (!is_numeric($IdSetor)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('setores');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cSetores')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('sectors'));
            redirect(base_url());
        }

        $row = $this->Setores_model->get($IdSetor);
        $ajax = $this->input->get('ajax');

        if ($row) {
            if ($this->Setores_model->update(array('situacao' => !$row->situacao), $IdSetor)) {

                if ($ajax) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_edit_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
                redirect(site_url('setores'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('setores'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('setores'));
        }

    }


    public function _rules()
    {
        $this->form_validation->set_rules('sigla', '<b>' . $this->lang->line('sector_sigla') . '</b>', 'trim|required');
		$this->form_validation->set_rules('descricao', '<b>' . $this->lang->line('description') . '</b>', 'trim|required');

        $this->form_validation->set_rules('IdSetor', 'IdSetor', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Setores.php */
/* Location: ./application/controllers/Setores.php */
