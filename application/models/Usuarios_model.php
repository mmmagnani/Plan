<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Usuarios_model extends MY_Model
{

    public $table = 'usuarios';
    public $primary_key = 'IdUsuarios';
    public $select_column = array('IdUsuarios', 'om_id','setor_id', 'usuarios.nome', 'cpf', 'email', 'senha', 'telefone', 'usuarios.situacao', 'dataCadastro', 'permissoes_id', 'permissoes.nome as permissao');

    public $order_column = array('IdUsuarios', 'usuarios.nome', 'usuarios.situacao', 'permissao',null);
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_one['permissao'] = array('Permissoes_model','IdPermissao','permissoes_id');
		$this->has_one['setor'] = array('Setores_model', 'IdSetor', 'setor_id');
    }

    public function get_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
        $this->db->join('permissoes', 'permissoes.IdPermissao = permissoes_id', 'left');
        if($this->session->userdata('om_id') != 1) {
			$this->db->where('om_id', $this->session->userdata('om_id'));
			$this->db->where('setor_id <> 1');
		}
        if ((isset($_POST["search"]["value"])) && (!empty($_POST["search"]["value"]))){
            $this->db->like("usuarios.nome", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('IdUsuarios', 'ASC');
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

    public function total_rows($q = null)
    {
        if ($q) {

            $this->db->like('IdUsuarios', $q);
            $this->db->or_like('usuarios.nome', $q);
        }
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_limit_data($limit, $start = 0, $q = null)
    {
        $this->db->order_by($this->primary_key, $this->order);
        if ($q) {

            $this->db->like('IdUsuarios', $q);
            $this->db->or_like('usuarios.nome', $q);
        }

        $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }
	public function selectSetor($om_id = null)
    {
		if($om_id) {
		$this->db->where('om_id', $om_id);
		}
		$this->db->where('situacao', 1);
		$this->order_by('descricao');
		return $this->db->get('setores')->result();
	}

}

/* End of file Usuarios_model.php */
/* Location: ./application/models/Usuarios_model.php */
