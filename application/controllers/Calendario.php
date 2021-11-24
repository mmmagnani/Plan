<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }


    /**
     * author: Marcelo Magnani 
     * email: marcelommagnani@uol.com.br
     * 
     */
	 
class Calendario extends CI_Controller
{
	 
    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('plan/login');
        }

        $this->load->model('Calendario_model');
        $this->load->library('form_validation');
		$this->load->library('PHPExcel');
		$this->load->helper('formater');
    }

    public function index()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'vCalendario')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('calendar'));
            redirect(base_url());
        }

        $data['view'] = 'calendario/calendario_list';
        $this->load->view('tema/topo', $data, false);
    }

    public function datatable()
    {

        if (!$this->permission->check($this->session->userdata('permissao'), 'vCalendario')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('cadastros'));
            redirect(base_url());
        }

        $this->load->model('Calendario_model');
        $result_data = $this->Calendario_model->get_datatables();
        $data = array();

        foreach ($result_data as $row) {
			
            $line = array();

            $line[] = $row->sigla;
            $line[] = $row->objeto;
			$line[] = $row->homol_pretendida ? date('d/m/Y', strtotime($row->homol_pretendida)) : '';
			$line[] = $row->prazo_ent_gap ? date('d/m/Y', strtotime($row->prazo_ent_gap)) : '';
			$line[] = $row->status; 
			
		if ($this->permission->check($this->session->userdata('permissao'), 'dCalendario')) {       
			$line[] = $row->situacao ? $this->lang->line('app_active') : $this->lang->line('app_inactive');
			
			$color = $row->situacao ? 'btn-danger' : 'btn-success';
            $icon = $row->situacao ? 'fa fa-window-close' : 'fa fa-check';
            $title = $row->situacao ? $this->lang->line('app_disable') : $this->lang->line('app_activate');
		}
			 
		if ($this->permission->check($this->session->userdata('permissao'), 'vCalendario')) {
            $view = '<a href="' . site_url('calendario/read/' . $row->idCalendario) . '" class="btn btn-dark" title="' . $this->lang->line('app_view') . '"><i class="fa fa-eye"></i> </a>';
		} else {
			$view = '';
		}
		if ($this->permission->check($this->session->userdata('permissao'), 'eCalendario')) {
			$edit = '<a href="' . site_url('calendario/update/' . $row->idCalendario) . '" class="btn btn-info" title="' . $this->lang->line('app_edit') . '"><i class="fa fa-edit"></i></a>';
		} else {
			$edit = '';
		}
		if ($this->permission->check($this->session->userdata('permissao'), 'dCalendario')) {
			$del = '<a href="'.site_url('calendario/status/' . $row->idCalendario) . '" class="btn '.$color.' delete" title="' . $title . '"><i class="'.$icon.'"></i></a>';
		} else {
			$del = '';
		}
		
		$line[] = $view . ' ' . $edit . ' ' . $del;
			
        $data[] = $line;
        }

        $output = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $this->Calendario_model->get_all_data(),
            'recordsFiltered' => $this->Calendario_model->get_filtered_data(),
            'data' => $data,
        );
        echo json_encode($output);
    }

    public function read($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('calendario');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'vCalendario')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_view') . ' ' . $this->lang->line('calendario'));
            redirect(base_url());
        }

        $row = $this->Calendario_model->with('om')->with('tipo')->with('status')->get($id);
        if ($row) {
            $data = array(
                'om' => $row->om->sigla,
				'objeto' => $row->objeto,
                'valor_estimado' => number_format($row->valor_estimado, 2, ',', '.'),
                'gerset' => $row->gerset,
                'prazo_env_gerset' => $row->prazo_env_gerset ? date('d/m/Y', strtotime($row->prazo_env_gerset)) : '',
                'prazo_ent_gap' => $row->prazo_ent_gap ? date('d/m/Y', strtotime($row->prazo_ent_gap)) : '',
                'data_ent_gap' => $row->data_ent_gap ? date('d/m/Y', strtotime($row->data_ent_gap)) : '',
                'atraso' => $row->atraso,
                'homol_pretendida' => $row->homol_pretendida ? date('d/m/Y', strtotime($row->homol_pretendida)) : '',
                'homol_estimada' => $row->homol_estimada ? date('d/m/Y', strtotime($row->homol_estimada)) : '',
				'status' => isset($row->status->status_desc) ? $row->status->status_desc : '', 
				'observacao' => $row->observacao,
				);
			$data['graph'] = $this->Calendario_model->getGraph($id);
			$data['apoiadora'] = $this->Calendario_model->getSiglaApoiadora($this->session->userdata('apoiadora'));
            $data['view'] = 'calendario/calendario_read';
            $this->load->view('tema/topo', $data, false);
        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('calendario'));
        }
    }

    public function create()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'aCalendario')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('calendar'));
            redirect(base_url());
        }
		
        $data = array(
            'button' => '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'),
            'action' => site_url('calendario/create_action'),
			'idCalendario' => set_value('idCalendario'),
			'ano_calendario' => set_value('ano_calendario'),
			'om_id' => set_value('om_id'),
			'objeto' => set_value('objeto'),
            'valor_estimado' => set_value('valor_estimado'),			
            'homol_pretendida' => set_value('homol_pretendida'),
            'observacao' => set_value('observacao'),
			'situacao' => set_value('situacao'),
			'gerset' => set_value('gerset'),
			'data_ent_gap' => set_value('data_ent_gap'),
			'tipo' => set_value('tipo'),
			'homol_efetiva' => set_value('homol_efetiva'),
			'status_id' => set_value('status_id'),
			
        );

        $data['view'] = 'calendario/calendario_form';
        $this->load->view('tema/topo', $data, false);

    }

    public function create_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'aCalendario')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_add') . ' ' . $this->lang->line('calendar'));
            redirect(base_url());
        }

        	$this->_rules();

        	if ($this->form_validation->run() == false) {
            	$this->create();
        	} else {
				$homol_pretendida = $this->input->post('homol_pretendida', true);
				try
				{
					$homol_pretendida = explode('/', $homol_pretendida);
					$homol_pretendida = $homol_pretendida[2] . '-' . $homol_pretendida[1] . '-' . $homol_pretendida[0];
					$homol_estimada = $homol_pretendida;
	
				}
				catch (exception $e)
				{
					$homol_pretendida = date('Y-m-d');
					$homol_estimada = $homol_pretendida;
				}
				$situacao = 1;
				$status = 1;
				$valor_estimado =  $this->input->post('valor_estimado', true);
				$valor_estimado = str_replace(",", ".", str_replace(".", "", $valor_estimado));
				$ano_calendario = $this->session->userdata('anofiscal');
				$om_id = $this->session->userdata('om_id');
            	$data = array(
                	'idCalendario' => $this->input->post('idCalendario', true),
					'ano_calendario' => $ano_calendario,
					'om_id' => $om_id,
					'objeto' => $this->input->post('objeto', true),
                	'valor_estimado' => $valor_estimado,
                	'homol_pretendida' => $homol_pretendida,
					'homol_estimada' => $homol_estimada,
                	'observacao' => $this->input->post('observacao', true),		
					'situacao' => $situacao,
					'status_id' => $status,
            	);

            	$this->Calendario_model->insert($data);
            	$this->session->set_flashdata('success', $this->lang->line('app_add_message'));
            	redirect(site_url('calendario'));
        	}
    }

    public function update($id)
    {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('calendario');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'eCalendario')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('calendar'));
            redirect(base_url());
        }
		
		$this->load->model('Tipo_model');
        $tipos = $this->Tipo_model->as_dropdown('tipo_desc')->get_all();
		$tipos[] = '';
		asort($tipos);
		
		$this->load->model('Status_model');
        $status = $this->Status_model->as_dropdown('status_desc')->get_all();
		$status[] = '';
		asort($status);
			


        $row = $this->Calendario_model->get($id);

        if ($row) {
			if(!is_null($row->homol_pretendida)) {
				$homol_pretendida = date('d/m/Y', strtotime($row->homol_pretendida));
			} else {
				$homol_pretendida = NULL;
			}
			if(!is_null($row->data_ent_gap)) {
				$data_ent_gap = date('d/m/Y', strtotime($row->data_ent_gap));
			} else {
				$data_ent_gap = NULL;
			}
            $data = array(
                'button' => '<i class="fa fa-edit"></i> ' . $this->lang->line('app_edit'),
                'action' => site_url('calendario/update_action'),
				'idCalendario' => set_value('idCalendario', $row->idCalendario),
				'ano_calendario' => set_value('ano_calendario', $row->ano_calendario),
				'objeto' => set_value('objeto', $row->objeto),
				'valor_estimado' => set_value('valor_estimado', $row->valor_estimado),
            	'homol_pretendida' => set_value('homol_pretendida', $homol_pretendida),
            	'observacao' => set_value('observacao', $row->observacao),	
				'situacao' => set_value('situacao', $row->situacao),
				'om_id' => set_value('om_id', $row->om_id),	
				'gerset' => set_value('gerset', $row->gerset),
				'data_ent_gap' => set_value('data_ent_gap', $data_ent_gap),
				'tipo' => set_value('tipo', $row->tipo),
				'tipos' => $tipos,
				'homol_efetiva' => set_value('homol_efetiva', $row->homol_efetiva),
				'status_id' => set_value('status_id', $row->status_id),	
				'status' => $status,	

            );
            $data['view'] = 'calendario/calendario_form';
            $this->load->view('tema/topo', $data, false);

        } else {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('cadastros'));
        }
    }

    public function update_action()
    {
        if (!$this->permission->check($this->session->userdata('permissao'), 'eCalendario')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('cadastros'));
            redirect(base_url());
        }
        	$this->_rules();

        	if ($this->form_validation->run() == false) {
            	$this->update($this->input->post('idCalendario', true));
        	} else {
				$homol_pretendida = $this->input->post('homol_pretendida', true);
				try
				{
					$homol_pretendida = explode('/', $homol_pretendida);
					$homol_pretendida = $homol_pretendida[2] . '-' . $homol_pretendida[1] . '-' . $homol_pretendida[0];
	
				}
				catch (exception $e)
				{
					$homol_pretendida = date('Y-m-d');
				}
				if(!empty($this->input->post('data_ent_gap')))
				{
					$data_ent_gap = $this->input->post('data_ent_gap', true);
					try
					{
						$data_ent_gap = explode('/', $data_ent_gap);
						$data_ent_gap = $data_ent_gap[2] . '-' . $data_ent_gap[1] . '-' . $data_ent_gap[0];
	
					}
					catch (exception $e)
					{
						$data_ent_gap = date('Y-m-d');
					}		
				}
				else
				{
					$data_ent_gap = NULL;
				}
				$prazo_ent_gap = NULL;
				$prazo_env_gerset = NULL;
				if(!empty($this->input->post('tipo')))
				{
					switch($this->input->post('tipo'))
					{
					case 1: $prazo_ent_gap = date('Y-m-d',strtotime("-120 days",strtotime($homol_pretendida)));
							if(!empty($this->input->post('gerset')))
							{
								$prazo_env_gerset = date('Y-m-d',strtotime("-150 days",strtotime($homol_pretendida)));	
							}
						break;
					case 2: $prazo_ent_gap = date('Y-m-d',strtotime("-95 days",strtotime($homol_pretendida)));
							if(!empty($this->input->post('gerset')))
							{
								$prazo_env_gerset = date('Y-m-d',strtotime("-125 days",strtotime($homol_pretendida)));	
							}				
						break;
					case 3: $prazo_ent_gap = date('Y-m-d',strtotime("-180 days",strtotime($homol_pretendida)));
							if(!empty($this->input->post('gerset')))
							{
								$prazo_env_gerset = date('Y-m-d',strtotime("-210 days",strtotime($homol_pretendida)));	
							}				
						break;
					}
				}
				if((empty($this->input->post('data_ent_gap')))&&(strtotime($prazo_ent_gap) < strtotime("now")))
				{
					$date1 = date_create("now");
					$date2 = date_create($prazo_ent_gap);
					$intervalo = date_diff($date2,$date1);
					$atraso = $intervalo->format('%R%a');
				}
				else 
				{
					if(empty($data_ent_gap))
					{
						$atraso = NULL;
					}
					else
					{
						$date1 = date_create($data_ent_gap);
						$date2 = date_create($prazo_ent_gap);
						$intervalo = date_diff($date2,$date1);
						$atraso = $intervalo->format('%R%a');
					}
				}
				if(empty($atraso))
				{
					$homol_estimada = $homol_pretendida;
				}
				else
				{
					$homol_estimada = date('Y-m-d',strtotime("{$atraso} days",strtotime($homol_pretendida)));
				}
				
				if(!empty($this->input->post('homol_efetiva')))
				{
					$homol_efetiva = $this->input->post('homol_efetiva', true);
					try
					{
						$homol_efetiva = explode('/', $homol_efetiva);
						$homol_efetiva = $homol_efetiva[2] . '-' . $homol_efetiva[1] . '-' . $homol_efetiva[0];
	
					}
					catch (exception $e)
					{
						$homol_efetiva = date('Y/m/d');
					}
				}
				else
				{
					$homol_efetiva = NULL;
				}
				if(!empty($this->input->post('gerset')))
				{
					$gerset = $this->input->post('gerset', true);
				}
				else
				{
					$gerset = NULL;
				}
				$situacao = $this->input->post('situacao', true);
				$status = $this->input->post('status_id', true);
				$valor_estimado =  $this->input->post('valor_estimado', true);
				$valor_estimado = str_replace(",", ".", str_replace(".", "", $valor_estimado));	
				$om = $this->input->post('om_id', true);
            	$data = array(
					'objeto' => $this->input->post('objeto', true),
					'gerset' => $gerset,
					'prazo_env_gerset' => $prazo_env_gerset,
					'prazo_ent_gap' => $prazo_ent_gap,				
					'om_id' => $om,
					'data_ent_gap' => $data_ent_gap,				
					'valor_estimado' => $valor_estimado,
					'homol_pretendida' => $homol_pretendida,
					'homol_estimada' => $homol_estimada,
					'atraso' => $atraso,
					'tipo' => $this->input->post('tipo', true),
					'homol_efetiva' => $homol_efetiva,
					'status_id' => $status,
					'observacao' => $this->input->post('observacao'),
					'situacao' => $situacao,
            	);

            	$this->Calendario_model->update($data, $this->input->post('idCalendario', true));
            	$this->session->set_flashdata('success', $this->lang->line('app_edit_message'));
				
				//exporta para planilha excel__________________________________________
				//
				//
				$this->exporta_local('base_certames');
				//
				//
				//_____________________________________________________________________
				
            	redirect(site_url('calendario'));
        	}		
    }

    public function status($idCalendario)
    {
        if (!is_numeric($idCalendario)) {
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect('calendario');
        }

        if (!$this->permission->check($this->session->userdata('permissao'), 'dCalendario')) {
            $this->session->set_flashdata('error', $this->lang->line('app_permission_edit') . ' ' . $this->lang->line('calendar'));
            redirect(base_url());
        }

        $row = $this->Calendario_model->get($idCalendario);
        $ajax = $this->input->get('ajax');

        if ($row) {
            if ($this->Calendario_model->delete($idCalendario)) {

                if ($ajax) {
					$this->session->set_flashdata('success', $this->lang->line('app_delete_message'));
                    echo json_encode(array('result' => true, 'message' => $this->lang->line('app_delete_message')));die();
                }
                $this->session->set_flashdata('success', $this->lang->line('app_delete_message'));
                redirect(site_url('calendario'));
            } else {

                if ($ajax) {
                    echo json_encode(array('result' => false, 'message' => $this->lang->line('app_error')));die();
                }

                $this->session->set_flashdata('error', $this->lang->line('app_error'));
                redirect(site_url('calendario'));
            }

        } else {

            if ($ajax) {
                echo json_encode(array('result' => false, 'message' => $this->lang->line('app_not_found')));die();
            }
            $this->session->set_flashdata('error', $this->lang->line('app_not_found'));
            redirect(site_url('calendario'));
        }

    }

    public function _rules()
    {
        $this->form_validation->set_rules('objeto', '<b>' . $this->lang->line('object') . '</b>', 'trim|required');
		$this->form_validation->set_rules('valor_estimado', '<b>' . $this->lang->line('estimated_val') . '</b>', 'trim|required');		
        $this->form_validation->set_rules('homol_pretendida', '<b>' . $this->lang->line('intended_approval_date') . '</b>', 'trim|required');		
        
        $this->form_validation->set_rules('idCalendario', 'idCalendario', 'trim');

        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }
	
	public function exportar()
    {

        // Create new Spreadsheet object
        $objPHPExcel = $this->phpexcel;

        //Set document properties
        $objPHPExcel->getProperties()->setCreator($this->session->userdata('nome'))->
            setLastModifiedBy($this->session->userdata('nome'))->setTitle('Calendario - ' . $this->
            session->userdata('anofiscal'))->setSubject('Calendario SPO')->setDescription('Planilha Calendario.')->
            setKeywords('SPO')->setCategory('Arquivo SPO');

        // add style to the header
        $styleArray = array(
            'font' => array('bold' => true, ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            'borders' => array('allBorders' => array(
                    'borderStyle' => PHPExcel_Style_Border::BORDER_THIN
                    ) 
				),
            'fill' => array(
                'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
                'startColor' => array('argb' => 'CCCCCCCC', ),
                ),
            );

        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);

        // auto fit column to content

        foreach (range('A', 'F') as $columnID)
        {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->getStyle('B2:B255')->getAlignment()->
            setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle('D2:D255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('E2:E255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('F2:F255')->getNumberFormat()->
            setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        // set the names of header cells
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1", 'OM')->
            setCellValue("B1", 'Objeto')->setCellValue("C1", 'GERSET')->setCellValue("D1",
            'Prazo envio ao GERSET')->setCellValue("E1", utf8_encode('Homologação Pretendida'))->setCellValue("F1", 'Valor Estimado (R$)');

        // Add some data
        $limite = 5000;
            $calendario = $this->Calendario_model->export_calendar($limite);
        
		$x = 2;
        if (!$calendario)
        {
            $objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",
                'Não há tarefas cadastradas');
        } else
        {
            foreach ($calendario as $t)
            {
				if(!is_null($t->prazo_env_gerset)) {
				  $peg = date('d/m/Y', strtotime($t->prazo_env_gerset));
				} else {
				  $peg = '';
				}
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$x", $t->sigla)->
                    setCellValue("B$x", $t->objeto)->setCellValue("C$x", $t->gerset)->setCellValue("D$x",
                    $peg)->setCellValue("E$x", date('d/m/Y', strtotime($t->
                    homol_pretendida)))->setCellValue("F$x", $t->valor_estimado);
                $x++;
            }
            $y = $x;
            $x = $x - 1;

			 $styleArray = array(
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $y . ':E' . $y);
            $objPHPExcel->getActiveSheet()->getStyle("A$y")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $y . ':F' . $y)->getFont()->
                setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1:F' . $y)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A1');
            $objPHPExcel->getActiveSheet()->setCellValue("A$y", 'TOTAL (R$)');
            $objPHPExcel->getActiveSheet()->setCellValue("F$y", '=SUM(F2:F' . $x . ')');
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('SPO - ' . $this->session->userdata('anofiscal'));

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="calendario_spo' . $this->session->userdata('anofiscal') . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');	
        exit;
    }
	
	public function exporta_local($filename='')
    {

        // Create new Spreadsheet object
        $objPHPExcel = $this->phpexcel;

        //Set document properties
        $objPHPExcel->getProperties()->setCreator($this->session->userdata('nome'))->
            setLastModifiedBy($this->session->userdata('nome'))->setTitle('Calendario - ' . $this->
            session->userdata('anofiscal'))->setSubject('Calendario SPO')->setDescription('Planilha Calendario.')->
            setKeywords('SPO')->setCategory('Arquivo SPO');

        // add style to the header
        $styleArray = array(
            'font' => array('bold' => false, ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );

        $objPHPExcel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($styleArray);

        // auto fit column to content

        foreach (range('A', 'N') as $columnID)
        {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->getStyle('A2:A255')->getAlignment()->
            setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('B2:B255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C2:C255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);	
		$objPHPExcel->getActiveSheet()->getStyle('D2:D255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('E2:E255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F2:F255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('G2:G255')->getNumberFormat()->
            setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle('H2:H255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('I2:I255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('J2:J255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('L2:L255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('M2:M255')->getAlignment()->
			setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
        // set the names of header cells
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1", 'OBJETO')->
            setCellValue("B1", 'GERSET')->setCellValue("C1", 'PRAZO PARA ENVIO AO GERSET')->setCellValue("D1",
            'PRAZO DE ENTRADA NO GAP')->setCellValue("E1", 'UASG/SETOR')->setCellValue("F1",'ENTRADA NO GAP')->setCellValue("G1", 'VALOR ESTIMADO')->setCellValue("H1", utf8_encode('HOMOLOGAÇÃO PRETENDIDA'))->setCellValue("I1", utf8_encode('HOMOLOGAÇÃO ESTIMADA'))->setCellValue("J1", 'ATRASO')->setCellValue("K1",'TIPO')->setCellValue("L1", utf8_encode('HOMOLOGAÇÃO EFETIVA'))->setCellValue("M1", 'STATUS')->setCellValue("N1",utf8_encode('OBSERVAÇÃO'));

        // Add some data
        $limite = 5000;
            $calendario = $this->Calendario_model->export_calendar($limite);
        
		$x = 2;
        if (!$calendario)
        {
            $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",
                'Não há tarefas cadastradas');
        } else
        {
            foreach ($calendario as $t)
            {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$x", $t->objeto)->                  setCellValue("B$x", (!$t->gerset)?' - ':$t->gerset)->setCellValue("C$x", (!$t->prazo_env_gerset)?'':date('d/m/Y', strtotime($t->prazo_env_gerset)))->setCellValue("D$x", (!$t->prazo_ent_gap)?'':date('d/m/Y', strtotime($t->prazo_ent_gap)))->setCellValue("E$x", $t->sigla)->setCellValue("F$x", (!$t->data_ent_gap)?'':date('d/m/Y', strtotime($t->data_ent_gap)))->setCellValue("G$x", $t->valor_estimado)->setCellValue("H$x", (!$t->homol_pretendida)?'':date('d/m/Y', strtotime($t->homol_pretendida)))->setCellValue("I$x", (!$t->homol_estimada)?'':date('d/m/Y', strtotime($t->homol_estimada)))->setCellValue("J$x", $t->atraso)->setCellValue("K$x", $t->tipo)->setCellValue("L$x", (!$t->homol_efetiva)?'':date('d/m/Y', strtotime($t->homol_efetiva)))->setCellValue("M$x", $t->status_desc)->setCellValue("N$x", $t->observacao);
                $x++;
            }
            $y = $x;
            $x = $x - 1;

			 $styleArray = array(
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);

        }
		$objPHPExcel->getActiveSheet()->freezePane('B2');

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('SPO - ' . $this->session->userdata('anofiscal'));

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();
		$objWriter->save('assets/planilhas/' . $filename . '.xlsx');
		return;
    }
	
}

/* End of file Calendario.php */
/* Location: ./application/controllers/Calendario.php */
