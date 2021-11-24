<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Gersets_model extends MY_Model
{

    public $table = 'gersets';
    public $primary_key = 'IdGerset';
    public $select_column = array('IdGerset', 'gersets.om_id', 'setor_id', 'gersets.situacao', 'setores.sigla AS sigla', 'setores.descricao AS descricao');

    public $order_column = array('sigla', 'descricao', 'situacao');
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
		$this->has_one['setores'] = array('Setores_model', 'IdSetor', 'setor_id');
    }
	
    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
		$this->db->join('setores', 'gersets.setor_id = setores.IdSetor', 'left');
		if($this->session->userdata('om_id') != 1) {
		  $this->db->where('gersets.om_id', $this->session->userdata('om_id'));
		}
		$this->db->where('gersets.situacao', 1);
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
            $this->db->like("descricao", $_POST["search"]["value"]);
			$this->db->or_like("sigla", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('sigla', 'ASC');
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

/* End of file Gersets_model.php */
/* Location: ./application/models/Gersets_model.php */
