<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Calendario_model extends MY_Model
{

    public $table = 'calendario';
    public $primary_key = 'idCalendario';
    public $select_column = array('idCalendario', 'ano_calendario', 'calendario.objeto', 'calendario.gerset', 'calendario.prazo_env_gerset', 'calendario.prazo_ent_gap', 'calendario.om_id', 'om.sigla AS sigla', 'data_ent_gap', 'calendario.valor_estimado', 'calendario.homol_pretendida', 'calendario.homol_estimada', 'atraso', 'tipo', 'tipo_tb.tipo_desc AS tipo', 'homol_efetiva', 'status_id', 'calendario.observacao', 'calendario.situacao', 'status_tb.status_desc AS status');

    public $order_column = array('sigla', 'objeto', 'gerset', 'prazo_ent_gap', 'homol_pretendida', 'status_desc', 'situacao');
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
		
		$this->has_one['tipo'] = array('Tipo_model','idTipo','tipo');
		$this->has_one['status'] = array('Status_model', 'idStatus', 'status_id');
		$this->has_one['om'] = array('Om_model', 'IdOm', 'om_id');
    }

    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
		$this->db->join('tipo_tb', 'calendario.tipo = tipo_tb.idTipo', 'left');
		$this->db->join('status_tb', 'calendario.status_id = status_tb.idStatus', 'left');
		$this->db->join('om', 'calendario.om_id = om.IdOm', 'left');
		$this->db->where('calendario.situacao', 1);
		$this->db->where('calendario.ano_calendario', $this->session->userdata('anofiscal'));
		$this->db->group_start();
		$this->db->where('om.om_id_apoiadora',$this->session->userdata('om_id'));
		$this->db->or_where('calendario.om_id', $this->session->userdata('om_id'));
		$this->db->group_end();
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
			$this->db->group_start();
			$this->db->like("calendario.objeto", $_POST["search"]["value"]);
			$this->db->or_like("om.sigla", $_POST["search"]["value"]);
			$this->db->group_end();
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('idCalendario', 'ASC');
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
	
	function getGraph($id)
	{
		$sql = "SELECT calendario.om_id, om.sigla, calendario.gerset, calendario.prazo_env_gerset, calendario.prazo_ent_gap, calendario.data_ent_gap, calendario.homol_pretendida, calendario.homol_estimada, calendario.atraso, calendario.tipo, calendario.homol_efetiva, (SELECT om_tmp.sigla FROM om AS om_tmp WHERE om_tmp.idOm = om.om_id_apoiadora) AS omapoiadora FROM calendario INNER JOIN om ON calendario.om_id = om.IdOm WHERE calendario.idCalendario = " . $id;
		return $this->db->query($sql)->row();		
	}
	
	function getSiglaApoiadora($id)
	{
	    $this->db->select('sigla');
		$this->db->where('IdOm', $id);
		return $this->db->get('om')->row();
	}
	
	function export_calendar($limite)
	{
		$this->db->select('om.sigla, calendario.*, status_tb.status_desc');
		$this->db->from($this->table);
		$this->db->join('om', 'calendario.om_id = om.IdOm', 'left');
		$this->db->join('status_tb', 'calendario.status_id = status_tb.idStatus', 'left');
		$this->db->where('calendario.ano_calendario', $this->session->userdata('anofiscal'));
		$this->db->where('calendario.situacao', 1);
		if($this->session->userdata('om_id') != $this->session->userdata('apoiadora')){
			$this->db->where('calendario.om_id', $this->session->userdata('om_id'));
		}
		$this->db->order_by('om.sigla');
		$this->db->limit($limite);
		return $this->db->get()->result();
	}

}

/* End of file Calendario_model.php */
/* Location: ./application/models/Calendario_model.php */
