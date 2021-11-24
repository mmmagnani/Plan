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
// JavaScript Document