<?php

if ($graph != null)
{
    $monthNames = array(
        "Janeiro",
        "Fevereiro",
        "Março",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro");

    $medido = array();
    $medido2 = array();
    $goal = array();
    $mes = array();

    foreach ($graph as $g)
    {
        $medido[] = $g->meta;
        $medido2[] = $g->meta2;
        $goal[] = $g->medicao;
        $mes[] = $monthNames[$g->mes - 1];
    }

    $medido = array_reverse($medido);
    $medido2 = array_reverse($medido2);
    $goal = array_reverse($goal);
    $mes = array_reverse($mes);
    $max = max(array_merge($medido, $goal));

    // Now you can aggregate all the data into one string
    $data_string = "[" . implode(", ", $goal) . "]";
    $data2 = "[" . implode(", ", $medido) . "]";
    $data3 = "[" . implode(", ", $medido2) . "]";
    $labels_string = "['" . implode("', '", $mes) . "']";
}

?>

<script type="text/javascript">

$(function () {
	Highcharts.setOptions({
	lang: { contextButtonTitle: 'Menu',
			decimalPoint:',',
			downloadJPEG: 'Download imagem JPEG',
			downloadPDF: 'Download documento PDF',
			downloadPNG: 'Download imagem PNG',
			downloadSVG: 'Download imagem vetorial SVG',
			thousandsSep: '.',
			printChart: 'Imprimir Gráfico'
	}
	});

    $('#containergraph').highcharts({
		chart: {
			backgroundColor:null,
            backgroundColor: '#FFFFFF',
        	shadow: true
        },
        title: {
            text:'<?= $descricao; ?> - <?= $projeto; ?>'
        },
        xAxis: {
            categories: <?= $labels_string; ?>,
			labels: {
				rotation: -45
			},
			title: {
                text: '<?= $this->session->userdata('anofiscal'); ?>'
            }
        },
		yAxis: {
            title: {
                text: '<?= $unidade_meta; ?>'
            }
		},
        labels: {
        },
		credits: {
            enabled: false
        },
        series: [{
            type: 'column',
            name: 'Medido',
			maxPointWidth: 50,
			color: 'rgba(176,196,222,1)',
            data: <?= $data_string; ?>,
			dataLabels: {
            enabled: true,
            rotation: 0,
            color: '#FFFFFF',
            align: 'center',
            y: 50, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
			}
        }, {
            type: 'spline',
            name: 'Meta',
			color: 'rgba(255,0,0,1)',
            data: <?= $data2 ?>,
            marker: {
                lineWidth: 2,
                lineColor: Highcharts.getOptions().colors[3],
                fillColor: 'white'
            }
			}<?php 

			  if ($meta2 != 0)
			  {

			 ?>, {
				type: 'spline',
				name: 'Meta Mínima',
				color: 'rgba(46,139,87,1)',
				data: <?= $data3; ?>,
				marker: {
					lineWidth: 2,
					lineColor: Highcharts.getOptions().colors[1],
					fillColor: 'white'
				}
		    }<?php

			  }
      ?>],
	  	responsive: {
			rules: [{
				condition: {
					maxWidth: 500
				},
				chartOptions: {
					legend: {
						align: 'center',
						verticalAlign: 'bottom',
						layout: 'horizontal'
					},
					yAxis: {
						labels: {
							align: 'left',
							x: 0,
							y: -5
						},
					},
				}
			}]
		}
    });
});

</script>


<div class="col-lg-12">
	<div class="card">
		<div class="card-title">
			<h4>
				<i class="fa fa-eye"></i>
				<?= $this->lang->line('app_view').' '.ucfirst($this->lang->line('indicator')); ?>
			</h4>
		</div>
		<div class="card-body">

			<div class="htabs">
				<ul class="nav nav-tabs tabs-vertical" role="tablist">
					<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#info" role="tab" aria-selected="true"><span class="hidden-sm-up"><i class="ti-info"></i></span> <span class="hidden-xs-down"><?= ucfirst($this->lang->line('indicator')); ?></span> </a> </li>
					<li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#grafico" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-bar-chart"></i></span> <span class="hidden-xs-down"><?= ucfirst($this->lang->line('graph')); ?></span> </a> </li>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content col-12">
					<div class="tab-pane" id="info" role="tabpanel">
                    <p>&nbsp;</p>
						<table class="table table-bordered table-striped">
							<tr>
								<td style="width: 30%">
									<?= ucfirst($this->lang->line('description')) ?>
								</td>
								<td class="text-left">
									<?= $descricao; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('formula')) ?>
								</td>
								<td class="text-left">
									<?= $formula; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= ucfirst($this->lang->line('indicator_target')) ?>
								</td>
								<td class="text-left">
									<?= $objetivo; ?>
								</td>
							</tr>   
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('data_source')) ?>
								</td>
								<td class="text-left">
									<?= $origem_dados; ?>
								</td>          
							</tr>   
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('advantage_sefa')) ?>
								</td>
								<td class="text-left">
									<?= $vantagem_sefa; ?>
								</td>          
							</tr> 
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('advantage_om')) ?>
								</td>
								<td class="text-left">
									<?= $vantagem_om; ?>
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
									<?= ucfirst($this->lang->line('frequency')) ?>
								</td>
								<td class="text-left">
									<?= $periodicidade; ?>
								</td>          
							</tr>
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('indicator_type')) ?>
								</td>
								<td class="text-left">
									<?= $tipoindicador; ?>
								</td>          
							</tr>
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('goal')) ?>
								</td>
								<td class="text-left">
									<?= $meta; ?>
								</td>          
							</tr>
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('goal')) . '2' ?>
								</td>
								<td class="text-left">
									<?= $meta2; ?>
								</td>          
							</tr>
                            <tr>
								<td>
									<?= ucfirst($this->lang->line('goal_unit')) ?>
								</td>
								<td class="text-left">
									<?= $unidade_meta; ?>
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
									<?= ucfirst($this->lang->line('indicator_status')) ?>
								</td>
								<td class="text-left">
									<?= $situacao; ?>
								</td>          
							</tr>                                                            
						</table>
					</div>
					<div class="tab-pane active show" id="grafico" role="tabpanel">                		 
                        		<div class="card-body card-block">   
                                <p>&nbsp;</p>         			
                                  <form method="get" action="<?= $action ?>">
                                    <div class="row form-group">                                     	
                                        <div class="input-group col-sm-6">
                                          <select name="mes" id="mes" class="form-control">
                                            <option value="">Selecione o mês</option>
                                            <option value="1">Janeiro</option>
                                            <option value="2">Fevereiro</option>
                                            <option value="3">Março</option>
                                            <option value="4">Abril</option>
                                            <option value="5">Maio</option>
                                            <option value="6">Junho</option>
                                            <option value="7">Julho</option>
                                            <option value="8">Agosto</option>
                                            <option value="9">Setembro</option>
                                            <option value="10">Outubro</option>
                                            <option value="11">Novembro</option>
                                            <option value="12">Dezembro</option>
                                          </select>
                                          <div class="input-group-btn">
                                            <button class="btn btn-primary"> Filtrar </button>
                                          </div>
                                        </div>                                     
                                    </div>
                                  </form>
                                </div>
                                <?php if ($graph != null) { ?> 
                                <div class="card-toggle-body">
                                	<div class="col col-sm-12" id="containergraph"></div>
                                </div>
						<?php } else {?>                        
						<p align="center"><strong><?= ucfirst($this->lang->line('app_zero_records')) ?></strong></p>
                        <p>&nbsp;</p>
                        <?php } ?>
   					</div>
				</div>
			</div>


			<hr>

			<a href="<?= site_url('indicadores') ?>" class="btn btn-dark">
				<i class="fa fa-reply"></i>
				<?= $this->lang->line('app_back'); ?>
			</a>

		</div>
	</div>
</div>