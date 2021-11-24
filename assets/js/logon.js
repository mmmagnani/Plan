        $(document).ready(function () {

            $('#email').focus();
            $("#form-login").validate({
                rules: {
                    email: { required: true, email: true },
                    senha: { required: true },
					anofiscal: { required: true}
                },
                messages: {
                    email: { required: 'Insira o e-mail.', email: 'Insira um email válido' },
                    senha: { required: 'Insira sua senha.' },
					anofiscal: {required: 'Campo Requerido.'}
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

        });// JavaScript Document