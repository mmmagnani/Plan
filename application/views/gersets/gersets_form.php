<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
                <h4>
                    <?= $button ?>
                        <?= ucfirst($this->lang->line('gerset')); ?>
                </h4>
                <hr>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="<?= $action; ?>" method="post">

                        <div class="row">
							
                            <input type="hidden" name="om_id" id="om_id" value="<?= $this->session->userdata('om_id'); ?>" />
                            <div class="form-group col-md-8 col-sm-12">
                                <label for="setor_id">
                                   <?= ucfirst($this->lang->line('sector')) ?>
                                </label>
                                <?= form_dropdown('setor_id', $setores, $setor_id, array('class' => 'form-control')); ?>
                                <?= form_error('setor_id') ?>                                
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
                        
                            
                        <input type="hidden" name="IdGerset" value="<?= $IdGerset; ?>" />
                        <button type="submit" class="btn btn-info">
                            <?= $button ?>
                        </button>
                        <a href="<?= site_url('gersets') ?>" class="btn btn-dark">
                            <i class="fa fa-reply"></i>
                            <?= $this->lang->line('app_cancel'); ?>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
