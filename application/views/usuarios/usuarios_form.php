<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
                <h4>
                    <?= $button ?>
                        <?= ucfirst($this->lang->line('user')); ?>
                </h4>
                <hr>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="<?= $action; ?>" method="post">
                    
                    <div class="row">
                      <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="om_id">
                                <?= ucfirst($this->lang->line('om')) ?>
                            </label>
                            <?= form_dropdown('om_id', $oms, $om_id, array('class' => 'form-control', 'id' => 'om_id')); ?>
                            <?= form_error('om_id') ?>
                        </div>
					  </div>
					  <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="setor_id">
                                <?= ucfirst($this->lang->line('sector')) ?>
                            </label>
                            <?php if(($IdUsuarios == '') && ($this->session->userdata('om_id') == 1)) {
									  $disabled = 'disabled';
								  } else {
									  $disabled = '';
								  } ?>
                            
                            <select class="form-control" name="setor_id" id="setor_id" <?= $disabled ?>>
                             <?php if(($IdUsuarios != '') || ($this->session->userdata('om_id') != 1)){
							    echo '<option>'. $this->lang->line('app_select_sector') . '</option>';
							    foreach ($setores as $s) {
								    $selected = '';
									if ($s->IdSetor == $setor_id)
									{
										$selected = 'selected';										
									}
									echo '<option value="' . $s->IdSetor . '"' . $selected . '>' . $s->sigla . ' - ' . $s->descricao . ' </option>';
								  }
							 } else {
							?>
                            	<option><?= $this->lang->line('app_select_om_first') ?></option>
                            <?php } ?>
                            </select>
                            <?= form_error('setor_id') ?>
                        </div>
					  </div>
					</div>
                    
					<div class="row">
                      <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="nome">
                                <?= ucfirst($this->lang->line('user_name')) ?>
                            </label>
                            <input type="text" class="form-control" name="nome" id="nome" onkeyup="this.value = this.value.toUpperCase()" value="<?= $nome; ?>" />
                            <?= form_error('nome') ?>
                        </div>
					  </div>
					  <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="usu_email">
                                <?= ucfirst($this->lang->line('user_email')) ?>
                            </label>
                            <input type="text" class="form-control" name="email" id="email" value="<?= $email; ?>" />
                            <?= form_error('email') ?>
                        </div>
					  </div>
					</div>
					<div class="row">
					  <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="telefone">
                                <?= ucfirst($this->lang->line('user_phone')) ?>
                            </label>
                            <input type="text" class="form-control phone" name="telefone" id="telefone" value="<?= $telefone; ?>" />
                            <?= form_error('telefone') ?>
                        </div>
					  </div>
					  <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="cpf">
                                <?= ucfirst($this->lang->line('cpf')) ?>
                            </label>
                            <input type="text" class="form-control cpf" name="cpf" id="cpf" value="<?= $cpf; ?>" />
                            <?= form_error('cpf') ?>
                        </div>
					  </div>
					  <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="senha">
                                <?= ucfirst($this->lang->line('user_password')) ?>
                            </label>
                            <input type="password" class="form-control" name="senha" id="senha" value="<?= $senha; ?>" placeholder="<?= $IdUsuarios != '' ? $this->lang->line('user_change_password') : ''; ?>" />
                            <?= form_error('senha') ?>
                        </div>
					  </div>
					  
					</div>
                    <div class="row">
					  <div class="col-lg-6 col-md-6 col-sm-12">
						<div class="form-group">
							<label for="situacao">
								<?= ucfirst($this->lang->line('user_status')) ?>
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
					  <div class="col-lg-6 col-md-6 col-sm-12">
					    <div class="form-group">
							<label for="permissoes_id">
								<?= ucfirst($this->lang->line('user_group')) ?>
							</label>
							<?= form_dropdown('permissoes_id', $permissoes, $permissoes_id, array('class' => 'form-control')); ?>
							 <?= form_error('permissoes_id') ?>
						</div>
					  </div>
                    </div>
					
					<input type="hidden" name="IdUsuarios" value="<?= $IdUsuarios; ?>" />
					<button type="submit" class="btn btn-info">
						<?= $button ?>
					</button>
					<a href="<?= site_url('usuarios') ?>" class="btn btn-dark">
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
   	$(function(){
   		$('#om_id').change(function(){
		$('#setor_id').attr('disabled','disabled');
			$('#setor_id').html("<option>Carregando...</option>");
			var om_id = $('#om_id').val();
			$.post("<?= site_url('usuarios/getSetores'); ?>",{
				om_id : om_id
			}, function(data){
				$('#setor_id').html(data);
				$('#setor_id').removeAttr('disabled');
			});
		});
	});
</script>