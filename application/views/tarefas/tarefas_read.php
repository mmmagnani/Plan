<link href="<?= base_url('assets/css/lib/sweetalert/sweetalert.css'); ?>" rel="stylesheet">
<div id="tarefas" class="col-lg-12 col-md-12 col-sm-12">
	<div class="card">
		<div class="card-title">
			<h4>
				<i class="fa fa-eye"></i>
				<?= $this->lang->line('app_view').' '.ucfirst($this->lang->line('tasks')); ?>
			</h4>
		</div>
		<div class="card-body">

			<div class="vtabs">
				<ul class="nav nav-tabs tabs-vertical" role="tablist">
					<li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#info" role="tab" aria-selected="true"><span class="hidden-sm-up"><i class="ti-check-box"></i></span> <span class="hidden-xs-down"><?= ucfirst($this->lang->line('task')); ?></span> </a> </li>
					<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#empenhos" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-receipt"></i></span> <span class="hidden-xs-down"><?= ucfirst($this->lang->line('commitments')); ?></span> </a> </li>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content col-12">
					<div class="tab-pane active show" id="info" role="tabpanel">
						<table class="table table-bordered table-striped">
							<tr>
								<td style="min-width:180px">
									<?= ucfirst($this->lang->line('numspo')) ?>
								</td>
								<td class="text-left">
									<?= $spo_id; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('sector')) ?>
								</td>
								<td class="text-left">
									<?= $setor; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('project')) ?>
								</td>
								<td class="text-left">
									<?= $projeto; ?>
								</td>
							</tr>   
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('title')) ?>
								</td>
								<td class="text-left">
									<?= $titulo; ?>
								</td>
							</tr> 
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('catmat')) ?>
								</td>
								<td class="text-left">
									<?= $CATMAT; ?>
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
									<?= ucfirst($this->lang->line('justification')) ?>
								</td>
								<td class="text-left">
									<?= $justificativa; ?>
								</td>
							</tr>
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('estimated_val')) ?>
								</td>
								<td class="text-left">
									<?= $valor_previsto; ?>
								</td>
							</tr>  
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('autorized_val')) ?>
								</td>
								<td class="text-left">
									<?= $valor_autorizado; ?>
								</td>
							</tr> 
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('executed_val')) ?>
								</td>
								<td class="text-left">
									<?= $valor_executado; ?>
								</td>
							</tr>                                         
						</table>
					</div>
					<div class="tab-pane" id="empenhos" role="tabpanel">
						<?php if ($this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) {	 ?>
                    	<?= anchor(site_url('empenhos/create/' . $IdTarefa),'<i class="fa fa-plus"></i> '.$this->lang->line('app_create'), 'class="btn btn-success"'); ?>
						<?php } ?>
						<button class="btn btn-info" id="reload">
							<i class="fa fa-refresh"></i>
							<?= $this->lang->line('app_reload') ?>
						</button>
						<form id="form_delete" method="post">
							<table style="width:100%" id="table" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>
											<?php if ($this->permission->check($this->session->userdata('permissao'), 'fEmpenho')) { ?>
											<input type="checkbox" id="remove-all">
											<button class="btn btn-danger btn-sm hide" id="delete" title="<?= $this->lang->line('app_delete') ?>">
												<i class="fa fa-trash"></i>
											</button>
											<?php } ?>
										</th>
										<th><?= ucfirst($this->lang->line('commitment')) ?></th>
										<th>
											<?= ucfirst($this->lang->line('date_commitment')) ?>
										</th>
										<th>
											<?= ucfirst($this->lang->line('commitment_val')) ?>
										</th>
										<th style="min-width:150px">
											<?= $this->lang->line('app_actions') ?>
										</th>
									</tr>
								</thead>
                                <tfoot>
            						<tr>
                						<th colspan="3" style="text-align:right">Total Executado (R$):</th>
                						<th><p style="color:green"><?= $valor_executado; ?></p></th>
                                        <th></th>
            						</tr>
        						</tfoot>
							</table>
						</form>	
					</div>
				</div>
			</div>


			<hr>
			<?php if ($this->permission->check($this->session->userdata('permissao'), 'aTarefas')) { ?>
			<a href="<?= site_url('tarefas/create') ?>" class="btn btn-success">
				<i class="fa fa-plus"></i>
				<?= $this->lang->line('app_create'); ?>
			</a>
			<?php } ?>
			<?php if ($this->permission->check($this->session->userdata('permissao'), 'eTarefas')) { ?>
			<a href="<?= site_url('tarefas/update/'.$IdTarefa) ?>" class="btn btn-info">
				<i class="fa fa-edit"></i>
				<?= $this->lang->line('app_edit'); ?>
			</a>
			<?php } ?>
			<a href="<?= site_url('tarefas') ?>" class="btn btn-dark">
				<i class="fa fa-reply"></i>
				<?= $this->lang->line('app_back'); ?>
			</a>

		</div>
	</div>
</div>

<script src="<?= base_url('assets/js/lib/datatables/datatables.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/lib/sweetalert/sweetalert.min.js'); ?>"></script> 
 
