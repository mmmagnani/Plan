<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Perspectiva_model extends MY_Model
{

    public $table = 'perspectiva';
    public $primary_key = 'IdPerspectiva';
    public $select_column = array('IdPerspectiva', 'nome');

    public $order_column = array('IdPerspectiva', 'nome');
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
            $this->db->like("IdPerspectiva", $_POST["search"]["value"]);
            $this->db->or_like("nome", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('IdPerspectiva', 'ASC');
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


}

/* End of file Perspectiva_model.php */
/* Location: ./application/models/Perspectiva_model.php */
