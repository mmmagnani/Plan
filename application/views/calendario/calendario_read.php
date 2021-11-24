<?php

if ($graph != null)
{ 
		$titulo1 = $graph->sigla . ' - Apoiada';
		if(empty($graph->prazo_ent_gap)){
		  $prazo_in_gap = NULL;
		} else {
		  $prazo_in_gap = date('d/m/Y', strtotime($graph->prazo_ent_gap));
		}
		if(empty($graph->atraso))
		{
		  if(!empty($graph->prazo_ent_gap)){
            $date1 = date_create("now");
		    $date2 = date_create($graph->prazo_ent_gap);
		    $intervalo = date_diff($date1,$date2);
		    $dif = $intervalo->format('%R%a');
		    $dado = 80 - $dif;
		    if($dado < 0){
			  $dado = 0;
		    }
			if($dado > 100)
			{
				$dado = 100;
			}
		  } else {
		    $dado = 0;
		  }
		} else {
			$dado = 80 + $graph->atraso;
			if($dado < 0){
			  $dado = 0;
		    }
			if($dado > 100)
			{
				$dado = 100;
			}
		}
		
		if(empty($graph->omapoiadora)){
		  $titulo2 = $apoiadora->sigla . ' - Apoiadora';
		} else {
		  $titulo2 = $graph->omapoiadora . ' - Apoiadora';
		}
		if(empty($graph->atraso))
		{
		  if(empty($graph->homol_pretendida)){
		    $homol_prevista = NULL;
		  } else {
		    $homol_prevista = date('d/m/Y', strtotime($graph->homol_pretendida));
		  }
		} else {
		  if(empty($graph->homol_estimada)){
		    $homol_prevista = NULL;
		  } else {
		    $homol_prevista = date('d/m/Y', strtotime($graph->homol_estimada));
		  }
		}
		if(empty($graph->data_ent_gap))
		{
		  $data_in_gap = 0;
		}
		else
		{
		  $data_in_gap = date('d/m/Y', strtotime($graph->data_ent_gap));
		}
		switch($graph->tipo)
		{
			case 1: 
			$dt1 = 0;
			$dt2 = 120;
			$dt3 = 140;
			$dt4 = $dt2-20;
			break;
			case 2:
			$dt1 = 0;
			$dt2 = 95;
			$dt3 = 115;
			$dt4 = $dt2-20;
			break;
			case 3:
			$dt1 = 0;
			$dt2 = 180;
			$dt3 = 200;
			$dt4 = $dt2-20;
			break;
			default:
			$dt1 = 0;
			$dt2 = 95;
			$dt3 = 115;
			$dt4 = $dt2-20;
			break;
			
		}
		$homol_pretendida = $graph->homol_pretendida ? date('d/m/Y', strtotime($graph->homol_pretendida)) : '';
		if((empty($graph->homol_efetiva))&&($data_in_gap == 0))
		{
			$dado2 = 0;
			$dreal = $dado2;
		} 
		else if((empty($graph->homol_efetiva))&&($data_in_gap != 0))
		{
          $date3 = date_create("now");
		  $date4 = date_create($graph->data_ent_gap);
		  $intervalo2 = date_diff($date4,$date3);
		  $dif2 = $intervalo2->format('%R%a');
		  $dado2 = $dt1 + $dif2;
		  $dreal = $dado2;
		  if($dado2 < 0){
			  $dado2 = 0;
		  }
		  if($dado2 > $dt3) {
			  $dado2 = $dt3;
		  }
		} else {
		  $homol_efetiva = $graph->homol_efetiva ? date('Y-m-d', strtotime($graph->homol_efetiva)) : '';
		if(empty($graph->atraso))
		{
		  if(empty($graph->homol_pretendida)){
		    $homo_calc = NULL;
		  } else {
		    $homol_calc = $graph->homol_pretendida;
		  }
		} else 
		{
		  if(empty($graph->homol_estimada)){
		    $homol_calc = NULL;
		  } else {
		    $homol_calc = $graph->homol_estimada;
		  }
		}
		  if(!empty($homol_calc)){    
		    $date5 = date_create($homol_calc);
		    $date6 = date_create($homol_efetiva);
		    $intervalo3 = date_diff($date5,$date6);
		    $dif3 = $intervalo3->format('%R%a');
		    $dado2 = $dt2 + $dif3;
			$dreal = $dado2;
			  if($dado2 > $dt3)
			  {
				  $dado2 = $dt3;
			  }	
		  } else {
		    $dado2 = 0;
			$dreal = $dado2;
		  }
		}		
}

