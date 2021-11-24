<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

class Amparos extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('fedd/login');
        }

        $this->load->model('Amparo_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cAmparos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('amparos'));
            redirect(base_url());
        }

        $data['view'] = 'amparo/amparo_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'cAmparos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('amparos'));
            redirect(base_url());
        }

        $this->load->model('Amparo_model');
        $result_data = $this->Amparo_model->get_datatables();
        $data = array();

        foreach ($result_data as $row) {
            $line = array();

            $line[] = $row->id_amparo;
            $line[] = $row->desc_amparo;

            $line[] = '<a href="' . site_url('amparos/update/' . $row->id_amparo) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>';
            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $this->Amparo_model->get_all_data(),
            'recordsFiltered' => $this->Amparo_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }


    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cAmparos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('amparos'));
            redirect(base_url());
        }

        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('amparos/create_action'),
            'id_amparo' => set_value('id_amparo'),
            'desc_amparo' => set_value('desc_amparo'),
        );

        $data['view'] = 'amparo/amparo_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cAmparos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('amparos'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->create();
        } else {

            $data = array(
                'desc_amparo' => $this->input->post('desc_amparo', true),
            );

            $this->Amparo_model->insert($data);
            $this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            redirect(site_url('amparos'));
        }
    }

    public function update($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('amparos');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cAmparos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('amparos'));
            redirect(base_url());
        }

        $row = $this->Amparo_model->get($id);

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('amparos/update_action'),
                'id_amparo' => set_value('id_amparo', $row->id_amparo),
                'desc_amparo' => set_value('desc_amparo', $row->desc_amparo),
            );
            $data['view'] = 'amparo/amparo_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('amparos'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cAmparos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('amparos'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->update($this->input->post('id_amparo', true));
        } else {

            $data = array(
                'desc_amparo' => $this->input->post('desc_amparo', true),
            );

            $this->Amparo_model->update($data, $this->input->post('id_amparo', true));
            $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            redirect(site_url('amparos'));
        }
    }

    public function status($id_amparo)
    {
        if (!is_numeric($id_amparo)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('amparos');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cAmparos')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('amparos'));
            redirect(base_url());
        }

        $row = $this->Amparo_model->get($id_amparo);
        $ajax = $this->input->get('ajax');

        if ($row) {
            if ($this->Amparo_model->update(array('ativo' => !$row->ativo), $id_amparo)) {

                if ($ajax) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_edit_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
                redirect(site_url('amparos'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('amparos'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('amparos'));
        }

    }


    public function _rules()
    {
        $this->form_validation->set_rules('desc_amparo', '<b>' . $this->lang->line('codigo_comp') . '</b>', 'trim|required');

        $this->form_validation->set_rules('id_amparo', 'id_amparo', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Amparos.php */
/* Location: ./application/controllers/Amparos.php */
