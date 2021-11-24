<?php $process = (isset($_GET["go"])) ? $_GET["go"] : "0"; ?>

<?php $aplicar = (isset($_GET["aplicar"])) ? $_GET["aplicar"] : "0"; ?>

<div class="row-fluid" style="margin-top:0">

	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="card">
        	<div class="card-title">
				<h4>
					<i class="fa fa-file-upload"></i>
					<?= ucfirst($this->lang->line('import_plan')); ?>
				</h4>
			</div>

			<div class="card-body">
			
			<?php if ($process == 0 && $aplicar == 0)
				  {
				  	if (isset($feito) && $feito == true)
    			  {
			?>
          			<div class="form-body">
            
                		<?php echo $custom_error; ?>

              			<form>
                        
                        	<div class="alert alert-success" align="center" style=" margin-top:20px">
                            
                            	<?php echo $upok; ?>
                                
                            </div>
                    
							<div class="form-actions">
                    
								<div class="span12">
                        
									<div class="span6 offset3">
                            
                                		<a href="?go=1&projeto_id=<?php echo $projetoid; ?>" class="btn btn-success"><?= ucfirst($this->lang->line('plan_process')); ?></a>
                            
                                		<a href="<?= site_url('tarefas') ?>" class="btn btn-dark" onclick="<?php $this->session->set_flashdata('success'); ?>">
                            			<i class="fa fa-reply"></i>
                            			<?= $this->lang->line('app_cancel'); ?>
                        				</a>
                             	
									</div>
                            
								</div>
                        
							</div>
                    
                		</form>
                
            		</div>            
                <?php

    				} else {
				?>
            
            <div class="form-body">
            
                <?php echo $custom_error; ?>
                
                <label style="padding:20px; color:#0000CC"><strong><?= $this->lang->line('download_plan_model'); ?> - <a href="<?php echo base_url(); ?>assets/arquivos/planilha_modelo_importacao.xls"><u><?= $this->lang->line('click_here'); ?></u></a></strong></label>

              	<form action="<?php echo current_url(); ?>" id="formArquivo" enctype="multipart/form-data" method="post" >
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-12">
					<div class="form-group">
                    	<label for="userfile"><?= ucfirst($this->lang->line('plan')) ?></label>                                               
                        <input type="file"  id="userfile" name="userfile" accept="application/vnd.ms-excel" class="filestyle form-control" data-placeholder="<?= ucfirst($this->lang->line('select_file')) ?>" data-text="<?= ucfirst($this->lang->line('search_file_button')) ?>" />   
                        
                    </div>
				  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-12">
					<div class="form-group">
                    
                        <label for="projeto_id">
						<?= ucfirst($this->lang->line('project')) ?>
                        </label>
                    	<?= form_dropdown('projeto_id', $projetos,null, array('class' => 'form-control')); ?>
                        <?= form_error('projeto_id') ?>                                                    
					</div>                       
				  </div>  
                </div>  
					<div class="form-actions">
                    
						<div class="span12">
                        
							<div class="span6 offset3">
                            
                                <button type="submit" class="btn btn-success" onclick="<?php $this->session->set_flashdata('error'); ?>"><i class="icon-upload icon-white"></i> Upload</button>
                            
                                <a href="<?= site_url('tarefas') ?>" class="btn btn-dark" onclick="<?php $this->session->set_flashdata('success'); ?>">
				<i class="fa fa-reply"></i>
				<?= $this->lang->line('app_back'); ?>
			</a>
                             	
							</div>
                            
						</div>
                        
					</div>
                    
                </form>
                
            </div>
			
			<?php  }

				}

				if ($process == "1")
				{
    				if ($this->session->userdata('importado'))
    			{

        echo '<div class="form-body">';
        echo '<div align="center" style=" margin-top:20px" class="alert alert-info">Verifique se os dados foram importados corretamente e clique em Criar Tarefas.<br>Se houver algum erro clique em Voltar.</div>';
        echo '<form>';
        echo '<div class="form-group">';
        echo '<table class="table table-bordered">';
        echo '<thead>';
        echo '<tr style="backgroud-color: #2D335B">';
		echo '<th>Tipo</th>';
        echo '<th>Título</th>';
        echo '<th>Descrição (R$)</th>';
		echo '<th>CATMAT</th>';
        echo '<th>Justificativa</th>';
        echo '<th>Valor Estimado (R$)</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($this->session->userdata('importado') as $r)
        {
            echo '<tr>';
			echo '<td>' . $r['checkBoxObjeto'] . '</td>';
            echo '<td>' . $r['titulo'] . '</td>';
            echo '<td>' . $r['descricao'] . '</td>';
			echo '<td>' . $r['CATMAT'] . '</td>';
            echo '<td>' . $r['justificativa'] . '</td>';
            echo '<td>' . number_format(floatval($r['estimado']), 2, ',', '.') . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '<div class="form-actions">';
        echo '<div class="span12">';
        echo '<div class="span6 offset3">';
        echo '<a href="?aplicar=1&projeto_id=' . $_GET['projeto_id'] .
            '" class="btn btn-success">Criar Tarefas</a> <a href="'.site_url('tarefas').'" class="btn btn-dark" onclick="' . $this->session->set_flashdata('success') . '"><i class="fa fa-reply"> </i>' . $this->lang->line('app_back') . '</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    } else
    {
        redirect(base_url() . 'index.php/tarefas');
    }

}

if ($aplicar == 1)
{
    if ($this->session->userdata('importado'))
    {
        $situacao = 1;
        $projetoid = $_GET['projeto_id'];
        $om = $this->session->userdata('om_id');
        $cod = $this->Tarefas_model->getCodOm();
        $setor = $this->session->userdata('setor_id');
        $ano = $this->session->userdata('anofiscal');
        $received = array();
        $received = $this->session->userdata('importado');
        $this->session->unset_userdata('importado');
        $data2 = array();
        foreach ($received as $r2)
        {
			$this->load->model('Tarefas_model');
            $seq = $this->Tarefas_model->uid('tarefas', 7, $cod);
            $valor_previsto = number_format(floatval($r2['estimado']), 2, '.', '');
            $spo = $cod . $seq;
            $data = array(
                'om_id' => $om,
                'ano' => $ano,
                'spo_id' => $spo,
                'setor_id' => $setor,
                'projeto_id' => $projetoid,
				'checkBoxObjeto' => $r2['checkBoxObjeto'],
                'titulo' => $r2['titulo'],
                'descricao' => $r2['descricao'],
				'CATMAT' => $r2['CATMAT'],
                'justificativa' => $r2['justificativa'],
                'valor_previsto' => $valor_previsto,
                'situacao' => $situacao,
                );
				$this->load->model('Tarefas_model');
            $this->Tarefas_model->insert($data);
            $data2[] = $data;
        }

        echo '<div class="wform-body">';
        echo '<div style="margin-top:20px" class="alert alert-success" align="center">Tarefas criadas. Clique em Voltar para retornar a listagem de tarefas.</div>';
        echo '<form>';
        echo '<div class="form-group">';
        echo '<table class="table table-bordered">';
        echo '<thead>';
        echo '<tr style="backgroud-color: #2D335B">';
        echo '<th>Nº SPO</th>';
        echo '<th>Título</th>';
        echo '<th>Descrição (R$)</th>';
		echo '<th>CATMAT</th>';
        echo '<th>Justificativa</th>';
        echo '<th>Valor Estimado (R$)</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($data2 as $d2)
        {
            echo '<tr>';
            echo '<td>' . $d2['spo_id'] . '</td>';
            echo '<td>' . $d2['titulo'] . '</td>';
            echo '<td>' . $d2['descricao'] . '</td>';
			echo '<td>' . $d2['CATMAT'] . '</th>';
            echo '<td>' . $d2['justificativa'] . '</td>';
            echo '<td>' . number_format(floatval($d2['valor_previsto']), 2, ',', '.') .
                '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '<div class="form-actions">';
        echo '<div class="span12">';
        echo '<div class="span6 offset3">';
        echo '<a href="'.site_url('tarefas').'" class="btn btn-dark" onclick="' . $this->session->set_flashdata('success') . '"><i class="fa fa-reply"> </i>' . $this->lang->line('app_back') . '</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    } else
    {
        redirect(base_url() . 'index.php/tarefas');

    }
}

?>

            
        </div>
        
    </div>
    
</div>

<script type="text/javascript" src="<?= base_url('assets/js/lib/bootstrap-filestyle/bootstrap-filestyle.min.js'); ?>"> </script>


                                    
