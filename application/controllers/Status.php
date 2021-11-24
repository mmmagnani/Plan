<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

class Status extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Status_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cStatus')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('perm_status'));
            redirect(base_url());
        }

        $data['view'] = 'status/status_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'cStatus')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('perm_status'));
            redirect(base_url());
        }

        $this->load->model('Status_model');
		$result_data = $this->Status_model->get_datatables();
				
        $data = array();

        foreach ($result_data as $row) {
            $line = array();

			$line[] = $row->status_desc;
			$line[] = $row->situacao ? $this->lang->line('app_active') : $this->lang->line('app_inactive');
			
			$color = $row->situacao ? 'btn-danger' : 'btn-success';
            $icon = $row->situacao ? 'fa fa-window-close' : 'fa fa-check';
            $title = $row->situacao ? $this->lang->line('app_disable') : $this->lang->line('app_activate');
			
            $line[] = '<a href="' . site_url('status/update/' . $row->idStatus) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>
					  <a href="'.site_url('status/status/' . $row->idStatus) . '" class="btn '.$color.' delete" title="' . $title . '"><i class="'.$icon.'"></i></a>';
            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
			'recordsTotal' => $this->Status_model->get_all_data(),
            'recordsFiltered' => $this->Status_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }


    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cStatus')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('perm_status'));
            redirect(base_url());
        }
		if ($this->session->userdata('om_id') == 1)
		{
			$this->load->model('Om_model');
			$oms = $this->Om_model->where('situacao', 1)->where('apoiadora', 1)->as_dropdown('sigla')->get_all();
			$oms[0] = '';
			asort($oms);
		} else {
			$oms = $this->session->userdata('om_id');
		}

        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('status/create_action'),
            'idStatus' => set_value('idStatus'),
			'status_desc' => set_value('status_desc'),
			'situacao' => set_value('situacao'),
			'om_id' => set_value('om_id'),
			'oms' => $oms,
        );

        $data['view'] = 'status/status_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cStatus')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('perm_status'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->create();
        } else {

            $data = array(
				'om_id' => $this->input->post('om_id', true),
				'status_desc' => $this->input->post('status_desc', true),
				'situacao' => $this->input->post('situacao', true),
            );

            $this->Status_model->insert($data);
            $this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            redirect(site_url('status'));
        }
    }

    public function update($idStatus)
    {
        if (!is_numeric($idStatus)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('status');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cStatus')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('perm_status'));
            redirect(base_url());
        }

        if ($this->session->userdata('om_id') == 1)
		{
			$this->load->model('Om_model');
			$oms = $this->Om_model->where('situacao', 1)->where('apoiadora', 1)->as_dropdown('sigla')->get_all();
			$oms[0] = '';
			asort($oms);
		} else {
			$oms = $this->session->userdata('om_id');
		}
		
		$row = $this->Status_model->get($idStatus);

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('status/update_action'),
                'idStatus' => set_value('idStatus', $row->idStatus),
				'status_desc' => set_value('status_desc', $row->status_desc),
				'situacao' => set_value('situacao', $row->situacao),
				'om_id' => set_value('om_id', $row->om_id),
				'oms' => $oms,
            );
            $data['view'] = 'status/status_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('status'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cStatus')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('perm_status'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->update($this->input->post('idStatus', true));
        } else {
			
            $data = array(
				'status_desc' => $this->input->post('status_desc', true),
				'situacao' => $this->input->post('situacao', true),
				'om_id' => $this->input->post('om_id', true),
            );

            $this->Status_model->update($data, $this->input->post('idStatus', true));
            $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            redirect(site_url('status'));
        }
    }

    public function status($idStatus)
    {
        if (!is_numeric($idStatus)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('status');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cStatus')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('perm_status'));
            redirect(base_url());
        }

        $row = $this->Status_model->get($idStatus);
        $ajax = $this->input->get('ajax');

        if ($row) {
            if ($this->Status_model->update(array('situacao' => !$row->situacao), $idStatus)) {

                if ($ajax) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_edit_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
                redirect(site_url('status'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('status'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('status'));
        }

    }


    public function _rules()
    {
		$this->form_validation->set_rules('status_desc', '<b>' . $this->lang->line('description') . '</b>', 'trim|required');

        $this->form_validation->set_rules('idStatus', 'idStatus', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Status.php */
/* Location: ./application/controllers/Status.php */
