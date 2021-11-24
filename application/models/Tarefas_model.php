<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Tarefas_model extends MY_Model
{

    public $table = 'tarefas';
    public $primary_key = 'IdTarefa';
    public $select_column = array('tarefas.IdTarefa', 'tarefas.spo_id', 'tarefas.om_id', 'tarefas.ano', 'tarefas.setor_id', 'tarefas.projeto_id', 'tarefas.titulo', 'tarefas.descricao', 'tarefas.justificativa', 'tarefas.valor_previsto', 'tarefas.valor_autorizado', 'tarefas.valor_executado', 'tarefas.`status`', 'tarefas.situacao', 'tarefas.prioridade', 'tarefas.checkBoxObjeto', 'projetos.titulo AS projeto', 'setores.sigla AS sigla', 'setores_gerset.sigla AS gerset');

    public $order_column = array(null, 'spo_id', 'tarefas.titulo', 'valor_previsto', 'valor_autorizado', 'valor_executado');
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();	
		$this->has_one['projetos'] = array('Projetos_model', 'IdProjeto', 'projeto_id');	
		$this->has_one['setores'] = array('Setores_model','IdSetor','setor_id');
    }

    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
		$this->db->join('setores', 'tarefas.setor_id = setores.IdSetor', 'left');
		$this->db->join('projetos', 'tarefas.projeto_id = projetos.IdProjeto', 'left');
		$this->db->join('gersets', 'projetos.gerset_id = gersets.IdGerset', 'left');
		$this->db->join('setores AS setores_gerset', 'gersets.setor_id = setores_gerset.IdSetor', 'left');
		$this->db->where('tarefas.om_id', $this->session->userdata('om_id'));
		$this->db->where('tarefas.ano', $this->session->userdata('anofiscal'));
		$this->db->where('tarefas.situacao', 1);
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
			$this->db->group_start();
            $this->db->like("tarefas.titulo", $_POST["search"]["value"]);
			$this->db->or_like("tarefas.spo_id", $_POST["search"]["value"]);
			$this->db->group_end();
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
			$this->db->order_by('-(prioridade)', 'DESC');
            $this->db->order_by('spo_id', 'ASC');
        }
    }

    public function get_datatables($id)
    {
        $this->get_query();
		
		if ($this->permission->check($this->session->userdata('permissao'), 'fConselho')) {
		//
		} else if($this->permission->check($this->session->userdata('permissao'), 'fAdministrador')) {
		//	
		} else if($this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
			$this->db->where('gersets.setor_id', $this->session->userdata('setor_id'));	
		} else {
			$this->db->where('tarefas.setor_id', $this->session->userdata('setor_id'));		  
		}
		if ($id != NULL) {
			$this->db->where('projeto_id', $id);
		}
        if ($_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_filtered_data($id)
    {
        $this->get_query();
		if ($this->permission->check($this->session->userdata('permissao'), 'fConselho')) {
		//
		} else if($this->permission->check($this->session->userdata('permissao'), 'fAdministrador')) {
		//	
		} else if($this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
			$this->db->where('gersets.setor_id', $this->session->userdata('setor_id'));	
		} else {
			$this->db->where('tarefas.setor_id', $this->session->userdata('setor_id'));		  
		}

		if ($id != NULL) {
			$this->db->where('projeto_id', $id);
		}
		
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_all_data($id)
    {
        $this->db->select("*");
        $this->db->from($this->table);
		if ($this->permission->check($this->session->userdata('permissao'), 'fConselho')) {
		//
		} else if($this->permission->check($this->session->userdata('permissao'), 'fAdministrador')) {
		//	
		} else if($this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
			//	
		} else {
			$this->db->where('tarefas.setor_id', $this->session->userdata('setor_id'));		  
		}

		if ($id != NULL) {
		$this->db->where('projeto_id', $id);
		}
		$this->db->where('tarefas.ano', $this->session->userdata('anofiscal'));
		$this->db->where('tarefas.om_id', $this->session->userdata('om_id'));
        return $this->db->count_all_results();
    }

    public function delete_many($items)
    {
        $this->db->where_in($this->primary_key, $items);
        return $this->db->delete($this->table);
    }
	
	public function get_tarefas($where = NULL, $limit = NULL)
	{
		$this->get_query();
		if ($where) {
			$this->db->where($where);
		}
		if ($limit) {
			$this->db->limit($limit);
		}
		return $this->db->get()->result();
	}
	

	function pesquisar($termo)
    {
        $data = array();
		$this->db->select('*, setores.sigla AS setor');
        $this->db->join('setores', 'tarefas.setor_id = setores.IdSetor');
        $this->db->where('tarefas.om_id', $this->session->userdata('om_id'));
        $this->db->where('tarefas.ano', $this->session->userdata('anofiscal'));
        $this->db->like('titulo', $termo);
		$this->db->or_like('spo_id', $termo);
		$this->db->or_like('sigla', $termo);
        $this->db->limit(10);
        return $this->db->get($this->table)->result();
	}
	
	function uid($tc = false, $l = 11, $cod)
    {
        $query = $this->db->query("SELECT COUNT(*) AS " . $this->db->escape_identifiers
            ('numrows') . " FROM " . $tc . " WHERE spo_id LIKE '" . $cod . "%' LIMIT 1");
        if ($query->num_rows() === 0)
        {
            $uid = '1';
            $uid = str_pad($uid, $l, '0', STR_PAD_LEFT);
            return $uid;
        }
        $query = $query->row();
        $uid = $query->numrows;
        $uid = ++$uid;
        $uid = str_pad($uid, $l, '0', STR_PAD_LEFT);
        return $uid;
    }
	
	function getCodOm()
    {
        $this->db->where('IdOm', $this->session->userdata('om_id'));
        $this->db->limit(1);
        $query = $this->db->get('om')->row();
        return $query->codigo;
    }
	
	public function getBloqueio()
    {
        $this->db->where('om_id', $this->session->userdata('om_id'));
        return $this->db->get('config')->row();
    }
	
	function somaEmpenhosByTarefa($id)
    {
        $this->db->select('Sum(valor_empenho) AS valor_executado');
        $this->db->where('tarefa_id', $id);
        $this->db->group_by('tarefa_id');
        return $this->db->get('execucoes')->row();
    }
	
	public function getAbrangenciainOM($table, $fields, $order = '')
    {

        $this->db->select($fields);
        $this->db->from($table);
		$this->db->join('gersets', 'projetos.gerset_id = gersets.IdGerset', 'left');
        $this->db->where('projetos.situacao', 1);
        $this->db->where('projetos.om_id', $this->session->userdata('om_id'));
        $this->db->where('abrangencia', 0);
		$this->db->where('gersets.setor_id', $this->session->userdata('setor_id'));
        $this->db->order_by($order, 'asc');
        $query = $this->db->get();
        return $query->result();
    
    }
	
	public function getActiveinOM($table, $fields, $order = '')
    {

        $this->db->select($fields);
        $this->db->from($table);
        $this->db->where('situacao', 1);
        $this->db->where('om_id', $this->session->userdata('om_id'));
		$this->db->where('abrangencia', 1);
        $this->db->order_by($order, 'asc');
        $query = $this->db->get();
        return $query->result();
		
    }
	
	public function getCATMAT($where)
	{
		if($where != ''){
		$this->db->where($where);
		}
		$query = $this->db->get('catmat');
		return $query->result();
	}
	
	public function searchCATMAT($termo)
	{
		
		$this->db->select("*, MATCH(descPDM, descCATMAT) AGAINST ('".$termo."*' IN BOOLEAN MODE) AS relevancia");
		$this->db->where("MATCH(descPDM, descCATMAT) AGAINST ('".$termo."*' IN BOOLEAN MODE)");
		$this->db->order_by('relevancia', 'desc');
		$this->db->order_by('descCATMAT', 'asc');
		$this->db->limit(50);
		$query = $this->db->get('catmat');
		return $query->result();
	}
		

}

/* End of file Tarefas_model.php */
/* Location: ./application/models/Tarefas_model.php */
