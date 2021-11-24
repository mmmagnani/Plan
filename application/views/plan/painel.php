    <?php
    if ($graph != null)
    {
        $series = array();
        $categorias = array();
        $metas = array();


        foreach ($graph as $g)
        {
          $series[] = $g->series;
          $categorias[] = $g->categorias;
          $metas[] = $g->metas;
        }

        $series = array_reverse($series);
        $categorias = array_reverse($categorias);
        $metas = array_reverse($metas);

        // Now you can aggregate all the data into one string
        $data = "[" . implode(", ", $series) . "]";
        $labels_string = "['" . implode("', '", $categorias) . "']";
        $data2 = "[" . implode(", ", $metas) . "]";
    }

    ?>
    <div class="row">

    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vObjetivos')){ ?>
    <div class="col-md-3 col-sm-6">
        <a href="<?= site_url('objetivos') ; ?>">
            <div class="card p-30">
                <div class="media">
                    <div class="media-left meida media-middle">
                        <span>
                            <i class="fa fa-bullseye f-s-40 color-primary"></i>
                        </span>
                    </div>
                    <div class="media-body media-text-right">
                    	<h2><?= $totalobjetivos ?></h2>
                        <p class="m-b-0">Objetivos</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php } ?>
    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vProjetos')){ ?>

    <div class="col-md-3 col-sm-6">
        <a href="<?= site_url('projetos') ; ?>">
            <div class="card p-30">
                <div class="media">
                    <div class="media-left meida media-middle">
                        <span>
                            <i class="fa fa-project-diagram f-s-40 color-success"></i>
                        </span>
                    </div>
                    <div class="media-body media-text-right">
                    	<h2><?= $totalprojetos ?></h2>
                        <p class="m-b-0">Projetos</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <?php } ?>
    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vTarefas')){ ?>

    <div class="col-md-3 col-sm-6">
        <a href="<?= site_url('tarefas') ; ?>">
            <div class="card p-30">
                <div class="media">
                    <div class="media-left meida media-middle">
                        <span>
                            <i class="fa fa-tasks f-s-40 color-warning"></i>
                        </span>
                    </div>
                    <div class="media-body media-text-right">
                    	<h2><?= $totaltarefas ?></h2>
                        <p class="m-b-0">Tarefas</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <?php } ?>
    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vRegistros')){ ?>

    <div class="col-md-3 col-sm-6">
        <a href="<?= site_url('indicadores') ; ?>">
            <div class="card p-30">
                <div class="media">
                    <div class="media-left meida media-middle">
                        <span>
                            <i class="fa fa-chart-bar f-s-40 color-info"></i>
                        </span>
                    </div>
                    <div class="media-body media-text-right">
                    	<h2><?= $totalindicadores ?></h2>
                        <p class="m-b-0">Indicadores</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <?php } ?>
    </div>    
    <?php if ($graph) { ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-title">
                    <h4>Cumprimento das Metas</h4>
                </div>

                <div class="card-body card-block">                			
        		  <form method="get" action="<?= site_url() ?>">
                    <div class="row form-group">
                      <div class="col col-md-4">	
                        <div class="input-group">
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
					</div>
    			  </form>
                </div>
                <hr>
                <div class="card-toggle-body">
                  <div class="col col-md-12" id="chart-metas" style="max-height: 600px;"></div>
                </div>
            </div>
            <!-- /# card -->
        </div>
    </div>
	<?php } ?>
    
    <?php

	if ($estatisticas_orcamento->total_autorizado) {

	?>
	<div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-title">
                    <h4>Estimado X Autorizado</h4>
                </div>

                <div class="card-body card-block">                			
        		  <form method="get" action="<?= site_url() ?>">
                      <div class="col col-md-4">	
            		      <select style="visibility:hidden" name="mes2" id="mes2" class="form-control">
                		    
            		      </select>
				      </div>
    			  </form>
                </div>
                <div class="card-toggle-body">
                  <div id="chart-orcamento"></div>
                </div>
            </div>
            <!-- /# card -->
        </div>


        <div class="col-lg-6">
            <div class="card">
                <div class="card-title">
                    <h4>Autorizado X Reserva</h4>
                </div>

                <div class="card-body card-block">                			
        		  <form method="get" action="<?= site_url() ?>">
                      <div class="col col-md-4">	
            		      <select style="visibility:hidden" name="mes2" id="mes2" class="form-control">
                		    
            		      </select>
				      </div>
    			  </form>
                </div>
                <div class="card-toggle-body">
                  <div id="chart-orcamento2"></div>
                </div>
            </div>
            <!-- /# card -->
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-title">
                    <h4>Autorizado X Executado</h4>
                </div>

                <div class="card-body card-block">                			
        		  <form method="get" action="<?= site_url() ?>">
                    <div class="row form-group">
                      <div class="col col-md-4">	
                        <div class="input-group">
            		      <select name="mes2" id="mes2" class="form-control">
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
					</div>
    			  </form>
                </div>

                <div class="card-toggle-body">
                  <div id="chart-orcamento3" style=""></div>
                </div>
            </div>
            <!-- /# card -->
        </div>
    </div>
    <?php } ?>
    <?php

	if ($graph)
	{

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
			printChart: 'Imprimir Gráfico',
			thousandsSep: '.'
	      }
	    });

        $('#chart-metas').highcharts({

            chart: {
                polar: true,
                type: 'line',
			    backgroundColor:null,
                backgroundColor: '#FFFFFF',
        	    shadow: true
            },
		    <?php

            if (!isset($_GET['mes']) || !$_GET['mes'])
            {

            ?>
            title: {
                text: '<?= $this->session->userdata('anofiscal') ?>'
            },
		<?php

        } else
        {
            switch ($_GET['mes'])
            {
              case 1:
                  $mesano = 'Janeiro/' . $this->session->userdata('anofiscal');
                break;
              case 2:
                  $mesano = 'Fevereiro/' . $this->session->userdata('anofiscal');
                break;
              case 3:
                  $mesano = 'Março/' . $this->session->userdata('anofiscal');
                break;
              case 4:
                  $mesano = 'Abril/' . $this->session->userdata('anofiscal');
                break;
              case 5:
                  $mesano = 'Maio/' . $this->session->userdata('anofiscal');
                break;
              case 6:
                  $mesano = 'Junho/' . $this->session->userdata('anofiscal');
                break;
              case 7:
                  $mesano = 'Julho/' . $this->session->userdata('anofiscal');
                break;
              case 8:
                  $mesano = 'Agosto/' . $this->session->userdata('anofiscal');
                break;
              case 9:
                  $mesano = 'Setembro/' . $this->session->userdata('anofiscal');
                break;
              case 10:
                  $mesano = 'Outubro/' . $this->session->userdata('anofiscal');
                break;
              case 11:
                  $mesano = 'Novembro/' . $this->session->userdata('anofiscal');
                break;
              case 12:
                  $mesano = 'Dezembro/' . $this->session->userdata('anofiscal');
                break;
            }

            ?>
		title: {
            text: '<?= $mesano ?>'
        },
		<?php

		}

		?>

        pane: {
            size: '85%'
        },

        xAxis: {
            categories: <?= $labels_string ?>,
            tickmarkPlacement: 'on',
            lineWidth: 0
        },

        yAxis: {
            gridLineInterpolation: 'polygon',
            lineWidth: 0,
            min: 0
        },

        tooltip: {
            shared: true,
            pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y}</b><br/>'
        },

        legend: {
            align: 'center',
        	layout: 'horizontal',
        },
		
        credits: {
            enabled: false
        },		

        series: [{
            name: 'Média das Medições',
            data: <?= $data ?>,
            pointPlacement: 'on'
        }, {
            name: 'Média das Metas',
            data: <?= $data2 ?>,
            pointPlacement: 'on'
        }]

		});
	});
	</script>
	<?php

	}

	?>
	<?php

	if (isset($estatisticas_orcamento) && $estatisticas_orcamento != null)
	{
    if ($estatisticas_orcamento->total_previsto != null || $estatisticas_orcamento->
        total_geral_autorizado != null || $estatisticas_orcamento->total_reserva != null ||
        $estatisticas_orcamento->total_sem_autorizacao != null || $estatisticas_orcamento->
        total_executado != null || $estatisticas_orcamento->total_autorizado != null)
    {

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
			printChart: 'Imprimir Gráfico',
			thousandsSep: '.'
		}
		});

		$('#chart-orcamento').highcharts({

        chart: {
			type: 'pie',
			backgroundColor:null,
            backgroundColor: '#FFFFFF',
        	shadow: true
        },
		
		colors: ['#f28f43', '#492970', '#8bbc21', '#910000', '#1aadce',
    			 '#2f7ed8', '#0d233a', '#77a1e5', '#c42525', '#a6c96a'],
		
		title: {
            text: ''
        },
			

        tooltip: {
            pointFormat: '<span style="color:{series.color}"><b>R$ {point.y:,.2f}</b><br/>'
        },

        plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',			
				dataLabels: {
					enabled: true,
					format: '<b>{point.percentage:.2f} %</b>: ',
					distance: 30,				
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
				},
				showInLegend: true
			}
		},
		
        credits: {
            enabled: false
        },		

        series: [{
			colorByPoint: true,
            data:[{
					name: 'Estimado',
					y: <?= $estatisticas_orcamento->total_previsto ?>,
					sliced: true,
					selected: true
					}, {
					name: 'Autorizado',
					y: <?= $estatisticas_orcamento->total_geral_autorizado ?>
				}]
        }]
		});
	});

	$(function () {
		Highcharts.setOptions({
		lang: { contextButtonTitle: 'Menu',
			decimalPoint:',',
			downloadJPEG: 'Download imagem JPEG',
			downloadPDF: 'Download documento PDF',
			downloadPNG: 'Download imagem PNG',
			downloadSVG: 'Download imagem vetorial SVG',
			printChart: 'Imprimir Gráfico',
			thousandsSep: '.'
		}
		});

		$('#chart-orcamento2').highcharts({

		chart: {
			type: 'pie',
			backgroundColor:null,
            backgroundColor: '#FFFFFF',
        	shadow: true
        },
		
		colors: ['#77a1e5', '#0d233a', '#8bbc21', '#910000', '#1aadce',
    			 '#2f7ed8', '#492970', '#f28f43', '#c42525', '#a6c96a'],
		
		title: {
            text: ''
        },

        tooltip: {
            pointFormat: '<span style="color:{series.color}"><b>R$ {point.y:,.2f}</b><br/>'
        },

        plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.percentage:.2f} %</b>: ',
					distance: 30,
					filter: {
                    	property: 'percentage',
                    	operator: '>',
                    	value: 4
                	},
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
				},
				showInLegend: true
			}
		},
		
        credits: {
            enabled: false
        },		

        series: [{
			colorByPoint: true,
            data:[{
					name: 'Autorizado',
					y: <?= $estatisticas_orcamento->total_geral_autorizado ?>,
					sliced: true,
					selected: true
					}, {
					name: 'Reserva',
					<?php if(empty($estatisticas_orcamento->total_reserva)) { ?>
						y: 0
					<?php } else { ?>
					y: <?= $estatisticas_orcamento->total_reserva ?>
					<?php } ?>
				}]
        }]
		});
	});

	$(function () {
		Highcharts.setOptions({
		lang: { contextButtonTitle: 'Menu',
			decimalPoint:',',
			downloadJPEG: 'Download imagem JPEG',
			downloadPDF: 'Download documento PDF',
			downloadPNG: 'Download imagem PNG',
			downloadSVG: 'Download imagem vetorial SVG',
			printChart: 'Imprimir Gráfico',
			thousandsSep: '.'
		}
		});

		$('#chart-orcamento3').highcharts({

        chart: {
			type: 'pie',			
			backgroundColor:null,
            backgroundColor: '#FFFFFF',
        	shadow: true
        },
		
		colors: ['#c42525', '#a6c96a', '#8bbc21', '#910000', '#1aadce',
    			 '#2f7ed8', '#0d233a', '#77a1e5', '#f28f43', '#492970'],
		
		<?php

        if (!isset($_GET['mes2']) || !$_GET['mes2'])
        {

		?>
        title: {
            text: '<?= $this->session->userdata('anofiscal') ?>'
        },
		<?php

        } else
        {
            switch ($_GET['mes2'])
            {
                case 1:
                    $mesano = 'Janeiro/' . $this->session->userdata('anofiscal');
                    break;
                case 2:
                    $mesano = 'Fevereiro/' . $this->session->userdata('anofiscal');
                    break;
                case 3:
                    $mesano = 'Março/' . $this->session->userdata('anofiscal');
                    break;
                case 4:
                    $mesano = 'Abril/' . $this->session->userdata('anofiscal');
                    break;
                case 5:
                    $mesano = 'Maio/' . $this->session->userdata('anofiscal');
                    break;
                case 6:
                    $mesano = 'Junho/' . $this->session->userdata('anofiscal');
                    break;
                case 7:
                    $mesano = 'Julho/' . $this->session->userdata('anofiscal');
                    break;
                case 8:
                    $mesano = 'Agosto/' . $this->session->userdata('anofiscal');
                    break;
                case 9:
                    $mesano = 'Setembro/' . $this->session->userdata('anofiscal');
                    break;
                case 10:
                    $mesano = 'Outubro/' . $this->session->userdata('anofiscal');
                    break;
                case 11:
                    $mesano = 'Novembro/' . $this->session->userdata('anofiscal');
                    break;
                case 12:
                    $mesano = 'Dezembro/' . $this->session->userdata('anofiscal');
                    break;
            }

	?>
		title: {
            text: '<?= $mesano ?>'
        },
		<?php

        }

		?>

        tooltip: {
            pointFormat: '<span style="color:{series.color}"><b>R$ {point.y:,.2f}</b><br/>'
        },

        plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.percentage:.2f} %</b>: ',
					distance: 30,
					filter: {
                    	property: 'percentage',
                    	operator: '>',
                    	value: 4
                	},
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
				},
				showInLegend: true
			}
		},
		
        credits: {
            enabled: false
        },		

        series: [{
			colorByPoint: true,
            data:[{
					name: 'Autorizado',
					y: <?= $estatisticas_orcamento->total_geral_autorizado ?>,
					sliced: true,
					selected: true
					}, {
					name: 'Executado',
		<?php

        if ($estatisticas_orcamento->executado_mes == 0)
        {

		?>
					y: 0
		<?php

        } else
        {

		?>
					y: <?= $estatisticas_orcamento->executado_mes ?>
		<?php

        }

		?>
				}]
        }]
		});
	});
    
	</script>

	<?php

	}
	}

	?>