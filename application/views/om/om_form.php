<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
                <h4>
                    <?= $button ?>
                        <?= ucfirst($this->lang->line('om')); ?>
                </h4>
                <hr>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="<?= $action; ?>" method="post">

                        <div class="row">

                            <div class="form-group col-md-3 col-sm-12">
                                <label for="sigla">
                                   <?= ucfirst($this->lang->line('sigla_ug')) ?>
                                </label>
                                <input type="text" class="form-control" name="sigla" id="sigla" value="<?= $sigla; ?>" />
                                <?= form_error('sigla') ?>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="nome">
                                   <?= ucfirst($this->lang->line('om')) ?>
                                </label>
                                <input type="text" class="form-control" name="nome" id="nome" value="<?= $nome; ?>" />
                                <?= form_error('nome') ?>
                            </div>
                            <div class="form-group col-md-3 col-sm-12">
                                <label for="cod_ug">
                                   <?= ucfirst($this->lang->line('cod_ug')) ?>
                                </label>
                                <input type="text" class="form-control" name="codigo" id="codigo" value="<?= $codigo; ?>" />
                                <?= form_error('codigo') ?>
                            </div>
                        </div>
                        <div class="row">
                        <?php
    						if($IdOm == 1)
   							{
	 							$display1 = 'style="display:none"';  
   							}
   							else
   							{
	 							$display1 = 'style="display:block"';
   							}
 						?>
                            <div id="div_apoiadora" class="form-group col-md-4 col-sm-12" <?= $display1 ?>>
                                <label for="apoiadora">
                                   <?= ucfirst($this->lang->line('supportive')) ?>
                                </label>
                                <?php 
                                        $options = array(
											'2' => '',
                                            '1' => $this->lang->line('app_yes'),
                                            '0' => $this->lang->line('app_no')
                                        );
                                        echo form_dropdown('apoiadora', $options, $apoiadora, array('class' => 'form-control', 'onchange' => 'mostraDiv(this.value)'));
                                        echo form_error('apoiadora');

                                    ?>
                            </div>
                            <?php  
   								if (($apoiadora == 1) || ($IdOm == 1) || ($IdOm == ''))
   								{
	 								$display = 'style="display:none"';  
   								}
   								else
   								{
	 								$display = 'style="display:block"';
   								}
 							?>
                            <div id="div_om_apoiadora" class="form-group col-md-4 col-sm-12" <?= $display ?>>
                            	<label for="om_id_apoiadora">
                                	<?= ucfirst($this->lang->line('om_supporter')) ?>
                                </label>
                                <?= form_dropdown('om_id_apoiadora', $apoiadoras, $om_id_apoiadora, array('class' => 'form-control')); ?>
                                  <?= form_error('om_id_apoiadora') ?>
                            </div>
                            <?php if($this->session->userdata('om_id') == 1) { ?>
                            <div class="form-group col-md-4 col-sm-12">
                            	<label for="situacao">
                                	<?= ucfirst($this->lang->line('om_status')) ?>
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
                            <?php } ?>
                        </div>
                        
                            
                        <input type="hidden" name="IdOm" value="<?= $IdOm; ?>" />
                        <button type="submit" class="btn btn-info">
                            <?= $button ?>
                        </button>
                        <a href="<?= site_url('om') ?>" class="btn btn-dark">
                            <i class="fa fa-reply"></i>
                            <?= $this->lang->line('app_cancel'); ?>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function mostraDiv(valor)

{

  if(valor == 0)

  {

    document.getElementById("div_om_apoiadora").style.display = "block";

  }

  else

  {

    document.getElementById("div_om_apoiadora").style.display = "none";

  }

}
</script>