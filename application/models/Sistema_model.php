<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Sistema_model extends MY_Model
{

    public $table = 'config';
    public $primary_key = 'IdConfig';
    public $select_column = array('IdConfig', 'config.om_id', 'margem_reserva', 'bloqueio', 'om.sigla AS sigla');

    public $order_column = array('IdConfig', 'sigla', 'margem_reserva', 'bloqueio');
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
    }
	
    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
		if($this->session->userdata('om_id') != 1) {
			$this->db->where('om_id', $this->session->userdata('om_id'));
		}
		$this->db->join('om', 'config.om_id = om.IdOm', 'left');
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
            $this->db->like("IdConfig", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('IdConfig', 'ASC');
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

/* End of file Sistema_model.php */
/* Location: ./application/models/Sistema_model.php */
