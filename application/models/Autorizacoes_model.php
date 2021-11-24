<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class Autorizacoes_model extends MY_Model {

	public $table = 'autorizacoes';
	public $primary_key = 'IdAutorizacao';
	public $select_column = array('IdAutorizacao', 'autorizacoes.om_id', 'autorizacoes.projeto_id', 'data_autorizacao', 'autorizacoes.valor');

	public $order_column = array('IdAutorizacao', 'om_id', 'projeto_id', 'data_autorizacao', 'valor');
	public $timestamps = false;

	public function __construct() {
		parent::__construct();
	}

	public function get_query() {
		$this -> db -> select($this -> select_column);
		$this -> db -> from($this -> table);
		if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))) {
			$this -> db -> like("IdAutorizacao", $_POST["search"]["value"]);
		}
		if (isset($_POST["order"])) {
			$this -> db -> order_by($this -> order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else {
			$this -> db -> order_by('IdAutorizacao', 'ASC');
		}
	}

	public function get_datatables() {
		$this -> get_query();
		if ($_POST["length"] != -1) {
			$this -> db -> limit($_POST['length'], $_POST['start']);
		}
		$query = $this -> db -> get();
		return $query -> result();
	}

	public function get_filtered_data() {
		$this -> get_query();
		$query = $this -> db -> get();
		return $query -> num_rows();
	}

	public function get_all_data() {
		$this -> db -> select("*");
		$this -> db -> from($this -> table);
		return $this -> db -> count_all_results();
	}

}

/* End of file Autorizacoes_model.php */
/* Location: ./application/models/Autorizacoes_model.php */
