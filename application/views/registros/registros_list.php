<link href="<?= base_url('assets/css/lib/sweetalert/sweetalert.css'); ?>" rel="stylesheet">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="card">
			<div class="card-body">
				<div class="card-content table-responsive">
						<button class="btn btn-info" id="reload">
							<i class="fa fa-refresh"></i>
							<?= $this->lang->line('app_reload') ?>
						</button>

						<table id="table" class="table table-bordered" style="margin-bottom: 10px; width:100%">

							<thead>
								<tr>
                                    <th>
										<?= ucfirst($this->lang->line('description')) ?>
									</th>
                                    <th>
										<?= ucfirst($this->lang->line('indicator_target')) ?>
									</th>
                                    <th style="min-width:180px">
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
				url: "<?= site_url('registros/datatable'); ?>",
				type: "POST"
			},
			"columnDefs": [
				{
					"targets": [2],
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
				"sInfoThousands": ".",
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

 	});  
</script>