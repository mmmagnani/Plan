<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed');
}

/**
 * author: Marcelo Magnani
 * email: marcelommagnani@uol.com.br
 *
 */

class Priorizar extends CI_Controller {

	public function __construct() {
		parent::__construct();

		if ((!session_id()) || (!$this -> session -> userdata('logado'))) {
			redirect('plan/login');
		}

		$this -> load -> model('Priorizar_model');
		$this -> load -> library('form_validation');
		$this -> load -> helper('formater');
		$this -> load -> library('PHPExcel');
	}

	public function index() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'vProjetos')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('projects'));
			redirect(base_url());
		}

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'fConselho')) {
			if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'fGerset')) {
				$data['results'] = $this -> Priorizar_model -> get_data();
			} else {
				$where = 'gersets.setor_id =' . $this -> session -> userdata('setor_id');
				$data['results'] = $this -> Priorizar_model -> get_data($where);
			}
		} else {
			$data['results'] = $this -> Priorizar_model -> get_data();
		}

		if ($this -> Priorizar_model -> checkAutorizados()) {
			$data['habilita'] = true;
		} else {
			$data['habilita'] = false;
		}

		$data['view'] = 'priorizar/projetos_list';
		$this -> load -> view('tema/topo', $data, false);
	}

	public function datatable() {

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'vProjetos')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('projects'));
			redirect(base_url());
		}

		$this -> load -> model('Priorizar_model');

		$result_data = $this -> Priorizar_model -> get_datatables();

		$data = array();

		foreach ($result_data as $row) {
			$line = array();

			$line[] = $row -> titulo;
			$line[] = number_format($row -> estimado, 2, ',', '.');
			$line[] = 'Custeio - ' . number_format($row -> autorizado, 2, ',', '.') . '<br />'.' Investimento - ' . number_format($row -> autorizadop, 2, ',', '.');
			$line[] = $row -> gerset;
			if ($this -> permission -> check($this -> session -> userdata('permissao'), 'fConselho')) {
				$line[] = '<a href="' . site_url('priorizar/read/' . $row -> IdProjeto) . '" class="btn btn-dark" title="' . $this -> lang -> line('prioritize_project_tasks') . '"><i class="fa fa-sort"></i> </a>
                       <a href="' . site_url('priorizar/addAmount/' . $row -> IdProjeto) . '" class="btn btn-info" title="' . $this -> lang -> line('app_add_amount') . '"><i class="fa fa-plus-square"></i></a>
                       <a href="' . site_url('priorizar/delAmount/' . $row -> IdProjeto) . '" class="btn btn-danger" title="' . $this -> lang -> line('app_remove_amount') . '"><i class="fa fa-minus-square"></i></a>';
			} else {
				$line[] = '<a href="' . site_url('priorizar/read/' . $row -> IdProjeto) . '" class="btn btn-dark" title="' . $this -> lang -> line('app_view') . '"><i class="fa fa-eye"></i> </a>';
			}
			$data[] = $line;
		}

		$output = array('draw' => intval($this -> input -> post('draw')), 'recordsTotal' => $this -> Priorizar_model -> get_all_data(), 'recordsFiltered' => $this -> Priorizar_model -> get_filtered_data(), 'data' => $data, );
		echo json_encode($output);
	}

	public function read($id) {
		if (!is_numeric($id)) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect('priorizar');
		}

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'vProjetos')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('projects'));
			redirect(base_url());
		}

		$row = $this -> Priorizar_model -> with('gersets') -> get($id);
		$setorid = $row -> gersets -> setor_id;
		$this -> load -> model('Setores_model');
		$row2 = $this -> Setores_model -> where('IdSetor', '=', $setorid) -> get();
		if ($row) {
			if ($row -> situacao == 1) {
				$situacao = $this -> lang -> line('app_active');
			} else {
				$situacao = $this -> lang -> line('app_inactive');
			}
			if ($row -> abrangencia == 0) {
				$abrangencia = ucfirst($this -> lang -> line('app_restricted'));
			} else {
				$abrangencia = ucfirst($this -> lang -> line('app_general'));
			}
			$data = array('IdProjeto' => $row -> IdProjeto, 'om_id' => $row -> om_id, 'objetivo_id' => $row -> objetivo_id, 'titulo' => $row -> titulo, 'descricao' => $row -> descricao, 'abrangencia' => $abrangencia, 'situacao' => $situacao, );
			if (!is_null($row -> gerset_id)) {
				$data['gerset'] = $row2 -> sigla;
			} else {
				$data['gerset'] = "";
			}

			$this -> load -> model('Tarefas_model');
			$where = 'projeto_id = ' . $row -> IdProjeto;
			$data['tarefas'] = $this -> Tarefas_model -> get_tarefas($where);

			$data['view'] = 'priorizar/projetos_read';
			$this -> load -> view('tema/topo', $data, false);
		} else {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect(site_url('priorizar'));
		}
	}

	public function setOrdem() {
		$tar_id_item = $this -> input -> post('tar_id_item');
		$tar_id_proj = $this -> input -> post('tar_id_proj');
		$this -> load -> model('Priorizar_model');
		if ($this -> Priorizar_model -> setOrdemPrioridade($tar_id_item) && $this -> distribuirRecursos($tar_id_proj, 'c') && $this -> distribuirRecursos($tar_id_proj, 's') && $this -> distribuirRecursos($tar_id_proj, 'p') && $this -> distribuirRecursos($tar_id_proj, 'd')) {
			$this -> session -> set_flashdata('success', $this -> lang -> line('app_priority_success'));
		}
	}

	public function distribuirRecursos($id, $tipodespesa) {
		if($id && $tipodespesa){
		$results = $this -> Priorizar_model -> getTarefasByProjeto($id, $tipodespesa);
		$result = $this -> Priorizar_model -> getAutorizadoByProjeto($id, $tipodespesa);
		$margem = $this -> Priorizar_model -> getMargemReserva() -> margem_reserva;
		$valor_restante = $result -> valor_autorizado;
		$valor_reserva = bcdiv(bcmul($valor_restante, $margem, 2), 100, 2);
		$valor_restante2 = $valor_reserva;
		foreach ($results as $r) {
			$valor_orcado = $r -> valor_previsto;
			if ($valor_restante > 0) {
				if ($valor_orcado >= $valor_restante) {
					$valor_aut_tarefa = $valor_restante;
					$this -> Priorizar_model -> setValorAutorizado($r -> IdTarefa, $valor_aut_tarefa, 2);
					$valor_restante = 0;
					if ($valor_restante == 0 && $valor_orcado > $valor_aut_tarefa && $valor_restante2 > 0) {
						$valor_excedente = bcsub($valor_orcado, $valor_aut_tarefa, 2);
						if ($valor_excedente >= $valor_restante2) {
							$valor_aut_tarefa = bcadd($valor_aut_tarefa, $valor_restante2, 2);
							$this -> Priorizar_model -> setValorAutorizado($r -> IdTarefa, $valor_aut_tarefa, 3);
							$valor_restante2 = 0;
						} else if ($valor_orcado < $valor_restante2) {
							$valor_aut_tarefa = bcadd($valor_aut_tarefa, $valor_excedente, 2);
							$this -> Priorizar_model -> setValorAutorizado($r -> IdTarefa, $valor_aut_tarefa, 3);
							$valor_restante2 = bcsub($valor_restante2, $valor_excedente, 2);
						}
					}
				} else if ($valor_orcado < $valor_restante) {
					$valor_aut_tarefa = $valor_orcado;
					$this -> Priorizar_model -> setValorAutorizado($r -> IdTarefa, $valor_aut_tarefa, 2);
					$valor_restante = bcsub($valor_restante, $valor_aut_tarefa, 2);
				}
			} else if ($valor_restante2 > 0) {
				if ($valor_orcado >= $valor_restante2) {
					$valor_aut_tarefa = $valor_restante2;
					$this -> Priorizar_model -> setValorAutorizado($r -> IdTarefa, $valor_aut_tarefa, 1);
					$valor_restante2 = bcsub($valor_restante2, $valor_aut_tarefa, 2);
				} else if ($valor_orcado < $valor_restante2) {
					$valor_aut_tarefa = $valor_orcado;
					$this -> Priorizar_model -> setValorAutorizado($r -> IdTarefa, $valor_aut_tarefa, 1);
					$valor_restante2 = bcsub($valor_restante2, $valor_aut_tarefa, 2);
				}
			} else {
				$valor_aut_tarefa = 0;
				$this -> Priorizar_model -> setValorAutorizado($r -> IdTarefa, $valor_aut_tarefa, 0);
			}
		}
		return true;
		}
		return false;
	}

	public function delete($IdProjeto) {
		if (!is_numeric($IdProjeto)) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect('projetos');
		}

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'dProjetos')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_delete') . ' ' . $this -> lang -> line('projects'));
			redirect(base_url());
		}
		$data = array('situacao' => 0);

		$row = $this -> Projetos_model -> get($IdProjeto);
		$ajax = $this -> input -> get('ajax');

		if ($row) {

			if ($this -> Projetos_model -> update($data, $IdProjeto)) {

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
				redirect(site_url('projetos'));
			}

		} else {

			if ($ajax) {
				echo json_encode(array('result' => false, 'message' => $this -> lang -> line('app_not_found')));
				die();
			}
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_not_found'));
			redirect(site_url('projetos'));
		}

	}

	public function delete_many() {

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'dProjetos')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_delete') . ' ' . $this -> lang -> line('projects'));
			redirect(base_url());
		}

		$items = $this -> input -> post('item_id[]');

		if ($items) {

			$verify = implode('', $items);
			if (is_numeric($verify)) {

				// $this->Objetivos_model->delete_linked($items);

				$result = $this -> Projetos_model -> delete_many($items);
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

	public function pesquisar() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'vProjetos')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_view') . ' ' . $this -> lang -> line('projects'));
			redirect(base_url());
		}

		$termo = $this -> input -> get('termo');

		$data['projetos'] = $this -> Projetos_model -> pesquisar($termo);

		$data['view'] = 'projetos/pesquisa';
		$this -> load -> view('tema/topo', $data);

	}

	public function gerarCalendario() {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'aCalendario')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_add') . ' ' . $this -> lang -> line('calendar'));
			redirect(base_url());
		}
		if ($this -> Priorizar_model -> inputupdateCal() == true) {
			$this -> session -> set_flashdata('success', $this -> lang -> line('app_add_message'));
			redirect(site_url('projetos'));
		} else {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_error'));
			redirect(site_url('projetos'));
		}
	}

	public function exportar($id) {

		// Create new Spreadsheet object
		$spreadsheet = $this -> phpexcel;

		//Set document properties
		$spreadsheet -> getProperties() -> setCreator($this -> session -> userdata('nome')) -> setLastModifiedBy($this -> session -> userdata('nome')) -> setTitle('SPO - ' . $this -> session -> userdata('anofiscal') . ' Priorizada') -> setSubject('Planilha SPO') -> setDescription('Planilha SPO Priorizada.') -> setKeywords('SPO') -> setCategory('Arquivo SPO');

		// add style to the header
		$styleArray = array('font' => array('bold' => true, ), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, ), 'borders' => array('allBorders' => array('borderStyle' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => '00000000'), ), ), 'fill' => array('fillType' => PHPExcel_Style_Fill::FILL_SOLID, 'startColor' => array('argb' => 'CCCCCCCC', ), ), );

		$spreadsheet -> getActiveSheet() -> getStyle('A1:F1') -> applyFromArray($styleArray);

		// auto fit column to content

		foreach (range('A', 'D') as $columnID) {
			$spreadsheet -> getActiveSheet() -> getColumnDimension($columnID) -> setAutoSize(true);
		}
		$spreadsheet -> getActiveSheet() -> getColumnDimension('E') -> setWidth(100);
		$spreadsheet -> getActiveSheet() -> getColumnDimension('F') -> setWidth(100);
		$spreadsheet -> getActiveSheet() -> getColumnDimension('G') -> setAutoSize(true);
		$spreadsheet -> getActiveSheet() -> getStyle('D2:F255') -> getAlignment() -> setWrapText(true);
		$spreadsheet -> getActiveSheet() -> getStyle('G2:G255') -> getNumberFormat() -> setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

		// set the names of header cells
		$spreadsheet -> setActiveSheetIndex(0) -> setCellValue("A1", 'Prioridade') -> setCellValue("B1", 'Nº SPO') -> setCellValue("C1", 'Setor') -> setCellValue("D1", 'Título') -> setCellValue("E1", 'Descrição') -> setCellValue("F1", 'Justificativa') -> setCellValue("G1", 'Valor Estimado (R$)');

		// Add some data
		$tarefas = $this -> Priorizar_model -> getTarefasByProjeto($id);
		$x = 2;
		if (!$tarefas) {
			$spreadsheet -> getActiveSheet() -> mergeCells('A2:G2');
			$spreadsheet -> setActiveSheetIndex(0) -> setCellValue("A2", 'Não há tarefas cadastradas');
		} else {
			foreach ($tarefas as $t) {
				$spreadsheet -> setActiveSheetIndex(0) -> setCellValue("A$x", $t -> prioridade) -> setCellValue("B$x", $t -> spo_id) -> setCellValue("C$x", $t -> sigla) -> setCellValue("D$x", $t -> titulo) -> setCellValue("E$x", $t -> descricao) -> setCellValue("F$x", $t -> justificativa) -> setCellValue("G$x", $t -> valor_previsto);
				$x++;
			}
			$y = $x;
			$x = $x - 1;
			$styleArray = array('borders' => array('allBorders' => array('borderStyle' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => '00000000'), ), ), );

			$spreadsheet -> getActiveSheet() -> mergeCells('A' . $y . ':F' . $y);
			$spreadsheet -> getActiveSheet() -> getStyle("A$y") -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$spreadsheet -> getActiveSheet() -> getStyle('A' . $y . ':G' . $y) -> getFont() -> setBold(true);
			$spreadsheet -> getActiveSheet() -> getStyle('A1:G' . $y) -> applyFromArray($styleArray);
			$spreadsheet -> getActiveSheet() -> getStyle('A1');
			$spreadsheet -> getActiveSheet() -> setCellValue("A$y", 'TOTAL');
			$spreadsheet -> getActiveSheet() -> setCellValue("G$y", '=SUM(G2:G' . $x . ')');
		}

		// Rename worksheet
		$spreadsheet -> getActiveSheet() -> setTitle('SPO - ' . $this -> session -> userdata('anofiscal'));

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet -> setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01spopriorizada.xlsx"');
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

		$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
		ob_end_clean();
		$writer -> save('php://output');
		exit ;

	}

	public function addAmount($id) {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'fConselho')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_allocate') . ' ' . $this -> lang -> line('amount'));
			redirect(base_url());
		}

		$data = array('button' => '<i class="fa fa-plus"></i> ' . $this -> lang -> line('app_create'), 'action' => site_url('priorizar/addamount_action'), 'IdProjeto' => set_value('IdProjeto', $id), 'valor' => set_value('valor'), 'tipo_despesa' => set_value('tipo_despesa'), 'data_autorizacao' => set_value('data_autorizacao'), 'ano_autorizacao' => $this -> session -> userdata('anofiscal'), );

		$data['view'] = 'priorizar/recursos_form';
		$this -> load -> view('tema/topo', $data, false);

	}

	function addamount_action() {

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'fConselho')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_allocate') . ' ' . $this -> lang -> line('amount'));
			redirect(base_url());
		}

		$this -> _rules();
		if ($this -> form_validation -> run() == false) {
			$this -> addAmount($this -> input -> post('IdProjeto'));
		} else {

			$data_autorizacao = $this -> input -> post('data_autorizacao');

			if ($data_autorizacao == null) {
				$data_autorizacao = date('d/m/Y');
			}

			try {
				$data_autorizacao = explode('/', $data_autorizacao);
				$data_autorizacao = $data_autorizacao[2] . '-' . $data_autorizacao[1] . '-' . $data_autorizacao[0];
			} catch (exception $e) {
				$data_autorizacao = date('Y-m-d');
			}

			$valor = $this -> input -> post('valor');

			if (!validate_money($valor)) {
				$valor = str_replace(",", ".", str_replace(".", "", $valor));
			}

			$data = array('valor' => $valor, 'data_autorizacao' => $data_autorizacao, 'projeto_id' => $this -> input -> post('IdProjeto'), 'tipo_despesa' => $this -> input -> post('tipo_despesa'), 'om_id' => $this -> session -> userdata('om_id'), 'ano_autorizacao' => $this -> session -> userdata('anofiscal'), );

			$this -> load -> model('Autorizacoes_model');

			if ($this -> Autorizacoes_model -> insert($data) == true) {
				$this -> distribuirRecursos($this -> input -> post('IdProjeto'), $this -> input -> post('tipo_despesa'));
				$this -> session -> set_flashdata('success', $this -> lang -> line('app_allocate_success'));
				redirect('priorizar');
			} else {
				$this -> session -> set_flashdata('error', $this -> lang -> line('app_error'));
			}
		}
	}

	public function delAmount($id) {
		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'fConselho')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_remove') . ' ' . $this -> lang -> line('amount'));
			redirect(base_url());
		}

		$data = array('button' => '<i class="fa fa-minus"></i> ' . $this -> lang -> line('app_remove'), 'action' => site_url('priorizar/delamount_action'), 'IdProjeto' => set_value('IdProjeto', $id), 'valor' => set_value('valor'), 'tipo_despesa' => set_value('tipo_despesa'), 'data_autorizacao' => set_value('data_autorizacao'), 'ano_autorizacao' => $this -> session -> userdata('anofiscal'), );

		$data['view'] = 'priorizar/recursos_form';
		$this -> load -> view('tema/topo', $data, false);

	}

	function delamount_action() {

		if (!$this -> permission -> check($this -> session -> userdata('permissao'), 'fConselho')) {
			$this -> session -> set_flashdata('error', $this -> lang -> line('app_permission_remove') . ' ' . $this -> lang -> line('amount'));
			redirect(base_url());
		}

		$this -> _rules();
		if ($this -> form_validation -> run() == false) {
			$this -> delAmount($this -> input -> post('IdProjeto'));
		} else {

			$data_autorizacao = $this -> input -> post('data_autorizacao');

			if ($data_autorizacao == null) {
				$data_autorizacao = date('d/m/Y');
			}

			try {
				$data_autorizacao = explode('/', $data_autorizacao);
				$data_autorizacao = $data_autorizacao[2] . '-' . $data_autorizacao[1] . '-' . $data_autorizacao[0];
			} catch (exception $e) {
				$data_autorizacao = date('Y-m-d');
			}

			$valor = $this -> input -> post('valor');

			if (!validate_money($valor)) {
				$valor = '-' . str_replace(",", ".", str_replace(".", "", $valor));
			}

			$data = array('valor' => $valor, 'data_autorizacao' => $data_autorizacao, 'projeto_id' => $this -> input -> post('IdProjeto'), 'tipo_despesa' => $this -> input -> post('tipo_despesa'), 'om_id' => $this -> session -> userdata('om_id'), 'ano_autorizacao' => $this -> session -> userdata('anofiscal'), );

			$this -> load -> model('Autorizacoes_model');

			if ($this -> Autorizacoes_model -> insert($data) == true) {
				$this -> distribuirRecursos($this -> input -> post('IdProjeto'), $this -> input -> post('tipo_despesa'));
				$this -> session -> set_flashdata('success', $this -> lang -> line('app_remove_success'));
				redirect('priorizar');
			} else {
				$this -> session -> set_flashdata('error', $this -> lang -> line('app_error'));
			}
		}
	}

	public function _rules() {
		$this -> form_validation -> set_rules('valor', '<b>' . $this -> lang -> line('value') . '</b>', 'trim|required');
		$this -> form_validation -> set_rules('data_autorizacao', '<b>' . $this -> lang -> line('date') . '</b>', 'trim|required');
		$this -> form_validation -> set_error_delimiters('<span class="text-danger">', '</span>');
	}

}

/* End of file Priorizar.php */
/* Location: ./application/controllers/Priorizar.php */
