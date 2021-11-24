<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
				<?= $button ?>
                <?= ucfirst($this->lang->line('projects')); ?>
              
                <hr>
            </div>
            <div class="card-body">
                <div class="form-body">
                   <form id="projectform" action="<?= $action; ?>" method="post">
                   <div class="row">
                   	 <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="form-group">
                            <label for="objetivo_id">
                                <?= ucfirst($this->lang->line('target')) ?>
                            </label>
                            <?= form_dropdown('objetivo_id', $objetivos, $objetivo_id, array('class' => 'form-control')); ?>
                            <?= form_error('objetivo_id') ?>
                        </div>
                     </div>
                   </div>
                   <div class="row">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="form-group">
                            <label for="titulo">
                                <?= ucfirst($this->lang->line('title')) ?>
                            </label>
                            <textarea class="form-control" name="titulo" id="titulo" style="resize:none" cols="50" rows="1" onkeyup="this.value = this.value.toUpperCase()"><?= $titulo; ?>
                            </textarea>
                            <?= form_error('titulo') ?>
                        </div>
                     </div>
                   </div>
                   <div class="row">
                     <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="descricao">
                                <?= ucfirst($this->lang->line('description')) ?>
                            </label>
                            <textarea class="form-control" name="descricao" id="descricao" style="resize:none" cols="50" rows="1" onkeyup="this.value = this.value.toUpperCase()"><?= $descricao; ?>
                            </textarea>
                            <?= form_error('descricao') ?>
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="gerset_id">
                                <?= ucfirst($this->lang->line('gerset')) ?>
                            </label>
                            <?= form_dropdown('gerset_id', $gersets, $gerset_id, array('class' => 'form-control')); ?>
                            <?= form_error('gerset_id') ?>
                        </div>
                     </div>
                   </div>
                   <div class="row">
                   	 <div class="col-lg-4 col-md-4 col-sm-12">
                     	<div class="form-group">
                        	<label for="abrangencia">
                            	<?= ucfirst($this->lang->line('scope')) ?>
                            </label>
                            <?php 
                            	$options = array(
									'' => '',
                                	'1' => ucfirst($this->lang->line('app_restricted')),
                                    '0' => ucfirst($this->lang->line('app_general'))
                                );
                                echo form_dropdown('abrangencia', $options, $abrangencia, array('class' => 'form-control'));
                                echo form_error('abrangencia');
                           	?>
                        </div>
                     </div>
                     <div class="col-lg-4 col-md-4 col-sm-12">
                     	<div class="form-group">
                        	<label for="obras">
                            	<?= ucfirst($this->lang->line('construction')) . ' ' . $this->lang->line('or') . ' ' . $this->lang->line('services') ?>?
                            </label>
                            <?php 
                            	$options = array(
									'' => '',
                                	'1' => $this->lang->line('app_yes'),
                                    '0' => $this->lang->line('app_no')
                                );
                                echo form_dropdown('obras', $options, $obras, array('class' => 'form-control'));
                                echo form_error('obras');
                           	?>
                        </div>
                     </div>
                     <div class="col-lg-4 col-md-4 col-sm-12">
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
                        <input type="hidden" name="IdProjeto" value="<?= $IdProjeto; ?>" />
                        <input type="hidden" name="om_id" value="<?= $this->session->userdata('om_id'); ?>" />
                        <div><p>&nbsp;</p></div>
                        
                        <button type="submit" class="btn btn-info envia">
                            <?= $button ?>
                        </button>      
                        <a href="<?= site_url('projetos') ?>" class="btn btn-dark">
                            <i class="fa fa-reply"></i>
                            <?= $this->lang->line('app_cancel'); ?>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>