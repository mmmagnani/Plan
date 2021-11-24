<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plan_model extends CI_Model
{

    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */

    function __construct()
    {
        parent::__construct();
    }


    function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false,
        $array = 'array')
    {

        $this->db->select($fields);
        $this->db->from($table);
        $this->db->limit($perpage, $start);
        if ($where)
        {
            $this->db->where($where);
        }

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getById($id)
    {
        $this->db->from('usuarios');
        $this->db->select('usuarios.*, permissoes.nome as permissao');
        $this->db->join('permissoes', 'permissoes.IdPermissao = usuarios.permissoes_id',
            'left');
        $this->db->where('IdUsuarios', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function alterarSenha($newSenha, $oldSenha, $id)
    {

        $this->db->where('IdUsuarios', $id);
        $this->db->limit(1);
        $usuario = $this->db->get('usuarios')->row();
		$oldsenha = password_hash($oldSenha, PASSWORD_DEFAULT);
        if (password_verify($oldsenha, $usuario->senha))
        {
            return false;
        } else
        {
            $this->db->set('senha', password_hash($newSenha, PASSWORD_DEFAULT));
            $this->db->where('IdUsuarios', $id);
            return $this->db->update('tb_usuario');
        }


    }

    function pesquisar($termo)
    {
        $data = array();
        // buscando objetivos
        $this->db->like('descricao', $termo);
        $this->db->where('om_id', $this->session->userdata('om_id'));
        $this->db->limit(10);
        $data['objetivos'] = $this->db->get('objetivos')->result();

        // buscando tarefas
		$this->db->select('*, setores.sigla AS setor');
        $this->db->join('setores', 'tarefas.setor_id = setores.IdSetor');
        $this->db->where('tarefas.om_id', $this->session->userdata('om_id'));
        $this->db->where('tarefas.ano', $this->session->userdata('anofiscal'));
		$this->db->group_start();
        $this->db->like('titulo', $termo);
		$this->db->or_like('spo_id', $termo);
		$this->db->or_like('sigla', $termo);
		$this->db->group_end();
        $this->db->limit(10);
        $data['tarefas'] = $this->db->get('tarefas')->result();

        // buscando projetos
        $this->db->like('titulo', $termo);
        $this->db->where('om_id', $this->session->userdata('om_id'));
        $this->db->limit(10);
        $data['projetos'] = $this->db->get('projetos')->result();

        return $data;

    }


    function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1')
        {
            return true;
        }

        return false;
    }

    function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0)
        {
            return true;
        }

        return false;
    }

    function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1')
        {
            return true;
        }

        return false;
    }
	
	function getGraph($filtro = '')
    {
        $sql = "SELECT ROUND(Avg(registros.medicao), 0) AS series, perspectiva.nome AS categorias, ROUND(Avg(indicadores.meta), 0) AS metas FROM registros INNER JOIN indicadores ON registros.indicador_id = indicadores.IdIndicador INNER JOIN projetos ON indicadores.projeto_id = projetos.IdProjeto INNER JOIN objetivos ON projetos.objetivo_id = objetivos.IdObjetivo INNER JOIN perspectiva ON objetivos.perspectiva_id = perspectiva.IdPerspectiva WHERE registros.ano = " . $this->session->userdata('anofiscal') . " AND objetivos.om_id = " . $this->session->userdata('om_id') . $filtro . " GROUP BY perspectiva.nome";

        return $this->db->query($sql)->result();
	}


    function count($table)
    {
        $query = $this->db->query('SELECT COUNT(*) AS ' . $this->db->escape_identifiers
            ('numrows') . ' FROM ' . $table . ' WHERE om_id = ' . $this->session->userdata('om_id') .
            ' AND  situacao = 1');
        if ($query->num_rows() === 0)
        {
            return 0;
        }

        $query = $query->row();
        return (int)$query->numrows;

    }
	
	    function countTar($table)
    {
        $query = $this->db->query('SELECT COUNT(*) AS ' . $this->db->escape_identifiers
            ('numrows') . ' FROM ' . $table . ' WHERE om_id = ' . $this->session->userdata('om_id') . ' AND tarefas.ano = '. $this->session->userdata('anofiscal') . 
            ' AND  situacao = 1');
        if ($query->num_rows() === 0)
        {
            return 0;
        }

        $query = $query->row();
        return (int)$query->numrows;

    }
	
	public function getEstatisticasByProjeto()
    {
        $sql = "SELECT projetos.*, setores.sigla AS gerset, Sum(tarefas.valor_previsto) AS estimado, (SELECT Sum(autorizacoes.valor) FROM autorizacoes WHERE autorizacoes.projeto_id = projetos.IdProjeto) AS autorizado, Sum(tarefas.valor_executado) AS executado FROM projetos INNER JOIN gersets ON projetos.gerset_id = gersets.IdGerset INNER JOIN setores ON gersets.setor_id = setores.IdSetor LEFT JOIN tarefas ON tarefas.projeto_id = projetos.IdProjeto
WHERE projetos.om_id = " . $this->session->userdata('om_id') . " GROUP BY projetos.titulo, projetos.IdProjeto, projetos.om_id, projetos.objetivo_id, projetos.descricao, projetos.gerset_id, projetos.abrangencia, projetos.situacao, setores.sigla ORDER BY projetos.IdProjeto ASC";
        return $this->db->query($sql)->result();
    }

    public function getEstatisticasByOrcamento($filter = '')
    {
        $sql = "SELECT SUM(CASE WHEN om_id = " . $this->session->userdata('om_id') . " AND ano = " . $this->session->userdata('anofiscal') . " THEN valor_previsto END) AS total_previsto, SUM(CASE WHEN `status` = 1 AND om_id = " . $this->session->userdata('om_id') . " AND ano = " . $this->session->userdata('anofiscal') . " THEN valor_autorizado END) AS total_reserva, SUM(CASE WHEN `status` = 2 AND om_id = " . $this->session->userdata('om_id') . " AND ano = " . $this->session->userdata('anofiscal') . " THEN valor_autorizado END) AS total_autorizado, SUM(CASE WHEN `status` = 0 AND om_id = " . $this->session->userdata('om_id') . " AND ano = " . $this->session->userdata('anofiscal') . " THEN valor_previsto END) AS total_sem_autorizacao, SUM(CASE WHEN om_id = " . $this->session->userdata('om_id') . " AND ano = " . $this->session->userdata('anofiscal') . " THEN valor_executado END) AS total_executado, (SELECT SUM(CASE WHEN autorizacoes.om_id = " . $this->session->userdata('om_id') . " AND ano_autorizacao  = " . $this->session->userdata('anofiscal') . " THEN valor END) FROM autorizacoes) AS total_geral_autorizado, (SELECT SUM(CASE WHEN execucoes.om_id = " . $this->session->userdata('om_id') . " AND EXTRACT(YEAR FROM data_empenho) = " . $this->session->userdata('anofiscal') . " THEN valor_empenho END) FROM execucoes" . $filter . ") AS executado_mes FROM tarefas";
        return $this->db->query($sql)->row();
    }


    public function check_credentials($email)
    {
        $this->db->where('email', $email);
        $this->db->where('situacao', 1);
        $this->db->limit(1);
        return $this->db->get('usuarios')->row();
    }
	
		public function checkApoiadora($om)
	{
		$this->db->where('IdOm', $om);
		return $this->db->get('om')->row();
	}
}
