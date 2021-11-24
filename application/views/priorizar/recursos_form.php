<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
				<?= $button ?>
                <?= ucfirst($this->lang->line('amount')); ?>
              
                <hr>
            </div>
            <div class="card-body">
                <div class="form-body">
                   <form id="amountform" action="<?= $action; ?>" method="post">
                   <div class="row">
                   	 <div class="col-lg-4 col-md-4 col-sm-12">
                     	<div class="form-group">
                        	<label for="tipo_despesa">
                            	<?= ucfirst($this->lang->line('expense_type')) ?>
                            </label>
                            <?php 
                            	$options = array(
									'' => '',
                                	'c' => 'Consumo',
                                	's' => 'Serviço',
                                	'p' => 'Permanente',
                                    'd' => 'Diária'
                                );
                                echo form_dropdown('tipo_despesa', $options, $tipo_despesa, array('class' => 'form-control'));
                                echo form_error('tipo_despesa');
                           	?>
                        </div>
                     </div>
                     <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="valor">
                                <?= ucfirst($this->lang->line('value')) ?>
                            </label>
                            <input class="form-control money" id="valor" type="text" name="valor" value="<?= $valor; ?>" /> 
                            <?= form_error('valor') ?>
                        </div>
                     </div>
                     <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="data_autorizacao">
                                <?= ucfirst($this->lang->line('date')) ?>
                            </label>
                            <div class="input-group date">
  								<input type="text" class="form-control" name="data_autorizacao" id="data_autorizacao" value="<?= $data_autorizacao; ?>"><span class="input-group-addon"><i class="fa fa-calendar-alt"></i></span>
							</div>
                            <?= form_error('data_autorizacao') ?>
                        </div>
                     </div>
                   </div>
                        <input type="hidden" name="IdProjeto" value="<?= $IdProjeto; ?>" />
                        
                        <div><p>&nbsp;</p></div>
                        
                        <button type="submit" class="btn btn-info envia">
                            <?= $button ?>
                        </button>      
                        <a href="<?= site_url('priorizar') ?>" class="btn btn-dark">
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