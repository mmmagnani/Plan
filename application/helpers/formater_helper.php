<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * Função para formatar Telefone, CEP, CPF, CNPJ e RG
 *
 * Escolher tipo de formatação ( fone, cep, cpf, cnpj ou rg) 
 * Lembrar de colocar em lowercase
 * @param $tipo  string
 *   
 * Enviar string que para ser formata ex: 13974208014;
 * @param $string  string   
 *
 * Quantidade de caracteres a serem formatados, 
 * só serve para o telefone 10 para o padrão antigo e 11 para novo padrão com 9
 * @param $size  integer  
 *
 *
 * Valor formatado do padrão escolhido
 * @return $string  string   
 */
 

if ( !function_exists('formatar'))
{
function formatar ($tipo = "", $string, $size = 10)
{
    $string = preg_replace("[^0-9]", "", $string);

    $dias = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado');
    $meses = array('', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
    
    switch ($tipo)
    {
        case 'fone':
            if($size === 10){
             $string = '(' . substr($tipo, 0, 2) . ') ' . substr($tipo, 2, 4) 
             . '-' . substr($tipo, 6);
         }else
         if($size === 11){
             $string = '(' . substr($tipo, 0, 2) . ') ' . substr($tipo, 2, 5) 
             . '-' . substr($tipo, 7);
         }
         break;
        case 'cep':
            $string = substr($string, 0, 5) . '-' . substr($string, 5, 3);
         break;
        case 'cpf':
            $string = substr($string, 0, 3) . '.' . substr($string, 3, 3) . 
                '.' . substr($string, 6, 3) . '-' . substr($string, 9, 2);
         break;
        case 'cnpj':
            $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) . 
                '.' . substr($string, 5, 3) . '/' . 
                substr($string, 8, 4) . '-' . substr($string, 12, 2);
         break;
        case 'rg':
            $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) . 
                '.' . substr($string, 5, 3);
         break;
		case 'mes':
		    $string = utf8_encode($meses[intval($string)]);	
		 break; 
		case 'data_extenso':
		 	$dia = date('d', strtotime($string));
			$mes = date('m',strtotime($string));
			$ano = date('Y', strtotime($string));
			$semana = date('w', strtotime($string));
			$mes = utf8_encode($meses[intval($mes)]);
			$semana = utf8_encode($dias[intval($semana)]);
			$string = $semana . ", " . $dia . " de " . $mes . " de " . $ano;
		 break;
        default:
         $string = 'É ncessário definir um tipo(fone, cep, cpg, cnpj, rg, mes, data_extenso)';
         break;
    }
    return $string;
}
}
?>
