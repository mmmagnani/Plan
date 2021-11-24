<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Medicoes_model extends MY_Model
{

    public $table = 'registros';
    public $primary_key = 'IdRegistro';
    public $select_column = array('IdRegistro', 'indicador_id', 'registros.data', 'registros.observacao', 'medicao', 'registros.ano');

    public $order_column = array(null, 'data', 'observacao', 'medicao');
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
    }
	
    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
            $this->db->like("IdRegistro", $_POST["search"]["value"]);
			$this->db->or_like("registros.data", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('registros.data', 'ASC');
        }
    }

    public function get_datatables($id)
    {
        $this->get_query();
		$this->db->where('indicador_id', $id);
        if ($_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_filtered_data($id)
    {
        $this->get_query();
		$this->db->where('indicador_id', $id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_all_data($id)
    {
        $this->db->select("*");
		$this->db->where('indicador_id', $id);
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }	
	
	public function delete_many($items)
    {
        $this->db->where_in($this->primary_key, $items);
        return $this->db->delete($this->table);
    }
}

/* End of file Medicoes_model.php */
/* Location: ./application/models/Medicoes_model.php */
