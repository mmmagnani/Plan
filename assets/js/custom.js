
$(function () {
	$('input:radio[name="checkBoxObjeto"]').change(function() {
		if($("#material").prop("checked")) {
    // Atribui evento e função para limpeza dos campos
    $('#titulo').on('input', limpaCampos);
    
    // Dispara o Autocomplete a partir do terceiro caracter
    $( "#titulo" ).autocomplete({
	    minLength: 3,
	    source: function( request, response ) {
	        $.ajax({
	            url: "complete",
	            dataType: "json",
	            data: {
	            	acao: 'autocomplete',
	                parametro: $('#titulo').val()
	            },
	            success: function(data) {
	               response(data);
	            }
	        });
	    },
	    click: function( event, ui ) {
	        $("#titulo").val( ui.item.descPDM );
	        carregarDados();
	        return false;
	    },
	    select: function( event, ui ) {
	        $("#titulo").val(ui.item.descPDM);
			$("#CATMAT").val(ui.item.CATMAT);
			$("#descricao").val(ui.item.descCATMAT);
	        return false;
	    }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a><b>CATMAT: </b>" + item.CATMAT + "<br> <b>Título: </b>" + item.descPDM + "<br> <b>Descrição: </b>" + item.descCATMAT + "</a>" )
        .appendTo( ul );
    };

    // Função para carregar os dados da consulta nos respectivos campos
    function carregarDados(){
    	var titulo = $('#titulo').val();

    	if(titulo != "" && titulo.length >= 3){
    		$.ajax({
	            url: "consulta",
	            dataType: "json",	
	            data: {
	            	acao: 'consulta',
	                parametro: $('#titulo').val()
	            },
	            success: function( data ) {
	               $('#CATMAT').val(data[0].CATMAT);
	               $('#titulo').val(data[0].descPDM);
	               $('#descricao').val(data[0].descCATMAT);
	            }
	        });
    	}
    }

    // Função para limpar os campos caso a busca esteja vazia
    function limpaCampos(){
       var titulo = $('#titulo').val();

       if(titulo == ""){
	   	   $('#CATMAT').val('');
          
           $('#descricao').val('');
       }
    }
   }
 });
});
