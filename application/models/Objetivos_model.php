<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Objetivos_model extends MY_Model
{

    public $table = 'objetivos';
    public $primary_key = 'IdObjetivo';
    public $select_column = array('IdObjetivo', 'om_id', 'descricao', 'perspectiva_id', 'perspectiva.nome AS perspectiva', 'situacao');

    public $order_column = array(null, 'IdObjetivo', 'descricao', 'perspectiva');
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
		$this->has_one['perspectiva'] = array('Perspectiva_model','IdPerspectiva','perspectiva_id');
    }

    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
		$this->db->join('perspectiva', 'objetivos.perspectiva_id = perspectiva.IdPerspectiva', 'left');
		$this->db->where('objetivos.om_id', $this->session->userdata('om_id'));
		$this->db->where('objetivos.situacao', 1);
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
			$this->db->group_start();
            $this->db->like("descricao", $_POST["search"]["value"]);
			$this->db->or_like("perspectiva.nome", $_POST["search"]["value"]);
			$this->db->group_end();
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('IdObjetivo', 'ASC');
        }
    }

    public function get_datatables()
    {
        $this->get_query();
        if ($_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_filtered_data()
    {
        $this->get_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_all_data()
    {
        $this->db->select("*");
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function delete_many($items)
    {
		$data = array('situacao' => 0);
        $this->db->where_in($this->primary_key, $items);
        return $this->db->update($this->table, $data);
    }
	

	function pesquisar($termo)
    {
        $data = array();
        // buscando objetivos
        $this->db->like('descricao', $termo);
        $this->db->where('om_id', $this->session->userdata('om_id'));
        $this->db->limit(10);
        return $this->db->get($this->table)->result();
	}
	
	public function get_all_by_om()
	{
		$this->get_query();
		$query = $this->db->get();
		if($query->num_rows() > 0)
            {
                $data = $query->result_array();
                $data = $this->trigger('after_get', $data);
                $data = $this->_prep_after_read($data,TRUE);
                $this->_write_to_cache($data);
                return $data;
            }
            else
            {
                return FALSE;
            }
	}

}

/* End of file Objetivos_model.php */
/* Location: ./application/models/Objetivos_model.php */
