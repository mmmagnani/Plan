<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Om_model extends MY_Model
{

    public $table = 'om';
    public $primary_key = 'IdOm';
    public $select_column = array('IdOm', 'sigla',  'nome', 'situacao', 'codigo', 'apoiadora', 'om_id_apoiadora');

    public $order_column = array('IdOm', 'sigla', 'nome', 'codigo', 'situacao');
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
    }
	
    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
		if ($this->session->userdata('om_id') != 1)
        {
            $this->db->where('idOm', $this->session->userdata('om_id'));
        }
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
            $this->db->like("sigla", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('IdOm', 'ASC');
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
	
	public function get_om()
	{
		$this->db->where('IdOm', $this->session->userdata('om_id'));
		return $this->db->get('om')->row();
	}


}

/* End of file Om_model.php */
/* Location: ./application/models/Om_model.php */
