<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

class Om extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Om_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cOm')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('om'));
            redirect(base_url());
        }

        $data['view'] = 'om/om_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'cOm')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('om'));
            redirect(base_url());
        }

        $this->load->model('Om_model');
        $result_data = $this->Om_model->get_datatables();
        $data = array();

        foreach ($result_data as $row) {
            $line = array();

            $line[] = $row->IdOm;
            $line[] = $row->sigla;
			$line[] = $row->nome;
			$line[] = $row->codigo;
			$line[] = $row->situacao ? $this->lang->line('app_active') : $this->lang->line('app_inactive');
			
			$color = $row->situacao ? 'btn-danger' : 'btn-success';
            $icon = $row->situacao ? 'fa fa-window-close' : 'fa fa-check';
            $title = $row->situacao ? $this->lang->line('app_disable') : $this->lang->line('app_activate');
			if($this->session->userdata('om_id') == 1) {
            	$line[] = '<a href="' . site_url('om/update/' . $row->IdOm) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>				
					  <a href="'.site_url('om/status/' . $row->IdOm) . '" class="btn '.$color.' delete" title="' . $title . '"><i class="'.$icon.'"></i></a>';
			} else {
				$line[] = '<a href="' . site_url('om/update/' . $row->IdOm) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>';
			}
            $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $this->Om_model->get_all_data(),
            'recordsFiltered' => $this->Om_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }


    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cOm')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('om'));
            redirect(base_url());
        }
		$apoiadoras = $this->Om_model->where('apoiadora', 1)->as_dropdown('sigla')->get_all();
		$apoiadoras[0] = '';
		asort($apoiadoras);

        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('ug/create_action'),
            'IdOm' => set_value('IdOm'),
            'sigla' => set_value('sigla'),
			'nome' => set_value('nome'),
			'situacao' => set_value('situacao'),
			'codigo' => set_value('codigo'),
			'apoiadora' => set_value('apoiadora'),
			'om_id_apoiadora' => set_value('om_id_apoaidora'),
			'apoiadoras' => $apoiadoras,
        );

        $data['view'] = 'om/om_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cOm')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('om'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->create();
        } else {
			if(($this->input->post('apoiadora') == 1) || ($this->input->post('apoiadora') == '')){
				$om_apoiadora = NULL;
			} else {
				$om_apoiadora = $this->input->post('om_id_apoiadora', true);
			}

            $data = array(
                'sigla' => $this->input->post('sigla', true),
				'nome' => $this->input->post('nome', true),
				'situacao' => $this->input->post('situacao', true),
				'codigo' => $this->input->post('codigo', true),
				'apoiadora' => $this->input->post('apoiadora', true),
				'om_id_apoaidora' => om_apoiadora,
            );

            $this->Om_model->insert($data);
            $this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            redirect(site_url('om'));
        }
    }

    public function update($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('om');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cOm')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('om'));
            redirect(base_url());
        }

        $row = $this->Om_model->get($id);
		$apoiadoras = $this->Om_model->where('apoiadora', 1)->as_dropdown('sigla')->get_all();
		$apoiadoras[0] = '';
		asort($apoiadoras);

        if ($row) {
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('om/update_action'),
                'IdOm' => set_value('IdOm', $row->IdOm),
                'sigla' => set_value('sigla', $row->sigla),
				'nome' => set_value('nome', $row->nome),
				'situacao' => set_value('situacao', $row->situacao),
				'codigo' => set_value('codigo', $row->codigo),
				'apoiadora' => set_value('apoiadora', $row->apoiadora),
				'om_id_apoiadora' => set_value('om_id_apoiadora', $row->om_id_apoiadora),
				'apoiadoras' => $apoiadoras,
            );
            $data['view'] = 'om/om_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('om'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'cOm')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('om'));
            redirect(base_url());
        }

        $this->_rules();

        if ($this->form_validation->run() == false) {
            $this->update($this->input->post('IdOm', true));
        } else {
			if(($this->input->post('apoiadora') == 1) || ($this->input->post('apoiadora') == '')){
				$om_apoiadora = NULL;
			} else {
				$om_apoiadora = $this->input->post('om_id_apoiadora', true);
			}

            $data = array(
				'sigla' => $this->input->post('sigla', true),
				'nome' => $this->input->post('nome', true),
				'situacao' => $this->input->post('situacao', true),
				'codigo' => $this->input->post('codigo', true),
				'apoiadora' => $this->input->post('apoiadora', true),
				'om_id_apoiadora' => $om_apoiadora,
            );

            $this->Om_model->update($data, $this->input->post('IdOm', true));
            $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
            redirect(site_url('om'));
        }
    }

    public function status($IdOm)
    {
        if (!is_numeric($IdOm)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('om');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'cOm')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('om'));
            redirect(base_url());
        }

        $row = $this->Om_model->get($IdOm);
        $ajax = $this->input->get('ajax');

        if ($row) {
            if ($this->Om_model->update(array('situacao' => !$row->situacao), $IdOm)) {

                if ($ajax) {
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_edit_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
                redirect(site_url('om'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('om'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('om'));
        }

    }


    public function _rules()
    {
        $this->form_validation->set_rules('sigla', '<b>' . $this->lang->line('sigla_ug') . '</b>', 'trim|required');
		$this->form_validation->set_rules('nome', '<b>' . $this->lang->line('nome_ug') . '</b>', 'trim|required');
		$this->form_validation->set_rules('codigo', '<b>' . $this->lang->line('cod_ug') . '</b>', 'trim|required');

        $this->form_validation->set_rules('IdOm', 'IdOm', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Om.php */
/* Location: ./application/controllers/Om.php */