?>
<script type="text/javascript">

$(function () {
	Highcharts.setOptions({
	lang: { contextButtonTitle: 'Menu',
			downloadJPEG: 'Download imagem JPEG',
			downloadPDF: 'Download documento PDF',
			downloadPNG: 'Download imagem PNG',
			downloadSVG: 'Download imagem vetorial SVG',
			printChart: 'Imprimir Gráfico'
	}
	});
	
    $('#containergraph').highcharts({
	
	    chart: {
	        type: 'gauge',
	        plotBackgroundColor: null,
	        plotBackgroundImage: null,
	        plotBorderWidth: 0,
	        plotShadow: false
	    },
	    
	    title: {
	        text: '<?= $titulo1; ?>'
	    },
	    
	    pane: {
	        startAngle: -120,
	        endAngle: 120,
            background: null
	    },
        
        plotOptions: {
            gauge: {
                dataLabels: {
                    enabled: false
             },
                dial: {
                    baseLength: '0%',
                    baseWidth: 10,
                    radius: '100%',
                    rearLength: '0%',
                    topWidth: 1
                }
            }
        },
	       
	    // the value axis
	    yAxis: {
            labels: {
				distance: 35,
				formatter: function () {
					if(this.value == 80)
					{
					return '<?php echo $prazo_in_gap; ?>'
					}
					else if(this.value == 100)
					{
						return this.value + ' dias <br> ou mais';
					}
					else
					{
						return this.value + ' dias';
					}
            	}
            },
			tickPositions: [0,80,100],
            minorTickLength: 0,
	        min: 0,
	        max: 100,
	        plotBands: [{
	            from: 0,
	            to: 60,
	            color: 'rgb(0, 128, 0)', // green
                thickness: '50%'
	        }, {
	            from: 60,
	            to: 80,
	            color: 'rgb(255, 255, 0)', // yellow
                thickness: '50%'
	        }, {
	            from: 80,
	            to: 100,
	            color: 'rgb(255, 0, 0)', // red
                thickness: '50%'
	        }]        
	    },
		credits: {
            enabled: false
        },
	
	    series: [{
	        name: 'Tempo (dias)',
	        data: [<?= $dado; ?>]
	    }]
	
	});
});

</script>
<script type="text/javascript">

$(function () {
	Highcharts.setOptions({
	lang: { contextButtonTitle: 'Menu',
			downloadJPEG: 'Download imagem JPEG',
			downloadPDF: 'Download documento PDF',
			downloadPNG: 'Download imagem PNG',
			downloadSVG: 'Download imagem vetorial SVG',
			printChart: 'Imprimir Gráfico'
	}
	});
	
    $('#containergraph2').highcharts({
	
	    chart: {
	        type: 'gauge',
	        plotBackgroundColor: null,
	        plotBackgroundImage: null,
	        plotBorderWidth: 0,
	        plotShadow: false
	    },
	    
	    title: {
	        text: '<?= $titulo2; ?>'
	    },
	    
	    pane: {
	        startAngle: -120,
	        endAngle: 120,
            background: null
	    },
        
        plotOptions: {
            gauge: {
                dataLabels: {
                    enabled: false
             },
                dial: {
                    baseLength: '0%',
                    baseWidth: 10,
                    radius: '100%',
                    rearLength: '0%',
                    topWidth: 1
                }
            }
        },
	       
	    // the value axis
	    yAxis: {
            labels: {
				distance: 35,
				formatter: function () {
					if(this.value == <?= $dt2; ?>)
					{
					return '<?= $homol_prevista; ?>'
					}
					else if(this.value == <?= $dt3; ?>)
					{
						return this.value + ' dias <br> ou mais';
					}
					else if(this.value == <?= $data_in_gap; ?>)
					{
						return this.value + ' dias';
					}
					else
					{
						return '<?= $data_in_gap; ?>'
					}
            	}
            },
			tickPositions: [<?= $dt1; ?>,<?= $dt2; ?>,<?= $dt3; ?>],
            minorTickLength: 0,
	        min: <?= $dt1; ?>,
	        max: <?= $dt3; ?>,
	        plotBands: [{
	            from: <?= $dt1; ?>,
	            to: <?= $dt4; ?>,
	            color: 'rgb(0, 128, 0)', // green
                thickness: '50%'
	        }, {
	            from: <?= $dt4; ?>,
	            to: <?= $dt2; ?>,
	            color: 'rgb(255, 255, 0)', // yellow
                thickness: '50%'
	        }, {
	            from: <?= $dt2; ?>,
	            to: <?= $dt3; ?>,
	            color: 'rgb(255, 0, 0)', // red
                thickness: '50%'
	        }]        
	    },
		credits: {
            enabled: false
        },
	
	    series: [{
	        name: 'Tempo (dias)',
	        data: [<?= $dado2; ?>]
	    }]
	
	});
});