<script type="text/javascript">  
	$(document).ready(function () {

		var datatable = $('#table').DataTable({
			"processing": true,
			"serverSide": true,
			"order": [],
			"ajax": {
				url: "<?= site_url('empenhos/datatable/'.$IdTarefa); ?>",
				type: "POST"
			},
			"columnDefs": [
				{
					"targets": [0, 4],
					"orderable": false,
				},
			],
			"language": {
				"search": "<?= $this->lang->line('app_search'); ?>",
				"lengthMenu": "<?= $this->lang->line('app_per_page'); ?>",
				"zeroRecords": "<?= $this->lang->line('app_zero_records'); ?>",
				"info": "<?= $this->lang->line('app_showing'); ?>",
				"infoEmpty": "<?= $this->lang->line('app_empty'); ?>",
				"infoFiltered": "<?= $this->lang->line('app_filtered'); ?>",
				"oPaginate": {
					"sNext": "<?= $this->lang->line('app_next'); ?>",
					"sPrevious": "<?= $this->lang->line('app_previous'); ?>",
					"sFirst": "<?= $this->lang->line('app_first'); ?>",
					"sLast": "<?= $this->lang->line('app_last'); ?>"
				},
				"sLoadingRecords": "<?= $this->lang->line('app_loading'); ?>",
				"sProcessing": "<?= $this->lang->line('app_processing'); ?>",
			},			
		});
		
		
		// check if delete button must appear
		function check_delete_button() {
			$('table').find('.remove').each(function (index, val) {

				if ($(val)[0].checked) {
					$('#delete').removeClass('hide');
					return false;
				}
				$('#delete').addClass('hide');
			});
		}

		// mark all checkboxes
		$(document).on('click', '#remove-all', function () {

			var checkbox = $(this);
			if (checkbox[0].checked) {

				$('table').find('.remove').each(function (index, val) {
					$(val).prop('checked', true);
					$(val).closest('tr').addClass('table-danger');
					$('#delete').removeClass('hide');
				});

			} else {

				$('table').find('.remove').each(function (index, val) {
					$(val).prop('checked', false);
					$(val).closest('tr').removeClass('table-danger');
					$('#delete').addClass('hide');

				});
			}
		});

		// reload datatable
		$(document).on('click', '#reload', function () {

			$('#delete').addClass('hide');
			datatable.ajax.reload();
			toastr.info('<?= $this->lang->line('app_list_updated'); ?>', '<?= $this->lang->line('app_attention'); ?>', {
				timeOut: 8000,
				"closeButton": true,
				"newestOnTop": true,
				"progressBar": true,
				"positionClass": "toast-top-right",
				"onclick": null,
			});

		});

		// check item and highlight row
		$(document).on('click', '.remove', function () {

			var checkbox = $(this);
			if (checkbox[0].checked) {
				checkbox.closest('tr').addClass('table-danger');
			} else {
				checkbox.closest('tr').removeClass('table-danger');
			}

			check_delete_button();

		});

		// delete many items form
		$('#form_delete').submit(function (event) {
			event.preventDefault();
			data = $(this).serialize();
			
			swal({
				title: "<?= $this->lang->line('app_attention'); ?>",
				text: "<?= $this->lang->line('app_sure_delete'); ?>",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "<?= $this->lang->line('app_delete'); ?>",
				cancelButtonText: "<?= $this->lang->line('app_cancel'); ?>",
				showLoaderOnConfirm: true,
				closeOnConfirm: false
			},
			function () {

                $.ajax({
					url: '<?= site_url('empenhos/delete_many/' . $IdTarefa) ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
				})
				.done(function (response) {
					if (response.result == true) {
						location.reload();
					} else {
						swal("<?= $this->lang->line('app_attention'); ?>", response.message, "error");
					}
				})
				.fail(function () {
					swal("<?= $this->lang->line('app_attention'); ?>", "<?= $this->lang->line('app_error'); ?>", "error");

				});

            });
	
		});

		// remove single item
		$(document).on('click', '.delete', function (event) {

			event.preventDefault();
			var url = $(this).attr('href');

			swal({
				title: "<?= $this->lang->line('app_attention'); ?>",
				text: "<?= $this->lang->line('app_sure_delete'); ?>",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "<?= $this->lang->line('app_delete'); ?>",
				cancelButtonText: "<?= $this->lang->line('app_cancel'); ?>",
				showLoaderOnConfirm: true,
				closeOnConfirm: false
			},
			function () {

				$.ajax({
					url: url + '?ajax=true',
					type: 'GET',
					dataType: 'json',
				})
				.done(function (response) {
					if (response.result == true) {
											
						location.reload();
						
					} else {
						swal("<?= $this->lang->line('app_attention'); ?>", response.message, "error");
					}
				})
				.fail(function () {
					swal("<?= $this->lang->line('app_attention'); ?>", "<?= $this->lang->line('app_error'); ?>", "error");

				});
			});

		});

	});  
</script>

                             

