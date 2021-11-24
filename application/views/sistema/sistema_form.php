<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
                <h4>
                    <?= $button ?>
                        <?= ucfirst($this->lang->line('system')); ?>
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
                            <div class="form-group col-md-4 col-sm-12" <?= $display ?>>
                            	<label for="om_id">
                                	<?= ucfirst($this->lang->line('om')) ?>
                                </label>
                                <?= form_dropdown('om_id', $oms, $om_id, array('class' => 'form-control')); ?>
                                  <?= form_error('om_id') ?>
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="margem_reserva">
                                    <?= ucfirst($this->lang->line('reserve_margin')) ?>
                                </label>
                                <input type="text" class="form-control" name="margem_reserva" id="margem_reserva" value="<?= $margem_reserva; ?>" />
                                <?= form_error('margem_reserva') ?>
                            </div>
                            

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="bloqueio">
                                    <?= ucfirst($this->lang->line('block')) ?>
                                </label>
                                <?=  form_dropdown('bloqueio', array('0' => $this->lang->line('app_no'), '1' => $this->lang->line('app_yes')), $bloqueio, array('class' => 'form-control') ); ?> 
                                <?= form_error('bloqueio') ?>
                            </div>
                        </div>


                        <input type="hidden" name="IdConfig" value="<?= $IdConfig; ?>" />
                        <button type="submit" class="btn btn-info">
                            <?= $button ?>
                        </button>
                        <a href="<?= site_url('sistema') ?>" class="btn btn-dark">
                            <i class="fa fa-reply"></i>
                            <?= $this->lang->line('app_cancel'); ?>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
