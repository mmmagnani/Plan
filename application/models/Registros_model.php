<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Registros_model extends MY_Model
{

    public $table = 'indicadores';
    public $primary_key = 'IdIndicador';
    public $select_column = array('IdIndicador', 'indicadores.om_id', 'indicadores.descricao', 'formula', 'indicadores.objetivo', 'origem_dados', 'vantagem_sefa', 'vantagem_om', 'indicadores.gerset_id', 'periodicidade_id', 'tipoindicador_id', 'meta', 'meta2', 'unidade_meta', 'indicadores.projeto_id','indicadores.situacao');

    public $order_column = array('descricao', 'objetivo');
	 
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
		
		$this->has_one['projeto'] = array('Projetos_model', 'IdProjeto', 'projeto_id');
		$this->has_one['gerset'] = array('Gersets_model', 'IdGerset', 'gerset_id');
    }
	
    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
		$this->db->join('gersets', 'indicadores.gerset_id = gersets.IdGerset');
		$this->db->where('indicadores.situacao',1);
		$this->db->where('indicadores.om_id', $this->session->userdata('om_id'));
		$this->db->where('gersets.setor_id', $this->session->userdata('setor_id'));
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
			$this->db->group_start();
            $this->db->like("IdIndicador", $_POST["search"]["value"]);
			$this->db->or_like("indicadores.descricao", $_POST["search"]["value"]);
			$this->db->or_like("indicadores.objetivo", $_POST["search"]["value"]);
			$this->db->group_end();
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('IdIndicador', 'ASC');
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
	
	function getGraph($id, $filtro = '')
    {
        $sql = "SELECT EXTRACT(MONTH FROM registros.`data`) AS mes, registros.medicao, IF(indicadores.tipoindicador_id = 5, ROUND(indicadores.meta/12,2) * EXTRACT(MONTH FROM registros.`data`), indicadores.meta) AS meta, IFNULL(indicadores.meta2,0) AS meta2 FROM (registros INNER JOIN indicadores ON registros.indicador_id = indicadores.IdIndicador) WHERE registros.ano = " . $this->session->userdata('anofiscal') . " AND registros.indicador_id = " . $id . $filtro . " ORDER BY registros.`data` DESC LIMIT 12";

        return $this->db->query($sql)->result();
    }

}

/* End of file Indicadores_model.php */
/* Location: ./application/models/Indicadores_model.php */
