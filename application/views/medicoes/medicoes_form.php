<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
                <h4>
                    <?= $button ?>
                        <?= ucfirst($this->lang->line('measurements')); ?>
                </h4>
                <hr>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="<?= $action; ?>" method="post">
                        <div class="row">
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="data">
                                   <?= ucfirst($this->lang->line('date')) . " (" . $this->lang->line('last_business_day') . ")"?>
                                </label>
                               	<div class="input-group date">
                              		<input type="text" class="form-control datepicker" name="data" id="data" value="<?= $data; ?>" />
                              		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                              		<?= form_error('data') ?>
                              	</div>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                            	<label for="medicao">
                                	<?= ucfirst($this->lang->line('measurement')) ?>
                                </label>
                                <input type="text" class="form-control number" name="medicao" id="medicao" value="<?= $medicao; ?>" />
                                <?= form_error('medicao'); ?>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="observacao">
                                   <?= ucfirst($this->lang->line('observation')) ?>
                                </label>
                                <textarea class="form-control" name="observacao" id="observacao" style="resize:none" cols="50" rows="5" onkeyup="this.value = this.value.toUpperCase()"><?= $observacao; ?></textarea>
                                <?= form_error('observacao') ?>
                            </div>
                        </div>
                        
                            
                        <input type="hidden" name="IdRegistro" value="<?= $IdRegistro; ?>" />
                        <input type="hidden" name="indicador_id" value="<?= $indicador_id; ?>" />
                        <input type="hidden" name="ano" value="<?= $ano; ?>" />
                        <button type="submit" class="btn btn-info">
                            <?= $button ?>
                        </button>
                        <a href="<?= site_url('medicoes?id=') ?><?= $indicador_id; ?>" class="btn btn-dark">
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