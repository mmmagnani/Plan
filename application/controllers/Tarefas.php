<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed');
}

/**
 * author: Marcelo Magnani
 * email: marcelommagnani@uol.com.br
 *
 */

class Tarefas extends CI_Controller {

	public function __construct() {
		parent::__construct();

		if ((!session_id()) || (!$this -> session -> userdata('logado'))) {
			redirect('plan/login');
		}

		$this -> load -> model('Tarefas_model');
		$this -> load -> library('form_validation');
		$this -> load -> library('PHPExcel');
		$this -> load -> helper('formater');
	}

	public function index() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'vTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}
		$row = $this -> Tarefas_model -> getBloqueio();
		if (!$row) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('system_not_configured'));
			redirect(base_url());
		}

		$data['bloqueio'] = $row -> bloqueio;

		$data['view'] = 'tarefas/tarefas_list';
		$this -> load -> view('tema/topo', $data, false);
	}

	public function datatable($id = NULL) {

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'vTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}

		$this -> load -> model('Tarefas_model');
		$result_data = $this -> Tarefas_model -> get_datatables($id);
		$data = array();

		foreach ($result_data as $row) {
			$line = array();
			if ($this -> permission -> check($this -> session -> userdata('permissao'), 'dTarefas')) {
				$line[] = '<input type="checkbox" class="remove" name="item_id[]" value="' . $row -> IdTarefa . '">';
			} else {
				$line[] = "";
			}
			$line[] = $row -> spo_id;
			$line[] = $row -> titulo;
			$line[] = number_format($row -> valor_previsto, 2, ',', '.');
			switch ($row->status) {
				case 3 :
					$line[] = '<b>' . number_format($row -> valor_autorizado, 2, ',', '.') . ' *</b>';
					break;
				case 2 :
					$line[] = '<b>' . number_format($row -> valor_autorizado, 2, ',', '.') . '</b>';
					break;
				case 1 :
					$line[] = '<p style="color:#0000FF"><b>' . number_format($row -> valor_autorizado, 2, ',', '.') . '</p></b>';
					break;
				default :
					$line[] = number_format($row -> valor_autorizado, 2, ',', '.');
			}

			$color = $row -> situacao ? 'btn-danger' : 'btn-success';
			$icon = $row -> situacao ? 'fa fa-window-close' : 'fa fa-check';
			$title = $row -> situacao ? $this -> lang -> line('app_disable') : $this -> lang -> line('app_activate');

			if ($row -> valor_executado > 0) {
				$line[] = '<p style="color:green"><b>' . number_format($row -> valor_executado, 2, ',', '.') . '</p></b>';
			} else {
				$line[] = number_format($row -> valor_executado, 2, ',', '.');
			}

			if ($this -> permission -> check($this -> session -> userdata('permissao'), 'vTarefas')) {
				$view = '<a href="' . site_url('tarefas/read/' . $row -> IdTarefa) . '" class="btn btn-dark" title="' . $this -> lang -> line('app_view') . '"><i class="fa fa-eye"></i> </a>';
			} else {
				$view = '';
			}
			if ($this -> permission -> check($this -> session -> userdata('permissao'), 'eTarefas')) {
				$edit = '<a href="' . site_url('tarefas/update/' . $row -> IdTarefa) . '" class="btn btn-info" title="' . $this -> lang -> line('app_edit') . '"><i class="fa fa-edit"></i></a>';
			} else {
				$edit = '';
			}
			if ($this -> permission -> check($this -> session -> userdata('permissao'), 'dTarefas')) {
				$del = '<a href="' . site_url('tarefas/delete/' . $row -> IdTarefa) . '" class="btn ' . $color . ' delete" title="' . $title . '"><i class="' . $icon . '"></i></a>';
			} else {
				$del = '';
			}

			$line[] = $view . ' ' . $edit . ' ' . $del;

			$data[] = $line;
		}

		$output = array('draw' => intval($this -> input -> post('draw')), 'recordsTotal' => $this -> Tarefas_model -> get_all_data($id), 'recordsFiltered' => $this -> Tarefas_model -> get_filtered_data($id), 'data' => $data, );
		echo json_encode($output);
	}

	public function read($id) {
		if (!is_numeric($id)) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect('tarefas');
		}

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'vTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}

		$row = $this -> Tarefas_model -> with('projetos') -> with('setores') -> get($id);

		if ($row) {
			if ($row -> situacao == 1) {
				$situacao = $this -> lang -> line('app_active');
			} else {
				$situacao = $this -> lang -> line('app_inactive');
			}
			$valor_previsto = number_format($row -> valor_previsto, 2, ',', '.');
			$valor_autorizado = number_format($row -> valor_autorizado, 2, ',', '.');
			$valor_executado = number_format($row -> valor_executado, 2, ',', '.');

			$data = array('IdTarefa' => $row -> IdTarefa, 'spo_id' => $row -> spo_id, 'om_id' => $row -> om_id, 'setor' => $row -> setores -> sigla, 'projeto' => $row -> projetos -> titulo, 'titulo' => $row -> titulo, 'CATMAT' => $row -> CATMAT, 'descricao' => $row -> descricao, 'justificativa' => $row -> justificativa, 'valor_previsto' => $valor_previsto, 'valor_autorizado' => $valor_autorizado, 'valor_executado' => $valor_executado, 'situacao' => $situacao, );

			$data['view'] = 'tarefas/tarefas_read';
			$this -> load -> view('tema/topo', $data, false);
		} else {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect(site_url('tarefas'));
		}
	}

	public function create() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'aTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_add') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}

		$this -> load -> model('Projetos_model');
		$projetos = $this -> Projetos_model -> as_dropdown('titulo') -> get_all_by_abrangencia();
		$projetos[0] = '';
		asort($projetos);

		$data = array('button' => '<i class="fa fa-plus"></i> ' . $this -> lang -> line('app_create'), 'action' => site_url('tarefas/create_action'), 'IdTarefa' => set_value('IdProjeto'), 'spo_id' => set_value('spo_id'), 'om_id' => set_value('om_id'), 'ano' => set_value('ano'), 'setor_id' => set_value('setor_id'), 'projeto_id' => set_value('projeto_id'), 'projetos' => $projetos, 'titulo' => set_value('titulo'), 'CATMAT' => set_value('CATMAT'), 'descricao' => set_value('descricao'), 'justificativa' => set_value('justificativa'), 'valor_previsto' => set_value('valor_previsto'), 'situacao' => set_value('situacao'), 'checkBoxObjeto' => set_value('checkBoxObjeto'), 'valor_autorizado' => set_value(''), 'status' => set_value(''), 'prioridade' => set_value(''), );

		$data['view'] = 'tarefas/tarefas_form';
		$this -> load -> view('tema/topo', $data, false);

	}

	public function create_action() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'aTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_add') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}

		$this -> _rules();

		if ($this -> form_validation -> run() == false) {
			$this -> create();
		} else {

			//$cod = $this -> Tarefas_model -> getCodOm();
			//$seq = $this -> Tarefas_model -> uid('tarefas', 7, $cod);
			//$spo = $cod . $seq;

			$data = array('IdTarefa' => $this -> input -> post('IdTarefa', true), 'om_id' => $this -> input -> post('om_id', true), 'ano' => $this -> input -> post('ano', true), 'setor_id' => $this -> input -> post('setor_id', true), 'projeto_id' => $this -> input -> post('projeto_id', true), 'titulo' => $this -> input -> post('titulo', true), 'CATMAT' => $this -> input -> post('CATMAT', true), 'descricao' => $this -> input -> post('descricao', true), 'justificativa' => $this -> input -> post('justificativa', true), 'valor_previsto' => str_replace(',', '.', str_replace('.', '', $this -> input -> post('valor_previsto', true))), 'situacao' => $this -> input -> post('situacao', true), 'checkBoxObjeto' => $this -> input -> post('checkBoxObjeto', true), );

			$this -> Tarefas_model -> insert($data);
			$this -> session -> set_flashdata('success', $this -> lang -> line('app_add_message'));
			redirect(site_url('tarefas'));
		}
	}

	public function update($id) {
		if (!is_numeric($id)) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect('tarefas');
		}

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'eTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_edit') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}

		$this -> load -> model('Projetos_model');
		$projetos = $this -> Projetos_model -> as_dropdown('titulo') -> get_all_by_abrangencia();
		$projetos[0] = '';
		asort($projetos);

		$row = $this -> Tarefas_model -> with('setores') -> get($id);

		if ($row) {
			$data = array('button' => '<i class="fa fa-edit"></i> ' . $this -> lang -> line('app_edit'), 'action' => site_url('tarefas/update_action'), 'IdTarefa' => set_value('IdTarefa', $row -> IdTarefa), 'spo_id' => set_value('spo_id', $row -> spo_id), 'om_id' => set_value('om_id', $row -> om_id), 'ano' => set_value('ano', $row -> ano), 'setor' => $row -> setores -> sigla, 'setor_id' => set_value('setor_id', $row -> setor_id), 'projeto_id' => set_value('projeto_id', $row -> projeto_id), 'projetos' => $projetos, 'titulo' => set_value('titulo', $row -> titulo), 'CATMAT' => set_value('CATMAT', $row -> CATMAT), 'descricao' => set_value('descricao', $row -> descricao), 'justificativa' => set_value('justificativa', $row -> justificativa), 'valor_previsto' => set_value('valor_previsto', $row -> valor_previsto), 'valor_autorizado' => set_value('valor_autorizado', $row -> valor_autorizado), 'prioridade' => set_value('prioridade', $row -> prioridade), 'status' => set_value('status', $row -> status), 'situacao' => set_value('situacao', $row -> situacao), 'checkBoxObjeto' => set_value('checkBoxObjeto', $row -> checkBoxObjeto), );
			$data['view'] = 'tarefas/tarefas_form';
			$this -> load -> view('tema/topo', $data, false);

		} else {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect(site_url('tarefas'));
		}
	}

	public function update_action() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'eTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_edit') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}
		$this -> _rules();

		if ($this -> form_validation -> run() == false) {
			$this -> update($this -> input -> post('IdTarefa', true));
		} else {

			$data = array('spo_id' => $this -> input -> post('spo_id', true), 'om_id' => $this -> input -> post('om_id', true), 'ano' => $this -> input -> post('ano', true), 'setor_id' => $this -> input -> post('setor_id', true), 'projeto_id' => $this -> input -> post('projeto_id', true), 'titulo' => $this -> input -> post('titulo', true), 'CATMAT' => $this -> input -> post('CATMAT', true), 'descricao' => $this -> input -> post('descricao', true), 'justificativa' => $this -> input -> post('justificativa', true), 'valor_previsto' => str_replace(',', '.', str_replace('.', '', $this -> input -> post('valor_previsto', true))), 'valor_autorizado' => ($this -> input -> post('ano') > date('Y')) ? null : $this -> input -> post('valor_autorizado', true), 'status' => ($this -> input -> post('ano') > date('Y')) ? 0 : $this -> input -> post('status', true), 'prioridade' => ($this -> input -> post('ano') > date('Y')) ? null : $this -> input -> post('prioridade', true), 'situacao' => $this -> input -> post('situacao', true), 'checkBoxObjeto' => $this -> input -> post('checkBoxObjeto', true), );

			$this -> Tarefas_model -> update($data, $this -> input -> post('IdTarefa', true));
			$this -> session -> set_flashdata('success', $this -> lang -> line('app_edit_message'));
			redirect(site_url('tarefas'));
		}
	}

	public function delete($IdTarefa) {
		if (!is_numeric($IdTarefa)) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect('tarefas');
		}

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'dTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_delete') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}

		$row = $this -> Tarefas_model -> get($IdTarefa);
		$ajax = $this -> input -> get('ajax');

		if ($row) {

			if ($this -> Tarefas_model -> delete($IdTarefa)) {

				if ($ajax) {
					echo json_encode(array('result' => true, 'message' => $this -> lang -> line('app_delete_message')));
					die();
				}
				$this -> session -> set_flashdata('success', $this -> lang -> line('app_delete_message'));
				redirect(site_url('projetos'));
			} else {

				if ($ajax) {
					echo json_encode(array('result' => false, 'message' => $this -> lang -> line('app_error')));
					die();
				}

				$this -> session -> set_flashdata('error', $this -> lang -> line('app_error'));
				redirect(site_url('tarefas'));
			}

		} else {

			if ($ajax) {
				echo json_encode(array('result' => false, 'message' => $this -> lang -> line('app_not_found')));
				die();
			}
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect(site_url('tarefas'));
		}

	}

	public function delete_many() {

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'dTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_delete') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}

		$items = $this -> input -> post('item_id[]');

		if ($items) {

			$verify = implode('', $items);
			if (is_numeric($verify)) {

				// $this->Objetivos_model->delete_linked($items);

				$result = $this -> Tarefas_model -> delete_many($items);
				if ($result) {
					echo json_encode(array('result' => true, 'message' => $this -> lang -> line('app_delete_message_many')));
					die();
				} else {
					echo json_encode(array('result' => false, 'message' => $this -> lang -> line('app_error')));
					die();
				}

			} else {
				echo json_encode(array('result' => false, 'message' => $this -> lang -> line('app_data_not_supported')));
				die();
			}
		}

		echo json_encode(array('result' => false, 'message' => $this -> lang -> line('app_empty_data')));
		die();

	}

	function choice_type() {
		if ($this -> input -> post('checkBoxObjeto')) {
			return TRUE;
		} else {
			$error = 'Escolha o <b>tipo</b> da tarefa.';
			$this -> form_validation -> set_message('choice_type', $error);
			return FALSE;
		}
	}

	function check_projeto() {
		if ($this -> input -> post('projeto_id') > 0) {
			return TRUE;
		} else {
			$error = 'Escolha o <b>projeto</b> da tarefa.';
			$this -> form_validation -> set_message('check_projeto', $error);
			return FALSE;
		}
	}

	public function _rules() {
		$this -> form_validation -> set_rules('checkBoxObjeto', '', 'callback_choice_type');
		$this -> form_validation -> set_rules('projeto_id', '', 'callback_check_projeto');
		$this -> form_validation -> set_rules('descricao', '<b>' . $this -> lang -> line('description') . '</b>', 'trim|required');
		$this -> form_validation -> set_rules('titulo', '<b>' . $this -> lang -> line('title') . '</b>', 'trim|required');
		$this -> form_validation -> set_rules('justificativa', '<b>' . $this -> lang -> line('justification') . '</b>', 'trim|required');
		$this -> form_validation -> set_rules('valor_previsto', '<b>' . $this -> lang -> line('estimated_val') . '</b>', 'trim|required');
		$this -> form_validation -> set_error_delimiters('<span class="text-danger">', '</span>');
	}

	public function pesquisar() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'vTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}

		$termo = $this -> input -> get('termo');

		$data['tarefas'] = $this -> Tarefas_model -> pesquisar($termo);

		$data['view'] = 'tarefas/pesquisa';
		$this -> load -> view('tema/topo', $data);

	}

	public function importar() {
		if (!$this -> permission -> checkPermission($this -> session -> userdata('permissao'), 'aTarefas')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_add') . ' ' . $this -> lang -> line('tasks'));
			redirect(base_url());
		}

		$this -> load -> library('form_validation');
		$this -> data['custom_error'] = '';

		$this -> form_validation -> set_rules('projeto_id', '', 'trim|required');

		if ($this -> form_validation -> run() == false) {
			$this -> data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger form_error">' . validation_errors() . '</div>' : false);
		} else {

			$projeto = $this -> input -> post('projeto_id');
			$this -> data['projetoid'] = $projeto;
			$arquivo = $this -> do_upload();

			if ($arquivo == true) {
				$this -> data['upok'] = $this -> lang -> line('app_upload_success');
				$file = $arquivo['file_name'];
				$path = $arquivo['full_path'];
				$tamanho = $arquivo['file_size'];
				$tipo = $arquivo['file_ext'];

				/**  Identify the type of $inputFileName  **/
				$inputFileType = PHPExcel_IOFactory::identify($path);
				/**  Create a new Reader of the type that has been identified  **/
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);

				/**  Load $inputFileName to a PHPExcel Object  **/
				$objPHPExcel = $objReader -> load($path);

				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					$worksheetTitle = $worksheet -> getTitle();
					$highestRow = $worksheet -> getHighestRow() - 1;
					// e.g. 10
					$highestColumn = $worksheet -> getHighestColumn();
					// e.g 'F'
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					$nrColumns = ord($highestColumn) - 64;

					if ($nrColumns == 6 and $worksheetTitle == 'DADOS') {
						$linha = array();
						for ($row = 2; $row <= $highestRow + 1; ++$row) {
							$val = array();
							for ($col = 0; $col < $highestColumnIndex; ++$col) {
								$cell = $worksheet -> getCellByColumnAndRow($col, $row);
								$val[$col] = $cell -> getValue();
							}
							if (($val[0] != 'S') && ($val[0] != 'C') && ($val[0] != 'P') && ($val[0] != 'D')) {
								$this -> session -> set_flashdata('error', $this -> lang -> line('app_erro_tipo'));
								redirect(site_url('tarefas'));
							} else if ($val[0] == 'S') {
								$tipoO = 's';
							} else if ($val[0] == 'C') {
								$tipoO = 'c';
							} else if ($val[0] == 'P') {
								$tipoO = 'p';
							} else if ($val[0] == 'D') {
								$tipoO = 'd';
							}
							if (empty($val[3])) {
								$catmat = NULL;
							} else {
								$catmat = $val[3];
							}
							$linha[] = array('checkBoxObjeto' => $tipoO, 'titulo' => $val[1], 'descricao' => $val[2], 'CATMAT' => $catmat, 'justificativa' => $val[4], 'estimado' => $val[5]);
						}
						$this -> data['feito'] = true;
						//$this->data['result'] = $linha;
						$this -> session -> set_userdata('importado', $linha);

					} else {

						$this -> session -> set_flashdata('error', $this -> lang -> line('imported_plan_incorrect'));
					}
				}

				unlink($path);

			} else {
				$this -> data['custom_error'] = '<div class="alert alert-danger"><p>' . $this -> lang -> line('app_error') . '</p></div>';
			}
		}

		if (!$this -> permission -> checkPermission($this -> session -> userdata('permissao'), 'fGerset')) {
			$this -> load -> model('Projetos_model');
			$projetos = $this -> Projetos_model -> as_dropdown('titulo') -> get_all_by_abrangencia();
			$projetos[0] = '';
			sort($projetos);
			$this -> data['projetos'] = $projetos;
		} else {
			$this -> load -> model('Projetos_model');
			$projetos = $this -> Projetos_model -> as_dropdown('titulo') -> get_all_by_om();
			$projetos[0] = '';
			sort($projetos);
			$this -> data['projetos'] = $projetos;
		}
		$this -> data['process'] = 0;
		$this -> data['aplicar'] = 0;
		$this -> data['view'] = 'tarefas/uploadPlanilha';
		$this -> load -> view('tema/topo', $this -> data);
	}

	public function exportar() {

		// Create new Spreadsheet object
		$objPHPExcel = $this -> phpexcel;

		//Set document properties
		$objPHPExcel -> getProperties() -> setCreator($this -> session -> userdata('nome')) -> setLastModifiedBy($this -> session -> userdata('nome')) -> setTitle('SPO - ' . $this -> session -> userdata('anofiscal')) -> setSubject('Planilha SPO') -> setDescription('Planilha Tarefas.') -> setKeywords('SPO') -> setCategory('Arquivo SPO');

		// add style to the header
		$styleArray = array('font' => array('bold' => true, ), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, ), 'borders' => array('allBorders' => array('borderStyle' => PHPExcel_Style_Border::BORDER_THIN, ), ), 'fill' => array('fillType' => PHPExcel_Style_Fill::FILL_SOLID, 'startColor' => array('argb' => 'CCCCCCCC', ), ), );

		$objPHPExcel -> getActiveSheet() -> getStyle('A1:I1') -> applyFromArray($styleArray);

		// auto fit column to content

		foreach (range('A', 'C') as $columnID) {
			$objPHPExcel -> getActiveSheet() -> getColumnDimension($columnID) -> setAutoSize(true);
		}

		$objPHPExcel -> getActiveSheet() -> getColumnDimension('D') -> setWidth(100);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension('E') -> setWidth(100);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension('F') -> setAutoSize(true);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension('G') -> setAutoSize(true);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension('H') -> setAutoSize(true);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension('I') -> setAutoSize(true);

		// set the names of header cells
		$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A1", 'Nº SPO') -> setCellValue("B1", 'Título') -> setCellValue("C1", 'Setor') -> setCellValue("D1", 'Descrição') -> setCellValue("E1", 'Justificativa') -> setCellValue("F1", 'Valor Estimado (R$)') -> setCellValue("G1", 'Valor Autorizado (R$)') -> setCellValue("H1", 'Prioridade') -> setCellValue("I1", 'Projeto');

		// Add some data
		$limite = 5000;
		if ($this -> permission -> checkPermission($this -> session -> userdata('permissao'), 'fConselho')) {
			$tarefas = $this -> Tarefas_model -> get_tarefas('', $limite);
		} else if ($this -> permission -> checkPermission($this -> session -> userdata('permissao'), 'fGerset')) {
			$where = 'gersets.setor_id=' . $this -> session -> userdata('setor_id');
			$tarefas = $this -> Tarefas_model -> get_tarefas($where, $limite);
		} else {
			$where = 'tarefas.setor_id=' . $this -> session -> userdata('setor_id');
			$tarefas = $this -> Tarefas_model -> get_tarefas($where, $limite);
		}
		$x = 2;
		if (!$tarefas) {
			$objPHPExcel -> getActiveSheet() -> mergeCells('A2:F2');
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A2", 'Não há tarefas cadastradas');
		} else {
			foreach ($tarefas as $t) {
				$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("A$x", $t -> spo_id) -> setCellValue("B$x", $t -> titulo) -> SETcELLvALUE("C$x", $t -> sigla) -> setCellValue("D$x", $t -> descricao) -> setCellValue("E$x", $t -> justificativa) -> setCellValue("F$x", $t -> valor_previsto) -> setCellValue("G$x", $t -> valor_autorizado) -> setCellValue("H$x", $t -> prioridade) -> setCellValue("I$x", $t -> projeto);
				$x++;
			}
			$y = $x;
			$x = $x - 1;
			$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));

			$objPHPExcel -> getActiveSheet() -> mergeCells('A' . $y . ':E' . $y);
			$objPHPExcel -> getActiveSheet() -> getStyle("A$y") -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel -> getActiveSheet() -> getStyle('A' . $y . ':I' . $y) -> getFont() -> setBold(true);
			$objPHPExcel -> getActiveSheet() -> getStyle('A1:I' . $y) -> applyFromArray($styleArray);
			$objPHPExcel -> getActiveSheet() -> getStyle('A1');
			$objPHPExcel -> getActiveSheet() -> getStyle('D2:E' . $y) -> getAlignment() -> setWrapText(true);
			$objPHPExcel -> getActiveSheet() -> getStyle('F2:F' . $y) -> getNumberFormat() -> setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel -> getActiveSheet() -> getStyle('G2:G' . $y) -> getNumberFormat() -> setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel -> getActiveSheet() -> setCellValue("A$y", 'TOTAL');
			$objPHPExcel -> getActiveSheet() -> setCellValue("F$y", '=SUM(F2:F' . $x . ')');
			$objPHPExcel -> getActiveSheet() -> setCellValue("G$y", '=SUM(G2:G' . $x . ')');
		}

		// Rename worksheet
		$objPHPExcel -> getActiveSheet() -> setTitle('SPO - ' . $this -> session -> userdata('anofiscal'));

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel -> setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01spo.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		// Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		// always modified
		header('Cache-Control: cache, must-revalidate');
		// HTTP/1.1
		header('Pragma: public');
		// HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		ob_end_clean();
		$objWriter -> save('php://output');
		exit ;

	}

	public function do_upload() {

		$date = date('d-m-Y');

		$config['upload_path'] = './assets/planilhas/';
		$config['allowed_types'] = 'xls';
		$config['max_size'] = 0;
		$config['max_width'] = '3000';
		$config['max_height'] = '2000';
		$config['encrypt_name'] = true;

		$this -> load -> library('upload', $config);

		if (!$this -> upload -> do_upload()) {
			$error = array('error' => $this -> upload -> display_errors());

			$this -> session -> set_flashdata('error', 'Erro ao fazer upload do arquivo, verifique se a extensão do arquivo é permitida.');

		} else {
			return $this -> upload -> data();
		}
	}

	public function complete() {
		$acao = (isset($_GET['acao'])) ? $_GET['acao'] : '';
		$parametro = (isset($_GET['parametro'])) ? $_GET['parametro'] : '';
		if ($acao == 'autocomplete') {
			$this -> load -> model('Tarefas_model');
			$result = $this -> Tarefas_model -> searchCATMAT($parametro);
			$json = json_encode($result);
			echo $json;
		}
	}

	public function consulta() {
		$acao = (isset($_GET['acao'])) ? $_GET['acao'] : '';
		$parametro = (isset($_GET['parametro'])) ? $_GET['parametro'] : '';
		if ($acao == 'consulta') {
			$where = (!empty($parametro)) ? "MATCH(descPDM, descCATMAT) AGAINST ('" . $parametro . "'IN BOOLEAN MODE) LIMIT 1" : "";
			$this -> load -> model('Tarefas_model');
			$result = $this -> Tarefas_model -> getCATMAT($where);
			$json = json_encode($result);
			echo $json;
		}
	}

}

/* End of file Projetos.php */
/* Location: ./application/controllers/Projetos.php */
