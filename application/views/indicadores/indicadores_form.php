<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
                <h4>
                    <?= $button ?>
                        <?= ucfirst($this->lang->line('indicators')); ?>
                </h4>
                <hr>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="<?= $action; ?>" method="post">

                        <div class="row">
							
                            <input type="hidden" name="om_id" id="om_id" value="<?= $this->session->userdata('om_id'); ?>" />
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="descricao">
                                   <?= ucfirst($this->lang->line('description')) ?>
                                </label>
                                <textarea class="form-control" name="descricao" id="descricao" cols="50" rows="5" onkeyup="this.value = this.value.toUpperCase()"><?= $descricao; ?>
                            </textarea>
                            <?= form_error('descricao') ?>                              
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="formula">
                                   <?= ucfirst($this->lang->line('formula')) ?>
                                </label>
                                <textarea class="form-control" name="formula" id="formula" style="resize:none" cols="50" rows="5"><?= $formula; ?>
                            </textarea>
                            <?= form_error('formula') ?>                              
                            </div>
                        </div>
                        <div class="row">
                        	<div class="form-group col-md-6 col-sm-12">
                                <label for="objetivo">
                                   <?= ucfirst($this->lang->line('indicator_target')) ?>
                                </label>
                                <textarea class="form-control" name="objetivo" id="objetivo" style="resize:none" cols="50" rows="5" onkeyup="this.value = this.value.toUpperCase()"><?= $objetivo; ?>
                            </textarea>
                            <?= form_error('objetivo') ?>                              
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="origem_dados">
                                   <?= ucfirst($this->lang->line('data_source')) ?>
                                </label>
                                <textarea class="form-control" name="origem_dados" id="origem_dados" style="resize:none" cols="50" rows="5" onkeyup="this.value = this.value.toUpperCase()"><?= $origem_dados; ?>
                            </textarea>
                            <?= form_error('origem_dados') ?>                        
                            </div>
                        </div>
                        <div class="row">
                        	<div class="form-group col-md-6 col-sm-12">
                                <label for="vantagem_sefa">
                                   <?= ucfirst($this->lang->line('advantage_sefa')) ?>
                                </label>
                                <textarea class="form-control" name="vantagem_sefa" id="vantagem_sefa" style="resize:none" cols="50" rows="5" onkeyup="this.value = this.value.toUpperCase()"><?= $vantagem_sefa; ?>
                            </textarea>
                            <?= form_error('vantagem_sefa') ?>                              
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="vantagem_om">
                                   <?= ucfirst($this->lang->line('advantage_om')) ?>
                                </label>
                                <textarea class="form-control" name="vantagem_om" id="vantagem_om" style="resize:none" cols="50" rows="5" onkeyup="this.value = this.value.toUpperCase()"><?= $vantagem_om; ?>
                            </textarea>
                            <?= form_error('vantagem_om') ?>                              
                            </div>
                        </div>
                        <div class="row">
                        	<div class="form-group col-md-4 col-sm-12">
                                <label for="gerset_id">
                                   <?= ucfirst($this->lang->line('gerset')) ?>
                                </label>
                                <?= form_dropdown('gerset_id', $gersets, $gerset_id, array('class' => 'form-control')); ?>
                                <?= form_error('gerset_id') ?>                                
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                            	<label for="periodicidade_id">
                                	<?= ucfirst($this->lang->line('frequency')) ?>
                                </label>
                                <?php 
                                    $options = array(
										'' => '',
                                        '1' => $this->lang->line('monthly'),
                                        '2' => $this->lang->line('bimonthly'),
										'5' => $this->lang->line('trimonthly'),
										'3' => $this->lang->line('semiannualy'),
										'4' => $this->lang->line('annualy')
                                    );
                                    echo form_dropdown('periodicidade_id', $options, $periodicidade_id, array('class' => 'form-control'));
                                    echo form_error('periodicidade_id');

                                ?>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                            	<label for="tipoindicador_id">
                                	<?= ucfirst($this->lang->line('indicator_type')) ?>
                                </label>
                                <?php 
                                    $options = array(
										'' => '',
                                        '1' => $this->lang->line('effectiveness'),
                                        '2' => $this->lang->line('efficiency'),
										'3' => $this->lang->line('effect'),
										'4' => $this->lang->line('economy'),
										'5' => $this->lang->line('execution')
                                    );
                                    echo form_dropdown('tipoindicador_id', $options, $tipoindicador_id, array('class' => 'form-control'));
                                    echo form_error('tipoindicador_id');

                                ?>
                            </div>
                         </div>
                         <div class="row">
                         	<div class="form-group col-md-4 col-sm-12">
                            	<label for="meta">
                                	<?= ucfirst($this->lang->line('goal')) ?>
                            	</label>
                            	<input type="text" class="form-control" name="meta" id="meta" value="<?= $meta; ?>" />
                            	<?= form_error('meta') ?>
                        	</div>
                            <div class="form-group col-md-4 col-sm-12">
                            	<label for="meta2">
                                	<?= ucfirst($this->lang->line('goal')) . ' 2' ?>
                            	</label>
                            	<input type="text" class="form-control" name="meta2" id="meta2" value="<?= $meta2; ?>" />
                            	<?= form_error('meta2') ?>
                        	</div>
                            <div class="form-group col-md-4 col-sm-12">
                            	<label for="unidade_meta">
                                	<?= ucfirst($this->lang->line('goal_unit')) ?>
                            	</label>
                            	<input type="text" class="form-control" name="unidade_meta" id="unidade_meta" value="<?= $unidade_meta; ?>" />
                            	<?= form_error('unidade_meta') ?>
                        	</div>
                         </div>
                         <div class="row">
                            <div class="form-group col-md-9 col-sm-12">
                                <label for="projeto_id">
                                   <?= ucfirst($this->lang->line('project')) ?>
                                </label>
                                <?= form_dropdown('projeto_id', $projetos, $projeto_id, array('class' => 'form-control')); ?>
                                <?= form_error('projeto_id') ?>                                
                            </div>
                            <div class="form-group col-md-3 col-sm-12">
                            	<label for="situacao">
                                	<?= ucfirst($this->lang->line('indicator_status')) ?>
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
                        
                            
                        <input type="hidden" name="IdIndicador" value="<?= $IdIndicador; ?>" />
                        <button type="submit" class="btn btn-info">
                            <?= $button ?>
                        </button>
                        <a href="<?= site_url('indicadores') ?>" class="btn btn-dark">
                            <i class="fa fa-reply"></i>
                            <?= $this->lang->line('app_cancel'); ?>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
