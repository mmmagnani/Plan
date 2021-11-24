<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
				<?= $button ?>
                <?= ucfirst($this->lang->line('commitments')); ?>
              
                <hr>
            </div>
            <div class="card-body">
                <div class="form-body">
                   <form id="commitform" action="<?= $action; ?>" method="post">
                   <div class="row">
                     <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="titulo">
                                <?= ucfirst($this->lang->line('commitment')) ?>
                            </label>
                            <input class="form-control" name="empenho" id="empenho" onkeyup="this.value = this.value.toUpperCase()" value="<?= $empenho; ?>" />
                            <?= form_error('empenho') ?>
                        </div>
                     </div>
                     <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-group">
                   	        <label for="data_empenho">
                                <?= ucfirst($this->lang->line('date_commitment')) ?>
                            </label>
                            
                            <div class="input-group date">
  								<input type="text" class="form-control" name="data_empenho" id="data_empenho" value="<?= $data_empenho; ?>"><span class="input-group-addon"><i class="fa fa-calendar-alt"></i></span>
							</div>
 
                            <?= form_error('data_empenho') ?>
                        </div>
                     </div>
                   	 <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-group">
                   	        <label for="valor_empenho">
                                <?= ucfirst($this->lang->line('commitment_val')) ?>
                            </label>
                            <input type="text" class="form-control money" name="valor_empenho" id="valor_empenho" value="<?= $valor_empenho; ?>" />
                            <?= form_error('valor_empenho') ?>
                        </div>
                     </div>
                   </div>
                        <input type="hidden" name="IdExecucao" value="<?= $IdExecucao; ?>" />
                        <input type="hidden" name="om_id" value="<?= $this->session->userdata('om_id'); ?>" />
                        <input type="hidden" name="tarefa_id" value="<?= $tarefa_id; ?>" />
                        
                        <div><p>&nbsp;</p></div>
                        
                        <button type="submit" class="btn btn-info envia">
                            <?= $button ?>
                        </button>      
                        <a href="<?= site_url('tarefas/read/' . $tarefa_id) ?>" class="btn btn-dark">
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