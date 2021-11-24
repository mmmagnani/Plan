<link href="<?= base_url('assets/css/lib/sweetalert/sweetalert.css'); ?>" rel="stylesheet">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="card">
			<div class="card-body">
				<div class="card-content table-responsive">
                <?php
                	if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCalendario')) {
						echo anchor(site_url('calendario/create'),'<i class="fa fa-plus"></i> '.$this->lang->line('app_create'), 'class="btn btn-success"');
						echo ' ';
						} ?>
						<button class="btn btn-info" id="reload">
							<i class="fa fa-refresh"></i>
							<?= $this->lang->line('app_reload') ?>
						</button>
					<?= anchor(site_url('calendario/exportar'),'<i class="fa fa-file-download"></i> ' . $this->lang->line('export_to_plan'), 'class="btn btn-danger"') ?>

							<table id="table" class="table table-bordered table-striped" style="margin-bottom: 10px; width:100%">

								<thead>
									<tr>
										<th>
											<?= $this->lang->line('om') ?>
										</th>
										<th>
                                        	<?= ucfirst($this->lang->line('object')) ?>
                                        </th>
                                        <th>
                                            <?= ucfirst($this->lang->line('intended_approval_date')) ?>
                                        </th>
										<th>
                                            <?= ucfirst($this->lang->line('deadline')) ?>
                                        </th>
                                        <th>
                                            <?= ucfirst($this->lang->line('calendar_status')) ?>
                                        </th>
                                        <?php if ($this->permission->check($this->session->userdata('permissao'), 'dCalendario')) { ?>
                                        <th>
                                            <?= ucfirst($this->lang->line('situation')) ?>
                                        </th>
                                        <?php } ?>
										<th style="min-width:139px">
											<?= $this->lang->line('app_actions') ?>
										</th>
									</tr>
								</thead>

							</table>
				</div>
			</div>
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
				url: "<?= site_url('calendario/datatable'); ?>",
				type: "POST"
			},
			"columnDefs": [
				{
					<?php if ($this->permission->check($this->session->userdata('permissao'), 'dCalendario')) { ?>
					"targets": [5],
					<?php } else { ?>
					"targets": [4],
					<?php } ?>
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
			}
		});
		
		// reload datatable
		$(document).on('click', '#reload', function () {

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
				confirmButtonText: "<?= $this->lang->line('app_yes'); ?>",
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
						$('#reload').trigger('click');
						swal("<?= $this->lang->line('app_attention'); ?>", response.message, "success");

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