</script>
<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card">
		<div class="card-title">
			<h4>
				<i class="fa fa-eye"></i>
				<?= ucfirst($this->lang->line('view_calendar_item')); ?>
			</h4>
		</div>
		<div class="card-body">

			<div class="htabs">
				<ul class="nav nav-tabs tabs-vertical" role="tablist">
					<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#info" role="tab" aria-selected="true"><span class="hidden-sm-up"><i class="ti-calendar"></i></span> <span class="hidden-xs-down"><?= ucfirst($this->lang->line('calendar_data')); ?></span> </a> </li>
					<li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#grafico" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-bar-chart"></i></span> <span class="hidden-xs-down"><?= ucfirst($this->lang->line('graph')); ?></span></a> </li>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content col-12">
					<div class="tab-pane" id="info" role="tabpanel">
						<table class="table table-bordered table-striped">
							<tr>
								<td style="width: 30%">
									<?= $this->lang->line('om') ?>
								</td>
								<td class="text-left">
									<?= $om; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('object')) ?>
								</td>
								<td class="text-left">
									<?= $objeto; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('estimated_val')) ?>
								</td>
								<td class="text-left">
									<?= $valor_estimado; ?>
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
									<?= ucfirst($this->lang->line('deadline_gerset')) ?>
								</td>
								<td class="text-left">
									<?= $prazo_env_gerset; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('deadline')) ?>
								</td>
								<td class="text-left">
									<?= $prazo_ent_gap; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('date_gap_in')) ?>
								</td>
								<td class="text-left">
									<?= $data_ent_gap; ?>
								</td>
							</tr>
							<tr>
								<td>
                                <?php if((empty($atraso)) || ($atraso>0)){ ?>
									<?= ucfirst($this->lang->line('delay')) ?>
                                <?php } else { ?>
									<?= ucfirst($this->lang->line('antecipation')) ?>
                                <?php } ?>
								</td>
								<td class="text-left">
									<?= abs($atraso) . ' dias'; ?>
								</td>
							</tr> 
							<tr>
								<td>
									<?= ucfirst($this->lang->line('intended_approval_date')) ?>
								</td>
								<td class="text-left">
									<?= $homol_pretendida; ?>
								</td>
							</tr>   
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('estimated_approval_date')) ?>
								</td>
								<td class="text-left">
									<?= $homol_estimada; ?>
								</td>
							</tr>
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('calendar_status')) ?>
								</td>
								<td class="text-left">
									<?= $status; ?>
								</td>
							</tr>  
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('observation')) ?>
								</td>
								<td class="text-left">
									<?= $observacao; ?>
								</td>
							</tr>                                                 
						</table>
					</div>
					<div class="tab-pane active show" id="grafico" role="tabpanel">                		 
                        	<div class="card-body card-block">   
                                <p>&nbsp;</p>         			
                            </div>
                         <?php if ($graph != null) { ?> 
                            <div class="card-toggle-body">
                               <table align="center" cellpadding="10">
                                 <tr>
                                   <td>
                                     <div id="containergraph" style="min-width: 420px; height: 400px; margin: 0 auto">
                                     </div>
                                   </td>
                                   <td>
                                     <div id="containergraph2" style="min-width: 420px; height: 400px; margin: 0 auto">
                                     </div>                   
                                   </td>
                                 </tr>
                               </table>                                
                            </div>
						 <?php } else {?>                        
						    <p align="center"><strong><?= ucfirst($this->lang->line('app_zero_records')) ?></strong></p>
                            <p>&nbsp;</p>
                         <?php } ?>
   					</div>
				</div>
			</div>


			<hr>
			<a href="<?= site_url('calendario') ?>" class="btn btn-dark">
				<i class="fa fa-reply"></i>
				<?= $this->lang->line('app_back'); ?>
			</a>
		</div>
	</div>
</div>


                             

