<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Setores_model extends MY_Model
{

    public $table = 'setores';
    public $primary_key = 'IdSetor';
    public $select_column = array('setores.IdSetor', 'setores.om_id', 'setores.sigla', 'setores.descricao', 'setores.situacao');

    public $order_column = array('sigla', 'descricao', 'situacao');
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
			$this->db->where('setores.om_id', $this->session->userdata('om_id'));
		}
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
			$this->db->group_start();
            $this->db->like("setores.descricao", $_POST["search"]["value"]);
			$this->db->or_like("setores.sigla", $_POST["search"]["value"]);
			$this->db->group_end();
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('setores.sigla', 'ASC');
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

/* End of file Setores_model.php */
/* Location: ./application/models/Setores_model.php */
