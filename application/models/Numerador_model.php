<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Numerador_model extends MY_Model
{

    public $table = 'tb_numerador';
    public $primary_key = 'id_num';
    public $select_column = array('id_num', 'num', 'ano_num', 'tipo_num');

    public $order_column = array('id_num', 'num');
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
            $this->db->like("id_num", $_POST["search"]["value"]);
            $this->db->or_like("num", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('id_num', 'ASC');
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
	
	function get_new_numCautela($type)
	{
		$numnovo = "001/".$this->session->userdata('anaofiscal');
		$this->db->query('LOCK TABLES tb_numerador WRITE');
		$this->db->select('MAX(num) AS num');
		$this->db->where('tipo_num', $type);
		$this->db->where('ano_num', $this->session->userdata('anofiscal'));
		$this->db->limit(1);
		$row = $this->db->get('tb_numerador')->row();
		if(isset($row))
		{
			$newNum = intval($row->num) +1;
			$numplus = '"' . sprintf("%03s", $newNum) . '"';
			$numnovo = preg_replace('/(\'|")/', '', $numplus) . "/" . $this->session->userdata('anofiscal');
			$this->db->query("INSERT INTO tb_numerador (num,ano_num,tipo_num) VALUES (" . $numplus . "," . $this->session->userdata('anofiscal') . "," . $type . ")");
			$this->db->query('UNLOCK TABLES');
		}
		return $numnovo;
	}

}

/* End of file Numerador_model.php */
/* Location: ./application/models/Numerador_model.php */
