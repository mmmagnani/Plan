<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Empenhos_model extends MY_Model
{

    public $table = 'execucoes';
    public $primary_key = 'IdExecucao';
    public $select_column = array('IdExecucao', 'execucoes.om_id', 'execucoes.tarefa_id', 'empenho', 'data_empenho', 'valor_empenho');

    public $order_column = array(null, 'empenho', 'data_empenho', 'valor_empenho');
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();	
    }

    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
		$this->db->where('execucoes.om_id', $this->session->userdata('om_id'));
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
            $this->db->like("empenho", $_POST["search"]["value"]);
			$this->db->or_like("data_empenho", $_POST["search"]["value"]);
			$this->db->or_like("valor_empenho", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('empenho', 'ASC');
        }
    }

    public function get_datatables($id)
    {
        $this->get_query();
		if ($id != NULL) {
			$this->db->where('tarefa_id', $id);
		}
        if ($_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_filtered_data($id)
    {
        $this->get_query();
		if ($id != NULL) {
			$this->db->where('tarefa_id', $id);
		}
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_all_data($id)
    {
        $this->db->select("*");
        $this->db->from($this->table);
		if ($id != NULL) {
			$this->db->where('tarefa_id', $id);
		}
        return $this->db->count_all_results();
    }

    public function delete_many($items)
    {
        $this->db->where_in($this->primary_key, $items);
        return $this->db->delete($this->table);
    }
	

	function pesquisar($termo)
    {
        $data = array();
        // buscando objetivos
        $this->db->like('empenho', $termo);
        $this->db->where('om_id', $this->session->userdata('om_id'));
        $this->db->limit(10);
        return $this->db->get($this->table)->result();
	}

}

/* End of file Empenhos_model.php */
/* Location: ./application/models/Empenhos_model.php */
