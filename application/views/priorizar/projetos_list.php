<link href="<?= base_url('assets/css/lib/sweetalert/sweetalert.css'); ?>" rel="stylesheet">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="card">
			<div class="card-body">
				<div class="card-content table-responsive">
                <?php if (($this->permission->checkPermission($this->session->userdata('permissao'), 'aObjetivos'))&&($results)){
  						if($habilita){
							echo anchor(site_url('priorizar/gerarCalendario'),'<i class="fa fa-plus"></i> '.$this->lang->line('generate_calendar'), 'class="btn btn-success"');
  						}
				}
				?>
							<table id="table" class="table table-bordered table-striped" style="margin-bottom: 10px; width:100%">

								<thead>
									<tr>
										<th>
											<?= ucfirst($this->lang->line('title')) ?>
										</th>
                                        <th>
											<?= ucfirst($this->lang->line('estimated_cost')) ?>
										</th>
										<th>
											<?= ucfirst($this->lang->line('autorized')) ?>
										</th>
                                        <th>
											<?= ucfirst($this->lang->line('gerset')) ?>
										</th>
										<th style="min-width:150px">
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
				url: "<?= site_url('priorizar/datatable'); ?>",
				type: "POST"
			},
			"columnDefs": [
				{
					"targets": [4],
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
	});  
</script>