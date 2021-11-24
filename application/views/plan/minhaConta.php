<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card">
		<div class="card-title">
        	<h4><i class="icon-user"></i> <?= ucfirst($this->lang->line('user_count')); ?></h4>
        </div>
        <div class="card-body">	
        	<table class="table table-bordered table-striped">
				<tr>
					<td style="width: 30%">
						<?= ucfirst($this->lang->line('user_name')) ?>
					</td>
					<td class="text-left">
						<?= $usuario->nome; ?>
					</td>
				</tr>

				<tr>
					<td>
						<?= ucfirst($this->lang->line('user_email')) ?>
					</td>
					<td class="text-left">
						<?= $usuario->email; ?>
					</td>
				</tr>
                <tr>
					<td>
						<?= ucfirst($this->lang->line('user_phone')) ?>
					</td>
					<td class="text-left">
						<?= $usuario->telefone; ?>
					</td>
				</tr>
                
				<tr>
					<td>
						<?= ucfirst($this->lang->line('user_level')) ?>
					</td>
					<td class="text-left">
						<?= $usuario->permissao; ?>
					</td>
				</tr>
			</table>       
		</div>
	</div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card">
    	<div class="card-title">
        	<h4><i class="icon-lock"></i> <?= ucfirst($this->lang->line('user_change_pass')); ?></h4>
        </div>
        <div class="card-body">
        	<div class="form-body">
            	<form id="formSenha" action="<?php echo base_url();?>index.php/plan/alterarSenha" method="post">
                	<div class="form-group">
                    	<label for=""><?= ucfirst($this->lang->line('user_actual_password')); ?></label>
                        <input type="password" id="oldSenha" name="oldSenha" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for=""><?= ucfirst($this->lang->line('user_new_password')); ?></label>
                        <input type="password" id="novaSenha" name="novaSenha" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for=""><?= ucfirst($this->lang->line('user_confirm_password')); ?></label>
                        <input type="password" name="confirmarSenha" class="form-control" />
                    </div>
                        <button class="btn btn-primary"><?= ucfirst($this->lang->line('user_change_submit')); ?></button>
				</form>
			</div>
        </div>
    </div>
</div>


<script src="<?php echo base_url()?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        $('#formSenha').validate({
            rules :{
                  oldSenha: {required: true},  
                  novaSenha: { required: true},
                  confirmarSenha: { equalTo: "#novaSenha"}
            },
            messages:{
                  oldSenha: {required: 'Campo Requerido'},  
                  novaSenha: { required: 'Campo Requerido.'},
                  confirmarSenha: {equalTo: 'As senhas não combinam.'}
            },

            errorClass: "help-inline",
            errorElement: "span",
            highlight:function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
           });
    });
</script>