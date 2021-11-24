<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

class Gersets extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Gersets_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('gerset'));
            redirect(base_url());
        }

        $data['view'] = 'gersets/gersets_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'cGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('gerset'));
            redirect(base_url());
        }

        $this->load->model('Gersets_model');
		$result_data = $this->Gersets_model->get_datatables();
				
        $data = array();

        foreach ($result_data as $row) {
            $line = array();

            $line[] = $row->sigla;
			$line[] = $row->descricao;
			$line[] = $row->situacao ? $this->lang->line('app_active') : $this->lang->line('app_inactive');
			
			$color = $row->situacao ? 'btn-danger' : 'btn-success';
            $icon = $row->situacao ? 'fa fa-window-close' : 'fa fa-check';
            $title = $row->situacao ? $this->lang->line('app_disable') : $this->lang->line('app_activate');
			
            $line[] = '<a href="' . site_url('gersets/update/' . $row->IdGerset) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>
					  <a href="'.site_url('gersets/status/' . $row->IdGerset) . '" class="btn '.$color.' delete" title="' . $title . '"><i class="'.$icon.'"></i></a>';
            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
			'recordsTotal' => $this->Gersets_model->get_all_data(),
            'recordsFiltered' => $this->Gersets_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }


    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('gerset'));
            redirect(base_url());
        }
			$this->load->model('Setores_model');
			$setores = $this->Setores_model->where('situacao', 1)->where('om_id', $this->session->userdata('om_id'))->as_dropdown('descricao')->get_all();
			$setores[0] = '';
			asort($setores);

        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('gersets/create_action'),
            'IdGerset' => set_value('IdGerset'),
			'situacao' => set_value('situacao'),
			'om_id' => set_value('om_id'),
			'setor_id' => set_value('setor_id'),
			'setores' => $setores,
        );

        $data['view'] = 'gersets/gersets_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('sectors'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->create();
        } else {

            $data = array(
				'om_id' => $this->input->post('om_id', true),
                'setor_id' => $this->input->post('setor_id', true),
				'situacao' => $this->input->post('situacao', true),
            );

            $this->Gersets_model->insert($data);
            $this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            redirect(site_url('gersets'));
        }
    }

    public function update($IdGerset)
    {
        if (!is_numeric($IdGerset)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('gersets');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('gerset'));
            redirect(base_url());
        }

        $this->load->model('Setores_model');
		$setores = $this->Setores_model->where('situacao', 1)->where('om_id', $this->session->userdata('om_id'))->as_dropdown('descricao')->get_all();
		$setores[0] = '';
		asort($setores);
		
		$row = $this->Gersets_model->get($IdGerset);

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('gersets/update_action'),
                'IdGerset' => set_value('IdGerset', $row->IdGerset),
                'setor_id' => set_value('setor_id', $row->setor_id),
				'setores' => $setores,
				'situacao' => set_value('situacao', $row->situacao),
				'om_id' => set_value('om_id', $row->om_id),
            );
            $data['view'] = 'gersets/gersets_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('gersets'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('gerset'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->update($this->input->post('IdGerset', true));
        } else {
			
            $data = array(
				'setor_id' => $this->input->post('setor_id', true),
				'situacao' => $this->input->post('situacao', true),
				'om_id' => $this->input->post('om_id', true),
            );

            $this->Gersets_model->update($data, $this->input->post('IdGerset', true));
            $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            redirect(site_url('gersets'));
        }
    }

    public function status($IdGerset)
    {
        if (!is_numeric($IdGerset)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('gerset');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cGerset')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('gerset'));
            redirect(base_url());
        }

        $row = $this->Gersets_model->get($IdGerset);
        $ajax = $this->input->get('ajax');

        if ($row) {
            if ($this->Gersets_model->update(array('situacao' => !$row->situacao), $IdGerset)) {

                if ($ajax) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_edit_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
                redirect(site_url('gersets'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('gersets'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('gersets'));
        }

    }


    public function _rules()
    {
        $this->form_validation->set_rules('setor_id', '<b>' . $this->lang->line('sector_sigla') . '</b>', 'trim|required');

        $this->form_validation->set_rules('IdGerset', 'IdGerset', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Gersets.php */
/* Location: ./application/controllers/Gersets.php */
