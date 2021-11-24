<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

class Sistema extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Sistema_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('system'));
            redirect(base_url());
        }

        $data['view'] = 'sistema/sistema_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('system'));
            redirect(base_url());
        }

        $this->load->model('Sistema_model');
        $result_data = $this->Sistema_model->get_datatables();
        $data = array();

        foreach ($result_data as $row) {
            $line = array();

            $line[] = $row->IdConfig;
			$line[] = $row->sigla;
            $line[] = $row->margem_reserva;
			$line[] = $row->bloqueio ? $this->lang->line('app_yes') : $this->lang->line('app_no');

            $line[] = '<a href="' . site_url('sistema/update/' . $row->IdConfig) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>';
 
            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $this->Sistema_model->get_all_data(),
            'recordsFiltered' => $this->Sistema_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }


    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('system'));
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
            'action' => site_url('sistema/create_action'),
            'IdConfig' => set_value('IdConfig'),
			'om_id' => set_value('om_id'),
			'oms' => $oms,
            'margem_reserva' => set_value('margem_reserva'),
            'bloqueio' => set_value('bloqueio'),
        );

        $data['view'] = 'sistema/sistema_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('system'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->create();
        } else {
		 if($this->session->userdata('om_id') != 1) {
		   $om = $this->session->userdata('om_id');
		 } else {
		   $om = $this->input->post('om_id');
		 }

            $data = array(
                'margem_reserva' => $this->input->post('margem_reserva', true),
                'om_id' => $om,
                'bloqueio' => $this->input->post('bloqueio', true),
            );

            $this->Sistema_model->insert($data);
            $this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            redirect(site_url('sistema'));
        }
    }

    public function update($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('sistema');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('sistema'));
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


        $row = $this->Sistema_model->get($id);

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('sistema/update_action'),
                'IdConfig' => set_value('IdConfig', $row->IdConfig),
                'om_id' => set_value('om_id', $row->om_id),
				'oms' => $oms,
                'margem_reserva' => set_value('margem_reserva', $row->margem_reserva),
                'bloqueio' => set_value('bloqueio', $row->bloqueio),
            );
            $data['view'] = 'sistema/sistema_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('sistema'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('system'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->update($this->input->post('IdConfig', true));
        } else {

            $data = array(
                'om_id' => $this->input->post('om_id', true),
                'margem_reserva' => $this->input->post('margem_reserva', true),
                'bloqueio' => $this->input->post('bloqueio', true),
            );

            $this->Sistema_model->update($data, $this->input->post('IdConfig', true));
            $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            redirect(site_url('sistema'));
        }
    }


    public function _rules()
    {
		$this->form_validation->set_rules('margem_reserva', '<b>' . $this->lang->line('reserve_margin_error') . '</b>', 'trim|required');
        $this->form_validation->set_rules('bloqueio', '<b>' . $this->lang->line('block_error') . '</b>', 'trim|required');

        $this->form_validation->set_rules('IdConfig', 'IdConfig', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Sistema.php */
/* Location: ./application/controllers/Sistema.php */
