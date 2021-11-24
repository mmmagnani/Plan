<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
                <form action="<?php echo current_url() ?>" class="form-group">

                    <div class="row form-group">
                        <div class="col col-md-12">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary">
                                        <i class="fa fa-search"></i> <?= $this->lang->line('app_search'); ?>
                                    </button>
                                </div>
                                <input type="text" class="form-control" name="termo" placeholder="<?= $this->lang->line('app_input_search'); ?>" />
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <p class="text-center lead"> <?= ucfirst($this->lang->line('targets')); ?></p>
                    <table class="table table-bordered ">
                        <thead>
                            <tr style="backgroud-color: #2D335B">
                                <th><?= ucfirst($this->lang->line('description')); ?></th>
                                <th><?= ucfirst($this->lang->line('app_actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if ($objetivos == null) {
                                    echo '<tr><td colspan="4">'.$this->lang->line('app_not_found').'</td></tr>';
                                }
                                foreach ($objetivos as $r) {
                                    echo '<tr>';
									echo '<td>' . $r->descricao . '</td>';
                                
                                    echo '<td>';
									
									if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vObjetivos')) {
                                        echo '<a href="' . site_url('objetivos/read/'). $r->IdObjetivo . '" class="btn btn-dark tip-top" title="'.$this->lang->line('app_view').'"><i class="fa fa-eye"></i></a>';
                                    }

                                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObjetivos')) {
                                        echo ' <a href="' . site_url('objetivos/update/'). $r->IdObjetivo . '" class="btn btn-info tip-top" title="'.$this->lang->line('app_edit').'"><i class="fa fa-edit"></i></a>';
                                    }
                                
                                    echo '</td>';
                                    echo '</tr>';
                                }?>
                        </tbody>
                    </table>
                </div>
                <hr>
				
                <div class="card-body">
                <div class="table-responsive">
                    <p class="text-center lead"> <?= ucfirst($this->lang->line('projects')); ?></p>
                    <table class="table table-bordered ">
                        <thead>
                            <tr style="backgroud-color: #2D335B">
                                <th><?= ucfirst($this->lang->line('title')); ?></th>
                                <th><?= ucfirst($this->lang->line('app_actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if ($projetos == null) {
                                    echo '<tr><td colspan="4">'.$this->lang->line('app_not_found').'</td></tr>';
                                }
                                foreach ($projetos as $r) {
                                    echo '<tr>';
									echo '<td>' . $r->titulo . '</td>';
                                
                                    echo '<td>';
									
									if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProjetos')) {
                                        echo '<a href="' . site_url('projetos/read/'). $r->IdProjeto . '" class="btn btn-dark tip-top" title="'.$this->lang->line('app_view').'"><i class="fa fa-eye"></i></a>';
                                    }

                                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProjetos')) {
                                        echo ' <a href="' . site_url('projetos/update/'). $r->IdProjeto . '" class="btn btn-info tip-top" title="'.$this->lang->line('app_edit').'"><i class="fa fa-edit"></i></a>';
                                    }
                                
                                    echo '</td>';
                                    echo '</tr>';
                                }?>
                        </tbody>
                    </table>
                </div>
                <hr>
                
				<div class="card-body">
                <div class="table-responsive">
                    <p class="text-center lead"> <?= ucfirst($this->lang->line('tasks')); ?></p>
                    <table class="table table-bordered ">
                        <thead>
                            <tr style="backgroud-color: #2D335B">
                                <th><?= ucfirst($this->lang->line('title')); ?></th>
                                <th><?= ucfirst($this->lang->line('app_actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if ($tarefas == null) {
                                    echo '<tr><td colspan="4">'.$this->lang->line('app_not_found').'</td></tr>';
                                }
                                foreach ($tarefas as $r) {
                                    echo '<tr>';
									echo '<td>' . $r->titulo . '</td>';
                                
                                    echo '<td>';
									
									if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTarefas')) {
                                        echo '<a href="' . site_url('tarefas/read/'). $r->IdTarefa . '" class="btn btn-dark tip-top" title="'.$this->lang->line('app_view').'"><i class="fa fa-eye"></i></a>';
                                    }

                                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eTarefas')) {
                                        echo ' <a href="' . site_url('tarefas/update/'). $r->IdTarefa . '" class="btn btn-info tip-top" title="'.$this->lang->line('app_edit').'"><i class="fa fa-edit"></i></a>';
                                    }
                                
                                    echo '</td>';
                                    echo '</tr>';
                                }?>
                        </tbody>
                    </table>
                </div>
                <hr>
			</div>
        </div> 
	</div>
</div>
<script>
    $(document).ready(function() {
		$('.tip-top').tooltip({ placement: 'top' });
    });
</script> 