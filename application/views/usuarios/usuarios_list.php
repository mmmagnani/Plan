<link href="<?=base_url('assets/css/lib/sweetalert/sweetalert.css');?>" rel="stylesheet">
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="card-content table-responsive">
                    <?=anchor(site_url('Usuarios/create'), '<i class="fa fa-plus"></i> ' . $this->lang->line('app_create'), 'class="btn btn-success"');?>
                    <button class="btn btn-info" id="reload">
                        <i class="fa fa-refresh"></i>
                        <?=$this->lang->line('app_reload')?>
                    </button>

                    <form id="form_delete" method="post">
                        <table id="table" class="table table-bordered" style="margin-bottom: 10px; width:100%">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?=ucfirst($this->lang->line('user_name'))?></th>
                                    <th><?=ucfirst($this->lang->line('user_status'))?></th>
                                    <th><?=ucfirst($this->lang->line('user_group'))?></th>
                                    <th><?=$this->lang->line('app_actions')?></th>
                                </tr>
                            </thead>

                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url('assets/js/lib/datatables/datatables.min.js');?>"></script>
<script src="<?=base_url('assets/js/lib/sweetalert/sweetalert.min.js');?>"></script>
<script type="text/javascript">
    $(document).ready(function () {

        var datatable = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                url: "<?= site_url('usuarios/datatable'); ?>",
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

            toastr.info('<?=$this->lang->line('app_list_updated');?>', '<?=$this->lang->line('app_attention');?>', {
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

    });

    // remove single item
    $(document).on('click', '.delete', function (event) {

        event.preventDefault();
        var url = $(this).attr('href');

        swal({
            title: "<?=$this->lang->line('app_attention');?>",
            text: "<?=$this->lang->line('app_sure_edit');?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?=$this->lang->line('app_yes');?>",
            cancelButtonText: "<?=$this->lang->line('app_cancel');?>",
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
                    swal("<?=$this->lang->line('app_attention');?>", response.message, "success");

                } else {
                    swal("<?=$this->lang->line('app_attention');?>", response.message, "error");
                }
            })
            .fail(function () {
                swal("<?=$this->lang->line('app_attention');?>", "<?=$this->lang->line('app_error');?>", "error");

            });
        });

    });
</script>