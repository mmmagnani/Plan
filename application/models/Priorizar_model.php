<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class Priorizar_model extends MY_Model {

	public $table = 'projetos';
	public $primary_key = 'IdProjeto';
	public $select_column = array('IdProjeto', 'projetos.om_id', 'objetivo_id', 'projetos.titulo', 'projetos.descricao', 'gerset_id', 'abrangencia', 'obras', 'projetos.situacao', 'setores.sigla AS gerset', 'Sum(tarefas.valor_previsto) AS estimado', 'Sum(tarefas.valor_executado) AS executado');

	public $order_column = array('titulo', 'estimado', 'autorizado', 'gerset');
	public $timestamps = false;

	public function __construct() {
		parent::__construct();
		$this -> has_one['gersets'] = array('Gersets_model', 'IdGerset', 'gerset_id');
	}

	public function get_query() {
		$this -> select_column[] = '(SELECT Sum(autorizacoes.valor) FROM autorizacoes WHERE autorizacoes.ano_autorizacao = ' . $this -> session -> userdata('anofiscal') . ' AND autorizacoes.projeto_id = projetos.IdProjeto AND autorizacoes.tipo_despesa = "p") AS autorizadop';
		$this -> select_column[] = '(SELECT Sum(autorizacoes.valor) FROM autorizacoes WHERE autorizacoes.ano_autorizacao = ' . $this -> session -> userdata('anofiscal') . ' AND autorizacoes.projeto_id = projetos.IdProjeto AND autorizacoes.tipo_despesa <> "p") AS autorizado';
		$this -> db -> select($this -> select_column);
		$this -> db -> from($this -> table);
		$this -> db -> join('gersets', 'projetos.gerset_id = gersets.IdGerset', 'left');
		$this -> db -> join('setores', 'gersets.setor_id = setores.IdSetor', 'left');
		$this -> db -> join('tarefas', 'tarefas.projeto_id = projetos.IdProjeto', 'left');
		$this -> db -> where('projetos.om_id', $this -> session -> userdata('om_id'));
		$this -> db -> where('tarefas.situacao', 1);
		$this -> db -> where('tarefas.ano', $this -> session -> userdata('anofiscal'));
		$this -> db -> group_by('projetos.titulo');
		if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))) {
			$this -> db -> group_start();
			$this -> db -> like("projetos.titulo", $_POST["search"]["value"]);
			$this -> db -> or_like("setores.sigla", $_POST["search"]["value"]);
			$this -> db -> group_end();
		}
		if (isset($_POST["order"])) {
			$this -> db -> order_by($this -> order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else {
			$this -> db -> order_by('projetos.titulo', 'ASC');
		}
	}

	public function get_datatables($where = NULL) {
		$this -> get_query();
		if ($this -> permission -> check($this -> session -> userdata('permissao'), 'fGerset')) {
			$this -> db -> where('gersets.setor_id', $this -> session -> userdata('setor_id'));
		}
		if ($_POST["length"] != -1) {
			$this -> db -> limit($_POST['length'], $_POST['start']);
		}
		$query = $this -> db -> get();
		return $query -> result();
	}

	public function get_data($where = NULL) {
		$this -> get_query();
		if (isset($where)) {
			$this -> db -> where($where);
		}
		$query = $this -> db -> get();
		return $query -> result();
	}

	public function get_filtered_data() {
		$this -> get_query();
		if ($this -> permission -> check($this -> session -> userdata('permissao'), 'fGerset')) {
			$this -> db -> where('gersets.setor_id', $this -> session -> userdata('setor_id'));
		}
		$query = $this -> db -> get();
		return $query -> num_rows();
	}

	public function get_all_data() {
		$this -> db -> select("*");
		$this -> db -> from($this -> table);

		return $this -> db -> count_all_results();
	}

	public function delete_many($items) {
		$data = array('situacao' => 0);
		$this -> db -> where_in($this -> primary_key, $items);
		return $this -> db -> update($this -> table, $data);
	}

	function pesquisar($termo) {
		$data = array();
		// buscando objetivos
		$this -> db -> like('descricao', $termo);
		$this -> db -> where('om_id', $this -> session -> userdata('om_id'));
		$this -> db -> limit(10);
		return $this -> db -> get($this -> table) -> result();
	}

	public function get_all_by_om() {
		$this -> get_query();
		$query = $this -> db -> get();
		if ($query -> num_rows() > 0) {
			$data = $query -> result_array();
			$data = $this -> trigger('after_get', $data);
			$data = $this -> _prep_after_read($data, TRUE);
			$this -> _write_to_cache($data);
			return $data;
		} else {
			return FALSE;
		}
	}

	public function get_all_by_abrangencia() {
		$this -> get_query();
		$this -> db -> where('abrangencia', 1);
		$query = $this -> db -> get();
		if ($query -> num_rows() > 0) {
			$data = $query -> result_array();
			$data = $this -> trigger('after_get', $data);
			$data = $this -> _prep_after_read($data, TRUE);
			$this -> _write_to_cache($data);
			return $data;
		} else {
			return FALSE;
		}
	}

	function checkAutorizados() {
		$sql = "(SELECT tarefas.ano, projetos.om_id, UPPER(projetos.titulo) AS objeto, (SELECT Sum(tarefas.valor_autorizado) FROM tarefas WHERE tarefas.projeto_id = projetos.IdProjeto AND tarefas.situacao = 1) AS valor_estimado FROM projetos INNER JOIN tarefas ON tarefas.projeto_id = projetos.IdProjeto WHERE projetos.om_id = " . $this -> session -> userdata('om_id') . " AND projetos.obras = 0 AND tarefas.ano = " . $this -> session -> userdata('anofiscal') . " AND tarefas.valor_autorizado > 0 AND projetos.situacao = 1 GROUP BY projetos.titulo ORDER BY projetos.om_id ASC, projetos.titulo ASC) UNION (SELECT tarefas.ano, projetos.om_id, UPPER(tarefas.titulo) AS objeto, tarefas.valor_autorizado AS valor_estimado FROM tarefas INNER JOIN projetos ON tarefas.projeto_id = projetos.IdProjeto WHERE projetos.om_id = " . $this -> session -> userdata('om_id') . " AND projetos.obras = 1 AND tarefas.ano = " . $this -> session -> userdata('anofiscal') . " AND tarefas.valor_autorizado > 0 AND projetos.situacao = 1 ORDER BY tarefas.titulo ASC)";
		$this -> db -> query($sql);
		if ($this -> db -> affected_rows() > 0) {
			return true;
		}

		return false;
	}

	function getMargemReserva() {
		$this -> db -> where('om_id', $this -> session -> userdata('om_id'));
		return $this -> db -> get('config') -> row();
	}

	function setOrdemPrioridade($list) {

		$arr_item = explode(",", $list);
		$ordem = 1;

		foreach ($arr_item as $arr_item) {
			$data = array('prioridade' => $ordem);
			$this -> db -> where('IdTarefa', $arr_item);
			$this -> db -> update('tarefas', $data);
			$ordem++;
		}
		return true;
	}

	public function getTarefasByProjeto($id, $tipodespesa) {
		$this -> db -> select('tarefas.*,setores.sigla');
		$this -> db -> where('tarefas.om_id', $this -> session -> userdata('om_id'));
		$this -> db -> where('tarefas.projeto_id', $id);
		$this -> db -> where('tarefas.ano', $this -> session -> userdata('anofiscal'));
		$this -> db -> where('tarefas.checkBoxObjeto', $tipodespesa);
		$this -> db -> from('tarefas');
		$this -> db -> join('setores', 'tarefas.setor_id = setores.IdSetor');
		$this -> db -> order_by('-(tarefas.prioridade)', 'desc');
		$this -> db -> order_by('tarefas.spo_id', 'asc');
		return $this -> db -> get() -> result();
	}

	function inputupdateCal() {
		$sql = "INSERT INTO calendario (ano_calendario, om_id, objeto, valor_estimado) (SELECT tarefas.ano, projetos.om_id, UPPER(projetos.titulo) AS objeto, (SELECT Sum(tarefas.valor_autorizado) FROM tarefas WHERE tarefas.projeto_id = projetos.IdProjeto AND tarefas.situacao = 1) AS valor_estimado FROM projetos INNER JOIN tarefas ON tarefas.projeto_id = projetos.IdProjeto WHERE projetos.om_id = " . $this -> session -> userdata('om_id') . " AND projetos.obras = 0 AND projetos.calendario = 1 AND tarefas.ano = " . $this -> session -> userdata('anofiscal') . " AND tarefas.valor_autorizado > 0 AND projetos.situacao = 1 GROUP BY projetos.titulo ORDER BY projetos.om_id ASC, projetos.titulo ASC) UNION (SELECT tarefas.ano, projetos.om_id, UPPER(tarefas.titulo) AS objeto, tarefas.valor_autorizado AS valor_estimado FROM tarefas INNER JOIN projetos ON tarefas.projeto_id = projetos.IdProjeto WHERE projetos.om_id = " . $this -> session -> userdata('om_id') . " AND projetos.obras = 1 AND tarefas.ano = " . $this -> session -> userdata('anofiscal') . " AND tarefas.valor_autorizado > 0 AND projetos.situacao = 1 ORDER BY tarefas.titulo ASC) ON DUPLICATE KEY UPDATE  ano_calendario = VALUES(ano_calendario), om_id = VALUES(om_id), objeto = VALUES(objeto), valor_estimado = VALUES(valor_estimado)";
		$this -> db -> query($sql);
		if ($this -> db -> affected_rows() >= 0) {
			return true;
		}

		return false;
	}

	function setValorAutorizado($id, $valor, $status) {
		$this -> db -> where('IdTarefa', $id);
		$this -> db -> update('tarefas', array('valor_autorizado' => $valor, 'status' => $status));
	}

	function getAutorizadoByProjeto($id, $tipodespesa) {
		$this -> db -> select('Sum(autorizacoes.valor) AS valor_autorizado');
		$this -> db -> where('autorizacoes.projeto_id', $id);
		$this -> db -> where('autorizacoes.ano_autorizacao', $this -> session -> userdata('anofiscal'));
		$this -> db -> where('autorizacoes.tipo_despesa', $tipodespesa);
		$this -> db -> group_by('autorizacoes.projeto_id');
		return $this -> db -> get('autorizacoes') -> row();
	}

}

/* End of file Priorizar_model.php */
/* Location: ./application/models/Priorizar_model.php */
