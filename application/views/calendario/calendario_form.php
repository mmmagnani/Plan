<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
<?= $button ?>
                    <?= ucfirst($this->lang->line('calendar')); ?>
              
                <hr>
            </div>
            <div class="card-body">
                <div class="form-body">
                  <form action="<?= $action; ?>" method="post">
                   <div class="row"> 
					 <?php
                     // Show IF Conditional region 
                      if($idCalendario == "") { ?>
                            <input type="hidden" class="form-control" name="ano_calendario" id="ano_calendario" value="<?= $ano_calendario; ?>"  />
					 <?php } else { ?>
                     <div class="col-lg-2 col-md-2 col-sm-12">
                        <div class="form-group">
                        	<label for="ano_calendario">
                                <?= ucfirst($this->lang->line('year')) ?>
                            </label>
                            <?php
                            	$options = array();
								$data_inicial = $this->session->userdata('anofiscal');
								for($i=0;$i<5;$i++){
									$options[$data_inicial + $i] = $data_inicial + $i;
								}
								echo form_dropdown('ano_calendario', $options, $ano_calendario, array('class' => 'form-control'));
                             form_error('ano_calendario') ?>
                        </div>
                     </div>
					 <?php } 
                     // End conditional region
                     ?>                                        
                     <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="objeto">
                                <?= ucfirst($this->lang->line('object')) ?>
                            </label>
                            <input type="text" class="form-control" name="objeto" id="objeto" onkeyup="this.value = this.value.toUpperCase()" value="<?= $objeto; ?>"  />
                            <?= form_error('objeto') ?>
                        </div>
                     </div>    
                       
                     <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="valor_estimado">
                                <?= ucfirst($this->lang->line('estimated_val')) ?>
                            </label>
                            <input type="text" class="form-control money" name="valor_estimado" id="valor_estimado" value="<?= $valor_estimado; ?>" />
                            <?= form_error('valor_estimado') ?>
                        </div>
                     </div>
                   </div>
                   <div class="row">
                     <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="homol_pretendida">
                                <?= ucfirst($this->lang->line('intended_approval_date')) ?>
                            </label>
                            <div class="input-group date">
  								<input type="text" class="form-control" name="homol_pretendida" id="homol_pretendida" value="<?= $homol_pretendida; ?>"><span class="input-group-addon"><i class="fa fa-calendar-alt"></i></span>
							</div>
                            <?= form_error('homol_pretendida') ?>
                        </div>
                     </div>                        
                     <div class="col-lg-6 col-md-6 col-sm-12">
                         <div class="form-group">
                         <label for="observacao">
                             <?= ucfirst($this->lang->line('observation')) ?>
                         </label>
                         <textarea class="form-control" name="observacao" id="observacao" cols="50" rows="3" style="resize:none" onkeyup="this.value = this.value.toUpperCase()"><?= $observacao ?></textarea>
                         <?= form_error('observacao') ?>
                         </div>
                     </div>
					<?php
                     // Show IF Conditional region 
                      if($idCalendario == "") { ?>
                            <input type="hidden" class="form-control" name="situacao" id="situacao" value="<?= $situacao; ?>"  />
					 <?php } else { ?>
                     <div class="col-lg-2 col-md-2 col-sm-12">
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
                             form_error('situacao') ?>
                        </div>
                     </div>
                   </div>
                   <div class="row">
                     <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="gerset">
                                <?= ucfirst($this->lang->line('gerset')) ?>
                            </label>
                            <input type="text" class="form-control" name="gerset" id="gerset" value="<?= $gerset; ?>" onkeyup="this.value = this.value.toUpperCase()" />
                            <?= form_error('gerset') ?>
                        </div>
                     </div>
                     <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="data_ent_gap">
                                <?= ucfirst($this->lang->line('date_gap_in')) ?>
                            </label>
                            <div class="input-group date">
  								<input type="text" class="form-control" name="data_ent_gap" id="data_ent_gap" value="<?= $data_ent_gap; ?>"><span class="input-group-addon"><i class="fa fa-calendar-alt"></i></span>
							</div>
                            <?= form_error('data_ent_gap') ?>
                        </div>
                     </div>
                     <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="tipo">
                                <?= ucfirst($this->lang->line('bidding_type')) ?>
                            </label>
                            <?= form_dropdown('tipo', $tipos, $tipo, array('class' => 'form-control')); ?>
                            <?= form_error('projeto_id') ?>                                
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="homol_efetiva">
                                <?= ucfirst($this->lang->line('real_approval_date')) ?>
                            </label>
                            <div class="input-group date">
  								<input type="text" class="form-control" name="homol_efetiva" id="homol_efetiva" value="<?= $homol_efetiva; ?>"><span class="input-group-addon"><i class="fa fa-calendar-alt"></i></span>
							</div>
                            <?= form_error('homol_efetiva') ?>
                        </div>
                     </div>
                     <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="status_id">
                                <?= ucfirst($this->lang->line('process_progress')) ?>
                            </label>
                            <?= form_dropdown('status_id', $status, $status_id, array('class' => 'form-control')); ?>
                            <?= form_error('status_id') ?>                                
                        </div>
                     </div>
                   <?php } 
                     // End conditional region
                   ?>
                   </div>             
                   <input type="hidden" name="idCalendario" value="<?= $idCalendario; ?>" />
                   <input type="hidden" name="om_id" value="<?= $om_id; ?>" />
                   <div><p>&nbsp;</p></div>
                        
                   <button type="submit" class="btn btn-info envia">
                       <?= $button ?>
                   </button> 
                   <a href="<?= site_url('calendario') ?>" class="btn btn-dark">
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
$(document).ready(function(){

    $('.input-group.date').datepicker({ 
		    format: "dd/mm/yyyy",
    		todayBtn: "linked",
			orientation: "top left",
    		language: "pt-BR",
    		autoclose: true,
    		todayHighlight: true,
			zIndexOffset: 100
	});
   
});
</script>