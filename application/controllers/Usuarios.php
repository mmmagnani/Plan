<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * author: Marcelo Magnani
 * email: marcelommagnani@uol.com.br
 *
 */

class Usuarios extends CI_Controller {

	public function __construct() {
		parent::__construct();

		if ((!session_id()) || (!$this -> session -> userdata('logado'))) {
			redirect('plan/login');
		}

		$this -> load -> model('Usuarios_model');
		$this -> load -> library('form_validation');
	}

	public function index() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'cUsuario')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('users'));
			redirect(base_url());
		}

		$data['view'] = 'usuarios/usuarios_list';
		$this -> load -> view('tema/topo', $data, false);
	}

	public function datatable() {

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'cUsuario')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('users'));
			redirect(base_url());
		}

		$this -> load -> model('Usuarios_model');
		$result_data = $this -> Usuarios_model -> get_datatables();
		$data = array();

		foreach ($result_data as $row) {
			$line = array();

			$line[] = $row -> IdUsuarios;
			$line[] = $row -> nome;
			$line[] = $row -> situacao ? $this -> lang -> line('app_active') : $this -> lang -> line('app_inactive');
			$line[] = $row -> permissao;

			$color = $row -> situacao ? 'btn-danger' : 'btn-success';
			$icon = $row -> situacao ? 'fa fa-window-close' : 'fa fa-check';
			$title = $row -> situacao ? $this -> lang -> line('app_disable') : $this -> lang -> line('app_activate');

			$line[] = '<a href="' . site_url('usuarios/read/' . $row -> IdUsuarios) . '" class="btn btn-dark" title="' . $this -> lang -> line('app_view') . '"><i class="fa fa-eye"></i> </a> 
                       <a href="' . site_url('usuarios/update/' . $row -> IdUsuarios) . '" class="btn btn-info" title="' . $this -> lang -> line('app_edit') . '"><i class="fa fa-edit"></i></a> 
                       <a href="' . site_url('usuarios/status/' . $row -> IdUsuarios) . '" class="btn ' . $color . ' delete" title="' . $title . '"><i class="' . $icon . '"></i></a>';
			$data[] = $line;
		}

		$output = array('draw' => intval($this -> input -> post('draw')), 'recordsTotal' => $this -> Usuarios_model -> get_all_data(), 'recordsFiltered' => $this -> Usuarios_model -> get_filtered_data(), 'data' => $data);
		echo json_encode($output);
	}

	public function read($id) {

		if (!is_numeric($id)) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect('usuarios');
		}

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'cUsuario')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('users'));
			redirect(base_url());
		}

		$row = $this -> Usuarios_model -> with('permissao') -> with('setor') -> get($id);
		if ($row) {
			$data = array('IdUsuarios' => $row -> IdUsuarios, 'nome' => $row -> nome, 'email' => $row -> email, 'telefone' => $row -> telefone, 'situacao' => $row -> situacao, 'setor_id' => $row -> setor_id, 'setor' => $row -> setor -> sigla, 'permissoes_id' => $row -> permissoes_id, 'permissao' => $row -> permissao -> nome, );

			$data['view'] = 'usuarios/usuarios_read';
			$this -> load -> view('tema/topo', $data, false);
		} else {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect(site_url('usuarios'));
		}
	}

	public function create() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'cUsuario')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_add') . ' ' . $this -> lang -> line('users'));
			redirect(base_url());
		}

		$this -> load -> model('Permissoes_model');
		$permissoes = $this -> Permissoes_model -> where('situacao', '1') -> where('IdPermissao !=', 1) -> as_dropdown('nome') -> get_all();
		$permissoes[] = '';
		asort($permissoes);
		$this -> load -> model('Om_model');
		if ($this -> session -> userdata('om_id') == 1) {
			$oms = $this -> Om_model -> as_dropdown('sigla') -> get_all();
		} else {
			$oms = $this -> Om_model -> where('IdOm', $this -> session -> userdata('om_id')) -> as_dropdown('sigla') -> get_all();
		}
		$setores = $this -> Usuarios_model -> selectSetor($this -> session -> userdata('om_id'));

		$data = array('button' => '<i class="fa fa-plus"></i> ' . $this -> lang -> line('app_create'), 'action' => site_url('usuarios/create_action'), 'IdUsuarios' => set_value('IdUsuarios'), 'nome' => set_value('nome'), 'om_id' => set_value('om_id'), 'oms' => $oms, 'setor_id' => set_value('setor_id'), 'setores' => $setores, 'email' => set_value('email'), 'cpf' => set_value('cpf'), 'telefone' => set_value('telefone'), 'senha' => set_value('usu_senha'), 'situacao' => set_value('usu_active'), 'permissoes_id' => set_value('permissoes_id'), 'permissoes' => $permissoes);

		$data['view'] = 'usuarios/usuarios_form';
		$this -> load -> view('tema/topo', $data, false);

	}

	public function create_action() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'cUsuario')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_add') . ' ' . $this -> lang -> line('users'));
			redirect(base_url());
		}

		$this -> _rules();
		$this -> form_validation -> set_rules('senha', '<b>' . $this -> lang -> line('user_password') . '</b>', 'trim|required');

		if ($this -> form_validation -> run() == false) {
			$this -> create();
		} else {
			$data = array('nome' => $this -> input -> post('nome', true), 'om_id' => $this -> input -> post('om_id', true), 'setor_id' => $this -> input -> post('setor_id', true), 'email' => $this -> input -> post('email', true), 'cpf' => preg_replace("/\D+/", "", $this -> input -> post('cpf', true)), 'telefone' => $this -> input -> post('telefone', true), 'senha' => password_hash($this -> input -> post('senha'), PASSWORD_DEFAULT), 'situacao' => $this -> input -> post('situacao', true), 'dataCadastro' => date('Y-m-d'), 'permissoes_id' => $this -> input -> post('permissoes_id', true), 'usu_ug' => $this -> input -> post('usu_ug', true), );

			$this -> Usuarios_model -> insert($data);
			$this -> session -> set_flashdata('success', $this -> lang -> line('app_add_message'));
			redirect(site_url('usuarios'));
		}
	}

	public function update($id) {
		if (!is_numeric($id)) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect('usuarios');
		}

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'cUsuario')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_edit') . ' ' . $this -> lang -> line('users'));
			redirect(base_url());
		}

		$row = $this -> Usuarios_model -> get($id);

		if ($row) {

			$this -> load -> model('Permissoes_model');
			$permissoes = $this -> Permissoes_model -> where('situacao', '1') -> where('IdPermissao !=', 1) -> as_dropdown('nome') -> get_all();
			$this -> load -> model('Om_model');
			if ($this -> session -> userdata('om_id') == 1) {
				$oms = $this -> Om_model -> as_dropdown('sigla') -> get_all();
			} else {
				$oms = $this -> Om_model -> where('IdOm', $this -> session -> userdata('om_id')) -> as_dropdown('sigla') -> get_all();
			}

			$setores = $this -> Usuarios_model -> selectSetor($this -> session -> userdata('om_id'));

			$data = array('button' => '<i class="fa fa-edit"></i> ' . $this -> lang -> line('app_edit'), 'action' => site_url('usuarios/update_action'), 'IdUsuarios' => set_value('IdUsuarios', $row -> IdUsuarios), 'nome' => set_value('nome', $row -> nome), 'om_id' => set_value('om_id', $row -> om_id), 'oms' => $oms, 'setor_id' => set_value('setor_id', $row -> setor_id), 'setores' => $setores, 'cpf' => set_value('cpf', $row -> cpf), 'telefone' => set_value('telefone', $row -> telefone), 'email' => set_value('email', $row -> email), 'senha' => '', 'situacao' => set_value('situacao', $row -> situacao), 'permissoes_id' => set_value('permissoes_id', $row -> permissoes_id), 'permissoes' => $permissoes);
			$data['view'] = 'usuarios/usuarios_form';
			$this -> load -> view('tema/topo', $data, false);

		} else {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect(site_url('usuarios'));
		}
	}

	public function update_action() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'cUsuario')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_edit') . ' ' . $this -> lang -> line('users'));
			redirect(base_url());
		}

		$this -> _rules();

		if ($this -> form_validation -> run() == false) {
			$this -> update($this -> input -> post('IdUsuarios', true));
		} else {
			$data = array('nome' => $this -> input -> post('nome', true), 'om_id' => $this -> input -> post('om_id', true), 'setor_id' => $this -> input -> post('setor_id', true), 'email' => $this -> input -> post('email', true), 'cpf' => preg_replace("/\D+/", "", $this -> input -> post('cpf', true)), 'telefone' => $this -> input -> post('telefone', true), 'situacao' => $this -> input -> post('situacao', true), 'permissoes_id' => $this -> input -> post('permissoes_id', true), );
			// Change password if not blank
			if ($this -> input -> post('senha')) {
				$senha = password_hash($this -> input -> post('senha'), PASSWORD_DEFAULT);
				$data['senha'] = $senha;
			}

			$this -> Usuarios_model -> update($data, $this -> input -> post('IdUsuarios', true));
			$this -> session -> set_flashdata('success', $this -> lang -> line('app_edit_message'));
			redirect(site_url('usuarios'));
		}
	}

	public function status($IdUsuarios) {

		if (!is_numeric($IdUsuarios)) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect('usuarios');
		}

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'cUsuario')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_edit') . ' ' . $this -> lang -> line('users'));
			redirect(base_url());
		}

		$row = $this -> Usuarios_model -> get($IdUsuarios);
		$ajax = $this -> input -> get('ajax');

		if ($row) {
			if ($this -> Usuarios_model -> update(array('situacao' => !$row -> situacao), $IdUsuarios)) {

				if ($ajax) {
					echo json_encode(array('result' => true, 'message' => $this -> lang -> line('app_edit_message')));
					die();
				}
				$this -> session -> set_flashdata('success', $this -> lang -> line('app_edit_message'));
				redirect(site_url('usuarios'));
			} else {

				if ($ajax) {
					echo json_encode(array('result' => false, 'message' => $this -> lang -> line('app_error')));
					die();
				}

				$this -> session -> set_flashdata('error', $this -> lang -> line('app_error'));
				redirect(site_url('usuarios'));
			}

		} else {

			if ($ajax) {
				echo json_encode(array('result' => false, 'message' => $this -> lang -> line('app_not_found')));
				die();
			}
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect(site_url('usuarios'));
		}

	}

	function check_permissao() {
		if ($this -> input -> post('permissoes_id') > 0) {
			return TRUE;
		} else {
			$error = 'Escolha o <b>perfil</b> do usuário.';
			$this -> form_validation -> set_message('check_permissao', $error);
			return FALSE;
		}
	}

	public function _rules() {
		$this -> form_validation -> set_rules('nome', '<b>' . $this -> lang -> line('user_name') . '</b>', 'trim|required');
		$this -> form_validation -> set_rules('email', '<b>' . $this -> lang -> line('user_email') . '</b>', 'trim|required|valid_email');
		$this -> form_validation -> set_rules('senha', '<b>' . $this -> lang -> line('user_password') . '</b>', 'trim');
		$this -> form_validation -> set_rules('situacao', '<b>' . $this -> lang -> line('user_status') . '</b>', 'trim|required');
		$this -> form_validation -> set_rules('permissoes_id', '', 'callback_check_permissao');

		$this -> form_validation -> set_rules('IdUsuarios', 'IdUsuarios', 'trim');
		$this -> form_validation -> set_error_delimiters('<span class="text-danger">', '</span>');
	}

	public function getSetores() {
		$om_id = $this -> input -> post('om_id');

		$this -> load -> model('Usuarios_model');
		$setores = $this -> Usuarios_model -> selectSetor($om_id);
		$options = "<option>Selecione o setor</option>";
		foreach ($setores as $setor) {
			$options .= "<option value='{$setor->IdSetor}'>$setor->sigla - $setor->descricao</option>" . PHP_EOL;
		}
		echo $options;

	}

}

/* End of file Usuarios.php */
/* Location: ./application/controllers/Usuarios.php */
