<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
                <h4>
                    <?= $button ?>
                        <?= ucfirst($this->lang->line('status')); ?>
                </h4>
                <hr>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="<?= $action; ?>" method="post">

                        <div class="row">
							<?php  
   								if ($this->session->userdata('om_id') != 1)
   								{
	 								$display = 'style="display:none"';  
   								}
   								else
   								{
	 								$display = 'style="display:block"';
   								}
 							?>
                            <div class="form-group col-md-3 col-sm-12" <?= $display ?>>
                            	<label for="om_id">
                                	<?= ucfirst($this->lang->line('om')) ?>
                                </label>
                                <?= form_dropdown('om_id', $oms, $om_id, array('class' => 'form-control')); ?>
                                  <?= form_error('om_id') ?>
                            </div>
                            <div class="form-group col-md-8 col-sm-12">
                                <label for="descricao">
                                   <?= ucfirst($this->lang->line('perm_status')) ?>
                                </label>
                                <input type="text" class="form-control" name="status_desc" id="status_desc" value="<?= $status_desc; ?>" onkeyup="this.value = this.value.toUpperCase()" />
                                <?= form_error('status_desc') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3 col-sm-12">
                            	<label for="situacao">
                                	<?= ucfirst($this->lang->line('sector_status')) ?>
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
                    <?php  
   					  if ($this->session->userdata('om_id') != 1) {  ?>
                        <input type="hidden" name="om_id" value=" <?= $this->session->userdata('om_id') ?>" />    
					<?php } ?>	 
                        <input type="hidden" name="idStatus" value="<?= $idStatus; ?>" />
                        <button type="submit" class="btn btn-info">
                            <?= $button ?>
                        </button>
                        <a href="<?= site_url('status') ?>" class="btn btn-dark">
                            <i class="fa fa-reply"></i>
                            <?= $this->lang->line('app_cancel'); ?>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
