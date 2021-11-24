<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-title">
                <h4>
                    <?= $button ?>
                        <?= ucfirst($this->lang->line('permissao')); ?>
                </h4>
                <hr>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="<?= $action; ?>" method="post">

                        <div class="row">

                            <div class="form-group col-md-8 col-sm-12">
                                <label for="nome">
                                    <?= ucfirst($this->lang->line('perm_name')) ?>
                                </label>
                                <input type="text" class="form-control" name="nome" id="nome" value="<?= $nome; ?>" />
                                <?= form_error('nome') ?>
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="situacao">
                                    <?= ucfirst($this->lang->line('perm_status')) ?>
                                </label>
                                <?=  form_dropdown('situacao', array('1' => $this->lang->line('app_active'), '0' => $this->lang->line('app_inactive')), $situacao, array('class' => 'form-control') ); ?> 
                                <?= form_error('situacao') ?>
                            </div>
                        </div>

                        <div class="form-group">

                            <table class="table table-bordered">
                                <thead>
               						<tr>
                                    	<th class="text-center" colspan="5">
                                        	<?= mb_strtoupper($this->lang->line('permissions')); ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <label>
                                                <input name="" type="checkbox" value="1" id="marcarTodos" />
                                                <span class="lbl"> <?= $this->lang->line('app_check_all'); ?></span>
                                            </label>
                                        </th>
                                        <th><?= strtoupper($this->lang->line('app_view')); ?></th>
                                        <th><?= strtoupper($this->lang->line('app_create')); ?></th>
                                        <th><?= strtoupper($this->lang->line('app_edit')); ?></th>
                                        <th><?= strtoupper($this->lang->line('app_delete')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
									<tr>
                                        <td>
                                                <?= ucfirst($this->lang->line('targets')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['vObjetivos']) == '1'? 'checked' : '' ?> name="vObjetivos" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['aObjetivos']) == '1' ? 'checked' : '' ?> name="aObjetivos" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['eObjetivos']) =='1'? 'checked' : '' ?> name="eObjetivos" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['dObjetivos']) == '1'? 'checked' : '' ?> name="dObjetivos" class="marcar" type="checkbox" value="1" />
                                        </td>
                                    </tr>
									
                                    <tr>
                                        <td>
                                                <?= ucfirst($this->lang->line('projects')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['vProjetos']) == '1'? 'checked' : '' ?> name="vProjetos" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['aProjetos']) == '1' ? 'checked' : '' ?> name="aProjetos" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['eProjetos']) =='1'? 'checked' : '' ?> name="eProjetos" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['dProjetos']) == '1'? 'checked' : '' ?> name="dProjetos" class="marcar" type="checkbox" value="1" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                                <?= ucfirst($this->lang->line('tasks')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['vTarefas']) == '1'? 'checked' : '' ?> name="vTarefas" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['aTarefas']) == '1' ? 'checked' : '' ?> name="aTarefas" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['eTarefas']) =='1'? 'checked' : '' ?> name="eTarefas" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['dTarefas']) == '1'? 'checked' : '' ?> name="dTarefas" class="marcar" type="checkbox" value="1" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                                <?= ucfirst($this->lang->line('registers')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['vRegistros']) == '1'? 'checked' : '' ?> name="vRegistros" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['aRegistros']) == '1' ? 'checked' : '' ?> name="aRegistros" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['eRegistros']) =='1'? 'checked' : '' ?> name="eRegistros" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['dRegistros']) == '1'? 'checked' : '' ?> name="dRegistros" class="marcar" type="checkbox" value="1" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                                <?= ucfirst($this->lang->line('calendar')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['vCalendario']) == '1'? 'checked' : '' ?> name="vCalendario" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['aCalendario']) == '1' ? 'checked' : '' ?> name="aCalendario" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['eCalendario']) =='1'? 'checked' : '' ?> name="eCalendario" class="marcar" type="checkbox" value="1" />
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes['dCalendario']) == '1'? 'checked' : '' ?> name="dCalendario" class="marcar" type="checkbox" value="1" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center" colspan="4">
                                            <?= mb_strtoupper($this->lang->line('functions')); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                                <input <?= isset($permissoes[ 'fConselho']) == '1' ? 'checked' : '' ?> name="fConselho" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('general_council')) ?>
                                        </td>
										<td>
                                                <input <?= isset($permissoes[ 'fGerset']) == '1' ? 'checked' : '' ?> name="fGerset" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('gerset')) ?>                                        
										</td>
                                        <td>
                                                <input <?= isset($permissoes[ 'fEmpenho']) == '1' ? 'checked' : '' ?> name="fEmpenho" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('provider')) ?>
                                        </td>
                                        <td>
												<input <?= isset($permissoes[ 'fAdministrador']) == '1' ? 'checked' : '' ?> name="fAdministrador" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('administrator')) ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center" colspan="4">
                                            <?= mb_strtoupper($this->lang->line('app_configs')); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>

                                        <td>
                                                <input <?= isset($permissoes[ 'cUsuario']) == '1' ? 'checked' : '' ?> name="cUsuario" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('app_config')); ?> <?= ucfirst($this->lang->line('users')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes[ 'cOm']) == '1' ? 'checked' : '' ?> name="cOm" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('app_config')); ?> <?= ucfirst($this->lang->line('om')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes[ 'cGerset']) == '1' ? 'checked' : '' ?> name="cGerset" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('app_config')); ?> <?= ucfirst($this->lang->line('gersets')); ?>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>
                                                <input <?= isset($permissoes[ 'cIndicadores']) == '1' ? 'checked' : '' ?> name="cIndicadores" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('app_config')); ?> <?= ucfirst($this->lang->line('indicators')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes[ 'cSetores']) == '1' ? 'checked' : '' ?> name="cSetores" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('app_config')); ?> <?= ucfirst($this->lang->line('sectors')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes[ 'cStatus']) == '1' ? 'checked' : '' ?> name="cStatus" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('app_config')); ?> <?= ucfirst($this->lang->line('perm_status')); ?>
                                        </td>
									</tr>
                                    <tr>
                                        <td>
                                                <input <?= isset($permissoes[ 'cPermissao']) == '1' ? 'checked' : '' ?> name="cPermissao" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('app_config')); ?> <?= ucfirst($this->lang->line('permissions')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes[ 'cSistema']) == '1' ? 'checked' : '' ?> name="cSistema" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('app_config')); ?> <?= ucfirst($this->lang->line('system')); ?>
                                        </td>
                                        <td>
                                                <input <?= isset($permissoes[ 'cBackup']) == '1' ? 'checked' : '' ?> name="cBackup" class="marcar" type="checkbox" value="1" />
                                                <?= ucfirst($this->lang->line('app_backup')); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
						<input type="hidden" name="data" value="<?= $data; ?>" />
                        <input type="hidden" name="IdPermissao" value="<?= $IdPermissao; ?>" />
                        <button type="submit" class="btn btn-info">
                            <?= $button ?>
                        </button>
                        <a href="<?= site_url('permissoes') ?>" class="btn btn-dark">
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
    $(document).ready(function(){

        $("#marcarTodos").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

    });
</script>