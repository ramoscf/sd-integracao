<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

$today = date('Y-m-d');
$dataFormat = date('d.m.Y');


//PRODUTOS
try{
	##FIREBIRD
	$con1 = new PDO('firebird:dbname=riosulrionorte.ddns.com.br/3050:/SDSuper/Dados/SDSuper.fdb', 'CONSULTORIA', 'CONSULTA321');
	$con1->query("SET CHARACTER SET utf8");
	$sql_stmt2 = $con1->prepare("SELECT WIDPRODUTO, WNOMEGONDOLA, WDATAULTIMAALTERACAO, WCODIGOPRINCIPAL, WIDPRODUTOGRUPO, WIDUNIDADE
	 FROM PRODUTOS WHERE WDATAULTIMAALTERACAO >= '$dataFormat' ORDER BY WIDPRODUTO");
//
   $sql_stmt2->execute();

   while ($row = $sql_stmt2->fetch())
   {
	$id = $row[0];
	$codigo = $row[0];
	$descricao = $row[1];
	$dataalteracao = $row[2];
	$sku = $row[3];
	if($row[4] == '5941'){$idgrupo = 'PATRIMONIAL';}
	if($row[4] == '5951'){$idgrupo = 'PERECIVEIS';}
	if($row[4] == '5961'){$idgrupo = 'MERCEARIA';}
	if($row[4] == '5971'){$idgrupo = 'LEITES';}
	if($row[4] == '5981'){$idgrupo = 'CEREAIS';}
	if($row[4] == '821'){$idgrupo = 'A CLASSIFICAR';}
	if($row[4] == '5991'){$idgrupo = 'CHINELOS';}

	if($row[5] == '1'){$proporcao = 'CAIXA';}
	if($row[5] == '2'){$proporcao = 'COPO';}
	if($row[5] == '3'){$proporcao = 'DUZIA';}
	if($row[5] == '4'){$proporcao = 'KILOGRAMA';}
	if($row[5] == '5'){$proporcao = 'LITRO';}
	if($row[5] == '6'){$proporcao = 'METRO';}
	if($row[5] == '7'){$proporcao = 'POTE';}
	if($row[5] == '8'){$proporcao = 'UNIDADE';}
	if($row[5] == '9'){$proporcao = 'VIDRO';}
	if($row[5] == '10'){$proporcao = 'GRAMA';}
	if($row[5] == '91'){$proporcao = 'KG';}
	if($row[5] == '101'){$proporcao = 'FD';}
	if($row[5] == '111'){$proporcao = 'CX';}
	if($row[5] == '121'){$proporcao = 'EB';}
	if($row[5] == '131'){$proporcao = 'CXA';}
	if($row[5] == '151'){$proporcao = 'KGS';}
	if($row[5] == '141'){$proporcao = 'LT';}
	if($row[5] == '161'){$proporcao = 'PC';}
	if($row[5] == '20'){$proporcao = 'CENTIMETRO';}
	if($row[5] == '171'){$proporcao = 'G';}
	if($row[5] == '191'){$proporcao = 'L';}
	if($row[5] == '201'){$proporcao = 'M';}
	if($row[5] == '211'){$proporcao = 'CJ';}
	if($row[5] == '221'){$proporcao = 'CM';}
	if($row[5] == '181'){$proporcao = 'ML';}
	##MYSQL
	$con2 = new PDO('mysql:host=cartazfacilpro.ctj8bnjcqdvd.us-east-2.rds.amazonaws.com;dbname=riosul','cartazdb', 'tbCJShR2');
	$con2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$query2 = $con2->query("SELECT count(*) as quantidade from `cf_produto` WHERE `prod_id`='$id'");
	$rows = $query2->fetchAll(PDO::FETCH_OBJ);
	
	foreach($rows as $r => $value) {
		$quantidadeProd =  $value->quantidade;
	}


	   if ($quantidadeProd < 1) {
		$insert = $con2->prepare('INSERT INTO cf_produto (prod_id, prod_cod,prod_nome,prod_sessao,prod_empresa,prod_filial,prod_sku,prod_proporcao,prod_desc,prod_revisao,prod_flag100g) 
		VALUES(:prod_id,:prod_cod,:prod_nome,:prod_sessao,:prod_empresa,:prod_filial,:prod_sku,:prod_proporcao,:prod_desc,:prod_revisao,:prod_flag100g)');
		$insert->execute(array(
		  ':prod_id' =>  $id,
		  ':prod_cod' =>  $codigo,
		  ':prod_nome' => $descricao,
		  ':prod_sessao' => $idgrupo,
		  ':prod_empresa' => '1',
		  ':prod_filial' => '1',
		  ':prod_sku' => $sku,
		  ':prod_proporcao' => $proporcao,
		  ':prod_desc' => $descricao,
		  ':prod_revisao' => '0',
		  ':prod_flag100g' => '0',
		));

	   }

}
}
catch(Exception $e){}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	//VALORES
	try{
		##FIREBIRD
		$con1 = new PDO('firebird:dbname=riosulrionorte.ddns.com.br/3050:/SDSuper/Dados/SDSuper.fdb', 'CONSULTORIA', 'CONSULTA321');
		$con1->query("SET CHARACTER SET utf8");
		$sql_stmt2 = $con1->prepare("SELECT WIDFILIAL, WIDPRODUTO, WPRECO FROM PRECOSVENDA WHERE WDATAALTERACAO >= '$dataFormat'"); //
  
	   $sql_stmt2->execute();

	   while ($row = $sql_stmt2->fetch())
	   {
		$filial = $row[0];
		echo '<br>';
		echo $idproduto = $row[1];
		$valor3 = $row[2];
		$valor2 = number_format($valor3, 2, '.', '');
		echo '<br>';
		echo $valor = str_replace('.',',',$valor2);   
		

		##MYSQL
		$con2 = new PDO('mysql:host=cartazfacilpro.ctj8bnjcqdvd.us-east-2.rds.amazonaws.com;dbname=riosul','cartazdb', 'tbCJShR2');
		$con2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$query2 = $con2->query("SELECT count(*) as quantidade ,vlr_produto from `cf_valor` WHERE `vlr_produto`='$idproduto' AND `vlr_filial`='$filial'");
		$rows = $query2->fetchAll(PDO::FETCH_OBJ);
		
		foreach($rows as $r => $value) {
			$quantidade =  $value->quantidade;
		}

    	   if ($quantidade > 0) {			
				$insert = $con2->prepare('UPDATE cf_valor SET vlr_valores = :vlr_valores, vlr_data_de = :vlr_data_de, vlr_data_ate = :vlr_data_ate, vlr_hora = :vlr_hora WHERE `vlr_produto`= :vlr_produto AND `vlr_filial`= :vlr_filial');
     			$insert->execute(array(
				':vlr_produto' =>  $idproduto,
				':vlr_filial' => $filial,
				':vlr_valores' => $valor,
				':vlr_data_de' => $today,
				':vlr_data_ate' => $today,
				':vlr_hora' => '08:06'
				));		  
		
		    }else{			
				$insert = $con2->prepare('INSERT INTO cf_valor (vlr_produto,vlr_filial,vlr_empresa,vlr_valores,vlr_idcomercial,vlr_data_de,vlr_data_ate,vlr_usuario,vlr_hora) 
				VALUES(:vlr_produto,:vlr_filial,:vlr_empresa,:vlr_valores,:vlr_idcomercial,:vlr_data_de,:vlr_data_ate,:vlr_usuario,:vlr_hora)');
				$insert->execute(array(
				':vlr_produto' =>  $idproduto,
				':vlr_filial' => $filial,
				':vlr_empresa' => '1',
				':vlr_valores' => $valor,
				':vlr_idcomercial' => '1',
				':vlr_data_de' => $today,
				':vlr_data_ate' => $today,
				':vlr_usuario' => '1',
				':vlr_hora' => '06:06'
				));		  
				
		   }
	
		$con1 = null;
		$con2 = null;	
		}

		$con2 = new PDO('mysql:host=cartazfacilpro.ctj8bnjcqdvd.us-east-2.rds.amazonaws.com;dbname=riosul','cartazdb', 'tbCJShR2');
		$con2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$insert = $con2->prepare('INSERT INTO cf_logs (log_empresa,log_filial,log_usuario,log_data,log_mensagem) VALUES (:log_empresa,:log_filial,:log_usuario,:log_data,:log_mensagem)');
		$insert->execute(array(
		':log_empresa' =>  '333',
		':log_filial' =>'333',
		':log_usuario' => '333',
		':log_data' =>  $today,
		':log_mensagem' => 'IMPORTACAO DE VALORES e PRODUTOS REALIZADA',
		));	
	}
	catch(Exception $e){}
	
	
	echo "Produtos e Valores Atualizados;";

	?>