<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Tipo_model extends MY_Model
{

    public $table = 'tipo_tb';
    public $primary_key = 'idTipo';
    public $select_column = array('idTipo', 'tipo_desc');

    public $order_column = array('idTipo', 'tipo_desc');
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
            $this->db->like("idTipo", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('idTipo', 'ASC');
        }
    }

}

/* End of file Tipo_model.php */
/* Location: ./application/models/Tipo_model.php */
