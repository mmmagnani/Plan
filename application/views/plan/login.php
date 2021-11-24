<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema de Planejamento de Obtenções">
    <meta name="author" content="Magnani">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon-16x16.png'); ?>">
    <title>SPO - Sistema de Planejamento de Obtenções</title>
    <!-- Bootstrap Core CSS -->
    <link href="<?= base_url('assets/css/lib/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/helper.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">

    <link href="<?= base_url('assets/css/lib/sweetalert/sweetalert.css'); ?>" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:** -->
    <!--[if lt IE 9]>
		<script src="https:**oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https:**oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        .error {
            border-color: #fc6180;
        }
    </style>
</head>


<body class="fix-header fix-sidebar">
    <!-- Preloader - style you can find in spinners.css -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <!-- Main wrapper  -->
    <div id="main-wrapper">

        <div class="unix-login">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-4">
                        <div class="login-content card">
                            <div class="login-form">
                                <h4>
                                    <img class="img-responsive" src="<?= base_url('assets/images/logo-full.png') ?>" alt="Logo" />
                                </h4>
                                <form id="form-login" method="post" action="<?= site_url('plan/verificarLogin') ?>">
                                    <div class="form-group" id="progress-acessar" style="display: none">
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                                aria-valuemax="100" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input id="email" name="email" class="form-control" type="text" placeholder="Email" />
                                    </div>
                                    <div class="form-group">
                                        <label>Senha</label>
                                        <input name="senha" class="form-control" type="password" placeholder="Senha" />
                                    </div>
                    				<!--<div class="form-group">
                                    	<label>Ano</label>
                            			<input id="anofiscal" name="anofiscal" class="form-control" type="text" placeholder="Ano do Planejamento" />
                        			</div>-->

                                    <button type="submit" id="btn-acessar" class="btn btn-info btn-flat m-b-30 m-t-30">Acessar</button>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- End Wrapper -->

    <script src="<?= base_url('assets/js/lib/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/lib/form-validation/jquery.validate.min.js') ?>"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="<?= base_url('assets/js/jquery.slimscroll.js'); ?>"></script>
    <!--Menu sidebar -->
    <script src="<?= base_url('assets/js/sidebarmenu.js'); ?>"></script>
    <!--stickey kit -->
    <script src="<?= base_url('assets/js/lib/sticky-kit-master/dist/sticky-kit.min.js'); ?>"></script>
    <!--Custom JavaScript -->
    <script src="<?= base_url('assets/js/scripts.js'); ?>"></script>

    <script src="<?= base_url('assets/js/lib/sweetalert/sweetalert.min.js'); ?>"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#email').focus();
            $("#form-login").validate({
                rules: {
                    email: { required: true, email: true },
                    senha: { required: true }//,
					//anofiscal: { required: true}
                },
                messages: {
                    email: { required: 'Insira o e-mail.', email: 'Insira um email válido' },
                    senha: { required: 'Insira sua senha.' }//,
					//anofiscal: {required: 'Campo Requerido.'}
                },
                submitHandler: function (form) {
                    var dados = $(form).serialize();
                    
                    $('#progress-acessar').show();

                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('plan/verificarLogin?ajax=true'); ?>",
                        data: dados,
                        dataType: 'json',
                        success: function (data) {
                            if (data.result == true) {
                                window.location.href = "<?= site_url('plan'); ?>";
                            }
                            else {

                                $('#progress-acessar').hide();
                                sweetAlert("Oops...", "Dados de acesso são inválidos! Tente novamente.", "error");
                            }
                        },
                        fail: function () {
                            sweetAlert("Oops...", "Ocorreu um problema ao tentar efetuar o login! Tente novamente.", "error");
                        }
                    });

                    return false;
                },

                errorClass: "help-inline",
                errorElement: "span",
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('error');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('error');
                }
            });

        });

    </script>


</body>

</html>