<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Projetos_model extends MY_Model
{

    public $table = 'projetos';
    public $primary_key = 'IdProjeto';
    public $select_column = array('IdProjeto', 'projetos.om_id', 'objetivo_id', 'titulo', 'projetos.descricao', 'gerset_id', 'abrangencia', 'obras', 'projetos.situacao', 'setores.sigla');

    public $order_column = array(null, 'IdProjeto', 'titulo', 'descricao');
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();		
		$this->has_one['gersets'] = array('Gersets_model','IdGerset','gerset_id');
    }

    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
		$this->db->join('gersets', 'projetos.gerset_id = gersets.IdGerset', 'left');
		$this->db->join('setores', 'gersets.setor_id = setores.IdSetor', 'left');
		$this->db->where('projetos.om_id', $this->session->userdata('om_id'));
		$this->db->where('projetos.situacao', 1);
		

		
		if (!$this->permission->check($this->session->userdata('permissao'), 'fAdministrador')) {
			if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
				$this->db->where('abrangencia', 0);
			} else {
				$this->db->where('abrangencia', 1);
			}
		}
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
		$this->db->group_start();
            $this->db->like("titulo", $_POST["search"]["value"]);
			$this->db->or_like("projetos.descricao", $_POST["search"]["value"]);
			$this->db->group_end();
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('titulo', 'ASC');
        }
    }

    public function get_datatables($id)
    {
        $this->get_query();
		if ($this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
		  $this->db->where('gersets.setor_id', $this->session->userdata('setor_id'));
		}
		if ($id != NULL) {
			$this->db->where('objetivo_id', $id);
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
		if ($this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
		  $this->db->where('gersets.setor_id', $this->session->userdata('setor_id'));
		}
		if ($id != NULL) {
			$this->db->where('objetivo_id', $id);
		}
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_all_data($id)
    {
        $this->db->select("*");
        $this->db->from($this->table);
		if ($id != NULL) {
			$this->db->where('objetivo_id', $id);
		}
		$this->db->where('projetos.om_id', $this->session->userdata('om_id'));
        return $this->db->count_all_results();
    }

    public function delete_many($items)
    {
		$data = array('situacao' => 0);
        $this->db->where_in($this->primary_key, $items);
        return $this->db->update($this->table, $data);
    }
	

	function pesquisar($termo)
    {
        $data = array();
        // buscando objetivos
        $this->db->like('descricao', $termo);
        $this->db->where('om_id', $this->session->userdata('om_id'));
        $this->db->limit(10);
        return $this->db->get($this->table)->result();
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
	
	public function get_all_by_abrangencia()
	{
		$this->db->select($this->select_column);
        $this->db->from($this->table);
		$this->db->join('gersets', 'projetos.gerset_id = gersets.IdGerset', 'left');
		$this->db->join('setores', 'gersets.setor_id = setores.IdSetor', 'left');
		$this->db->where('projetos.om_id', $this->session->userdata('om_id'));
		$this->db->where('projetos.situacao', 1);
		
		if ($this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
		  $this->db->where('gersets.setor_id', $this->session->userdata('setor_id'));
		}
		
		if (!$this->permission->check($this->session->userdata('permissao'), 'fAdministrador')) {
			if (!$this->permission->check($this->session->userdata('permissao'), 'fGerset')) {
				$this->db->where('abrangencia', 0);
			} else {
				$this->db->where('abrangencia', 1);
			}
		}
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
		$this->db->group_start();
            $this->db->like("titulo", $_POST["search"]["value"]);
			$this->db->or_like("projetos.descricao", $_POST["search"]["value"]);
			$this->db->group_end();
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('titulo', 'ASC');
        }

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

/* End of file Projetos_model.php */
/* Location: ./application/models/Projetos_model.php */
