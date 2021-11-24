<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
				<?= $button ?>
                <?= ucfirst($this->lang->line('targets')); ?>
              
                <hr>
            </div>
            <div class="card-body">
                <div class="form-body">
                    <form id="targetform" action="<?= $action; ?>" method="post">
                   <div class="row">
                     <div class="col-lg-7 col-md-7 col-sm-12">
                        <div class="form-group">
                            <label for="descricao">
                                <?= ucfirst($this->lang->line('description')) ?>
                            </label>
                            <textarea class="form-control" name="descricao" id="descricao" style="resize:none" cols="50" rows="5" onkeyup="this.value = this.value.toUpperCase()"><?= $descricao; ?>
                            </textarea>
                            <?= form_error('descricao') ?>
                        </div>
                     </div>
                     <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="perspectiva_id">
                                <?= ucfirst($this->lang->line('perspective')) ?>
                            </label>
                            <?= form_dropdown('perspectiva_id', $perspectivas, $perspectiva_id, array('class' => 'form-control')); ?>
                            <?= form_error('perspectiva_id') ?>
                        </div>
                     </div>
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
                                echo form_error('situacao');
                           	?>
                        </div>
                     </div>
                   </div>
                        <input type="hidden" name="IdObjetivo" value="<?= $IdObjetivo; ?>" />
                        <input type="hidden" name="om_id" value="<?= $this->session->userdata('om_id'); ?>" />
                        <div><p>&nbsp;</p></div>
                        
                        <button type="submit" class="btn btn-info envia">
                            <?= $button ?>
                        </button>      
                        <a href="<?= site_url('objetivos') ?>" class="btn btn-dark">
                            <i class="fa fa-reply"></i>
                            <?= $this->lang->line('app_cancel'); ?>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>