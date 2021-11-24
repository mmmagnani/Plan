<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
				<?= $button ?>
                <?= ucfirst($this->lang->line('tasks')); ?>
              
                <hr>
            </div>
            <div class="card-body">
                <div class="form-body">
                   <form id="taskform" action="<?= $action; ?>" method="post">
                   <div class="row">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                     	 <label><?= ucfirst($this->lang->line('expense_type')); ?></label><br />
                         <label class="radio-inline">
                            <input type="radio" name="checkBoxObjeto" id="s" value="s" <?= ($checkBoxObjeto == "s") ? "checked" : null; ?> /> Serviço</label>                   
                         <label class="radio-inline offset-1">
                            <input type="radio" name="checkBoxObjeto" id="c" value="c" <?= ($checkBoxObjeto == "c") ? "checked" : null; ?> /> Consumo</label>      
                         <label class="radio-inline offset-1">
                            <input type="radio" name="checkBoxObjeto" id="p" value="p" <?= ($checkBoxObjeto == "p") ? "checked" : null; ?> /> Permanente</label>
                         <label class="radio-inline offset-1">
                            <input type="radio" name="checkBoxObjeto" id="d" value="d" <?= ($checkBoxObjeto == "d") ? "checked" : null; ?> /> Diária</label><br />
							<?= form_error('checkBoxObjeto') ?>						
                     </div>
                   </div>
                   <hr />
                   <?php
				   // Show IF Conditional region 
				   if($IdTarefa != "") { ?>
                   <div class="row">
                     <div class="col-lg-3 col-md-3 col-sm-12">
                   		<div class="form-group">
                            <label for="ano">
                                <?= ucfirst($this->lang->line('year')) ?>
                            </label>							
                            <input type="text" class="form-control" name="ano" id="ano" value="<?= $ano; ?>"  />							<?= form_error('ano') ?>
                        </div>
                     </div>
					 <div class="col-lg-3 col-md-3 col-sm-12">
                   		<div class="form-group">
                            <label for="setor">
                                <?= ucfirst($this->lang->line('sector')) ?>
                            </label>							
                            <input type="text" class="form-control" name="setor" id="setor" value="<?= $setor; ?>" readonly="readonly"  />	
							<input type="hidden" name="setor_id" value="<?= $setor_id; ?>" />
                        </div>
                     </div>
                   </div>
				   <?php } else { ?>
				   <input type="hidden" name="ano" value="<?= $this->session->userdata('anofiscal'); ?>" />
				   <input type="hidden" name="setor_id" value="<?= $this->session->userdata('setor_id'); ?>" />
				   <?php } 
				   // End conditional region
				   ?>
                   <div class="row">
                     <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="titulo">
                                <?= ucfirst($this->lang->line('title')) ?>
                            </label>
                            <input class="form-control" name="titulo" id="titulo" onkeyup="this.value = this.value.toUpperCase()" value="<?= $titulo; ?>" />
                            <?= form_error('titulo') ?>
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="projeto_id">
                                <?= ucfirst($this->lang->line('project')) ?>
                            </label>
                            <?= form_dropdown('projeto_id', $projetos, $projeto_id, array('class' => 'form-control')); ?>
                            <?= form_error('projeto_id') ?>
                        </div>
                     </div>
                   </div>
                   <div class="row">                     
                     <div class="col-lg-8 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="descricao">
                                <?= ucfirst($this->lang->line('description')) ?>
                            </label>
                            <textarea class="form-control" name="descricao" id="descricao" style="resize:none" cols="50" rows="5" onkeyup="this.value = this.value.toUpperCase()"><?= $descricao; ?></textarea>
                            <?= form_error('descricao') ?>
                        </div>
                     </div>                     
                     <div id="divHide" class="col-lg-4 col-md-4 col-sm-12">                     
                        <div class="form-group">
                            <label for="CATMAT">
                                <?= ucfirst($this->lang->line('catmat')) ?>
                            </label>
                            <input class="form-control" name="CATMAT" id="CATMAT" value="<?= $CATMAT; ?>" />
                            <?= form_error('CATMAT') ?>
                        </div>
                     </div>
                   </div>
                   <div class="row">
                     <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="justificativa">
                                <?= ucfirst($this->lang->line('justification')) ?>
                            </label>
                            <textarea class="form-control" name="justificativa" id="justificativa" style="resize:none" cols="50" rows="5" onkeyup="this.value = this.value.toUpperCase()"><?= $justificativa; ?></textarea>
                            <?= form_error('justificativa') ?>
                        </div>
                     </div>
                   	 <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-group">
                   	        <label for="valor_previsto">
                                <?= ucfirst($this->lang->line('estimated_val')) ?>
                            </label>
                            <input type="text" class="form-control money" name="valor_previsto" id="valor_previsto" value="<?= $valor_previsto; ?>" />
                            <?= form_error('valor_estimado') ?>
                        </div>
                     </div>
                     <div class="col-lg-3 col-md-3 col-sm-12">
                     	<div class="form-group">
                        	<label for="situacao">
                            	<?= ucfirst($this->lang->line('situation')) ?>
                            </label>
                            <?php 
                            	$options = array(
                                	'1' => $this->lang->line('app_active'),
                                    '0' => $this->lang->line('app_inactive')
                                );
                                echo form_dropdown('situacao', $options, $situacao, array('class' => 'form-control'));
                                echo form_error('situacao');
                           	?>
                        </div>
                     </div>
                   </div>
                        <input type="hidden" name="IdTarefa" value="<?= $IdTarefa; ?>" />
                        <input type="hidden" name="om_id" value="<?= $this->session->userdata('om_id'); ?>" />
                        <input type="hidden" name="spo_id" value="<?= $spo_id; ?>" />
						<input type="hidden" name="valor_autorizado" value="<?= $valor_autorizado; ?>" />
						<input type="hidden" name="status" value="<?= $status; ?>" />
						<input type="hidden" name="prioridade" value="<?= $prioridade; ?>" />
                        <div><p>&nbsp;</p></div>
                        
                        <button type="submit" class="btn btn-info envia">
                            <?= $button ?>
                        </button>      
                        <a href="<?= site_url('tarefas') ?>" class="btn btn-dark">
                            <i class="fa fa-reply"></i>
                            <?= $this->lang->line('app_cancel'); ?>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
        //FUNCAO PARA ESCONDER E APRESENTAR CAMPOS
        $(document).ready(function(){
			if($("#material").prop("checked")) {
              $("#divHide").show();
			}
            else if($("#servico").prop("checked")) {
              $("#divHide").hide();
            } else {
              $("#divHide").hide();
			}
            $("input[name$='checkBoxObjeto']").click(function(){                                             
                if($("#material").prop("checked")) {
                    $("#divHide").show();
                }else{
                   $("#divHide").hide();
                }
            });
        });
</script>