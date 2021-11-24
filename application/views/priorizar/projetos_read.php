<link href="<?= base_url('assets/css/priority.css'); ?>" rel="stylesheet">
<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card">
		<div class="card-title">
			<h4>
				<i class="fa fa-sort"></i>
				<?= ucfirst($this->lang->line('prioritize_project_tasks')); ?>
			</h4>
		</div>
		<div class="card-body">
        	<div align="right">
				<?= anchor(site_url('priorizar/exportar/'.$IdProjeto),'<i class="fa fa-file-download"></i> ' . $this->lang->line('export_to_plan'), 'class="btn btn-danger"'); ?>
            </div>
			<div class="tabs">
				<ul class="nav nav-tabs customtab2" role="tablist">
					<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#info" role="tab" aria-selected="true"><span class="hidden-sm-up"><i class="ti-menu-alt"></i></span> <span class="hidden-xs-down"><?= ucfirst($this->lang->line('project')); ?></span> </a> </li>
					<li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#tasks" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-check-box"></i></span> <span class="hidden-xs-down"><?= ucfirst($this->lang->line('tasks')); ?></span> </a> </li>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content col-lg-12 col-md-12 col-sm-12">
                <p>&nbsp;</p>
					<div class="tab-pane" id="info" role="tabpanel">
						<table class="table table-bordered table-striped col-lg-12 col-md-12 col-sm-12">
							<tr>
								<td style="width: 30%">
									<?= ucfirst($this->lang->line('title')) ?>
								</td>
								<td class="text-left">
									<?= $titulo; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('description')) ?>
								</td>
								<td class="text-left">
									<?= $descricao; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('gerset')) ?>
								</td>
								<td class="text-left">
									<?= $gerset; ?>
								</td>
							</tr> 
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('scope')) ?>
								</td>
								<td class="text-left">
									<?= $abrangencia; ?>
								</td>
							</tr>                                            
						</table>
					</div>
					<div class="tab-pane active show" id="tasks" role="tabpanel" style="min-height: 300px">
			<?php if (($this->permission->checkPermission($this->session->userdata('permissao'), 'fConselho')) || ($this->permission->checkPermission($this->session->userdata('permissao'), 'fGerset'))) {  ?>
                    <div class="alert alert-info" align="center"> <?= ucfirst($this->lang->line('draggable_and_sort')); ?></div>
			<?php } ?>
				<?php if (!$tarefas) { ?>
				          
                        <table class="table table-responsive table-striped">
                            <thead>
                                <tr style="backgroud-color: #2D335B">
                                	<th><?= ucfirst($this->lang->line('priority')); ?></th>
                                    <th><?= ucfirst($this->lang->line('numspo')); ?></th>
                                    <th><?= ucfirst($this->lang->line('title')); ?></th>
                                    <th><?= ucfirst($this->lang->line('description')); ?></th>
                                    <th><?= ucfirst($this->lang->line('justification')); ?></th>
                                    <th><?= ucfirst($this->lang->line('estimated_cost')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6"><?= ucfirst($this->lang->line('app_empty')); ?></td>
                                </tr>
                            </tbody>
                        </table>
                
                <?php } else { ?>

                        <table class="table table-striped" id="resultado">
                            <thead>
                                <tr style="backgroud-color: #2D335B">
                                	<th><?= ucfirst($this->lang->line('priority')); ?></th>
                                    <th><?= ucfirst($this->lang->line('numspo')); ?></th>
                                    <th><?= ucfirst($this->lang->line('title')); ?></th>
                                    <th><?= ucfirst($this->lang->line('description')); ?></th>
                                    <th><?= ucfirst($this->lang->line('justification')); ?></th>
                                    <th><?= ucfirst($this->lang->line('estimated_cost')); ?></th>
                                </tr>
                            </thead>   
							<?php if (($this->permission->check($this->session->userdata('permissao'), 'fConselho')) || ($this->permission->check($this->session->userdata('permissao'), 'fGerset'))) {  
									  $sortable = 'class="container sortable"';
								  } else {
								      $sortable = 'class="container"';
								  }    
							?>            
                            <tbody <?= $sortable; ?>>		                             
						<?php foreach ($tarefas as $r) {
						
								echo '<tr class="item" id="' . $r->IdTarefa . '">';
								echo '<td>' . $r->prioridade . '</td>';
								echo '<td><a href="' . site_url('tarefas/read/' . $r->IdTarefa) . '">' . $r->spo_id . '</a></td>';
								echo '<td class="tip-top tarefa" title="' . $r->titulo.'">' . $r->titulo . '</td>';
								echo '<td class="tip-top tarefa" title="' . $r->descricao . '">' . $r->descricao . '</td>';
								echo '<td class="tip-top tarefa" title="' . $r->justificativa . '">' . $r->justificativa . '</td>';
								echo '<td class="custo" align="right">' . number_format($r->valor_previsto, 2, ',',
									'.') . '</td>';
								echo '</tr>';
						} ?>                          
                        	</tbody>           
                        </table>
            
            	<?php } ?>
				
					</div>
				</div>
			</div>

			<hr>

			<a href="<?= site_url('priorizar') ?>" class="btn btn-dark">
				<i class="fa fa-reply"></i>
				<?= $this->lang->line('app_back'); ?>
			</a>

		</div>
	</div>
</div>
<script>
	$(function(){
       
            $(".sortable").sortable({
                connectWith: ".sortable",
                placeholder: 'dragHelper',
                scroll: true,
                revert: true,
                cursor: "move",
                update: function(event, ui) {
                     var tar_id_item_list = $(this).sortable('toArray').toString();
					 var tar_id_proj = "<?= $IdProjeto; ?>"
                     $.ajax({
                         url: "<?= site_url('priorizar/setOrdem') ?>",
                         type: 'POST',
                         data: {tar_id_item : tar_id_item_list, tar_id_proj : tar_id_proj},
                         success: function(data) {
    					 location.reload(true);
                         }
                     });
                },
                start: function( event, ui ) {
                                                
                },
                stop: function( event, ui ) {
                     
                }
            });
        });
</script>

                            

