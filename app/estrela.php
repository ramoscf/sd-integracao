<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

$today = date('Y-m-d');
$dataFormat = date('d.m.Y');


//PRODUTOS
try {
	##FIREBIRD
	$con1 = new PDO('firebird:dbname=191.249.153.50:/SDSuper/Dados/SDSuper.fdb', 'CONSULTORIA', 'CONSULTA321');
	$con1->query("SET CHARACTER SET utf8");
	$sql_stmt2 = $con1->prepare("SELECT WIDPRODUTO, WNOMEGONDOLA, WDATAULTIMAALTERACAO,
	WCODIGOPRINCIPAL, produtosgrupos.wnomeprodutogrupo, WIDUNIDADE  FROM PRODUTOS
	INNER JOIN produtosgrupos ON produtos.WIDPRODUTOGRUPO = produtosgrupos.widprodutogrupo 
	WHERE WDATAULTIMAALTERACAO >= '$dataFormat'");


	$sql_stmt2->execute();

	while ($row = $sql_stmt2->fetch()) {
		$id = $row[0];
		$codigo = $row[0];
		$descricao = $row[1];
		$dataalteracao = $row[2];
		$sku = $row[3];
		$idgrupo = $row[4];
		$proporcao = $row[5];

		##MYSQL
		$con2 = new PDO('mysql:host=cartazfacilpro.ctj8bnjcqdvd.us-east-2.rds.amazonaws.com;dbname=estrela', 'cartazdb', 'tbCJShR2');
		$con2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$query2 = $con2->query("SELECT count(*) as quantidade from `cf_produto` WHERE `prod_id`='$id'");
		$rows = $query2->fetchAll(PDO::FETCH_OBJ);

		foreach ($rows as $r => $value) {
			$quantidadeProd =  $value->quantidade;
		}


		if ($quantidadeProd < 1) {
			echo "Produto Inserido:" . $id;
			echo '<br>';
			$insert = $con2->prepare('INSERT INTO cf_produto (prod_id, prod_cod,prod_nome,prod_sessao,prod_empresa,prod_filial,prod_sku,prod_proporcao,prod_desc,prod_revisao,prod_flag100g,prod_desc_alt) 
		VALUES(:prod_id,:prod_cod,:prod_nome,:prod_sessao,:prod_empresa,:prod_filial,:prod_sku,:prod_proporcao,:prod_desc,:prod_revisao,:prod_flag100g, :prod_desc_alt)');
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
				':prod_desc_alt' => $descricao,

			));
		}

		$con1 = null;
		$con2 = null;
	}
} catch (Exception $e) {
	echo $e;
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//VALORES
try {
	##FIREBIRD
	$con1 = new PDO('firebird:dbname=177.10.170.143:/SDSuper/Dados/SDSuper.fdb', 'CONSULTORIA', 'CONSULTA321');
	$con1->query("SET CHARACTER SET utf8");
	$sql_stmt2 = $con1->prepare("SELECT WIDFILIAL, WIDPRODUTO, WPRECO FROM PRECOSVENDA WHERE WDATAALTERACAO >= '$dataFormat'");

	$sql_stmt2->execute();

	##MYSQL

	while ($row = $sql_stmt2->fetch()) {
		$filial = $row[0];
		$idproduto = $row[1];

		$valor3 = $row[2];
		$valor2 = number_format($valor3, 2, '.', '');
		$valor = str_replace('.', ',', $valor2);

		$con2 = new PDO('mysql:host=cartazfacilpro.ctj8bnjcqdvd.us-east-2.rds.amazonaws.com;dbname=estrela', 'cartazdb', 'tbCJShR2');
		$con2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



		$query2 = $con2->query("SELECT count(*) as quantidade ,vlr_produto from `cf_valor` WHERE `vlr_produto`='$idproduto' AND `vlr_filial`='$filial'");
		$rows = $query2->fetchAll(PDO::FETCH_OBJ);

		foreach ($rows as $r => $value) {
			$quantidade =  $value->quantidade;
		}

		if ($quantidade > 0) {
			echo '<br>';
			echo "Valor Atualizado:" . $idproduto . "-" . $valor;
			echo '<br>';
			$insert = $con2->prepare('UPDATE cf_valor SET vlr_valores = :vlr_valores, vlr_data_de = :vlr_data_de, vlr_data_ate = :vlr_data_ate, vlr_hora = :vlr_hora WHERE `vlr_produto`= :vlr_produto AND `vlr_filial`= :vlr_filial');
			$insert->execute(array(
				':vlr_produto' =>  $idproduto,
				':vlr_filial' => $filial,
				':vlr_valores' => $valor,
				':vlr_data_de' => $today,
				':vlr_data_ate' => $today,
				':vlr_hora' => '18:18'
			));
		} else {
			echo "Valor Cadastrado:" . $idproduto . "-" . $valor;
			echo '<br>';
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
				':vlr_hora' => '18:18'
			));
		}


		$con1 = null;
		$con2 = null;
	}
} catch (Exception $e) {
}



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
// VALORES E PROMOCOES


try {
	##FIREBIRD
	$con1 = new PDO('firebird:dbname=177.10.170.143:/SDSuper/Dados/SDSuper.fdb', 'CONSULTORIA', 'CONSULTA321');
	$con1->query("SET CHARACTER SET utf8");
	$sql_stmt2 = $con1->prepare("SELECT WFILIAIS,WPRECOPROMOCAO,WDATAINICIO,
	WDATATERMINO,ANTPRODUTOS.WORDEMPDVPRODUTO, CODIGOSPRO.widproduto, PRECOSVENDA.WPRECO FROM ANTPRODUTOS
	INNER JOIN CODIGOSPRO ON ANTPRODUTOS.WIDPRODUTO = CODIGOSPRO.wordempdvproduto 
	INNER JOIN PRECOSVENDA ON CODIGOSPRO.widproduto = PRECOSVENDA.WIDPRODUTO 
	WHERE ANTPRODUTOS.WDATAINICIO >= '$dataFormat'");


	$sql_stmt2->execute();

	##MYSQL

	while ($row = $sql_stmt2->fetch()) {
		$filial = '1';
		$valor3 = $row[1];
		$valor2 = number_format($valor3, 2, '.', '');
		$valor = str_replace('.', ',', $valor2);
		$dataInicio = $row[2];
		$dataFim = $row[3];
		$idproduto = $row[5];

		$preco_antigo3 = $row[6];
		$preco_antigo2 = number_format($preco_antigo3, 2, '.', '');
		$preco_antigo = str_replace('.', ',', $preco_antigo2);

		$valor_completo = $preco_antigo . "!@#" . $valor;

		$con2 = new PDO('mysql:host=cartazfacilpro.ctj8bnjcqdvd.us-east-2.rds.amazonaws.com;dbname=estrela', 'cartazdb', 'tbCJShR2');
		$con2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$query2 = $con2->query("SELECT count(*) as quantidade ,vlr_produto from `cf_valor` WHERE `vlr_produto`='$idproduto' AND `vlr_filial`='$filial'");
		$rows = $query2->fetchAll(PDO::FETCH_OBJ);

		foreach ($rows as $r => $value) {
			$quantidade =  $value->quantidade;
		}

		if ($quantidade > 0) {
			echo '<br>';
			echo "Valor PROMOCIONAL Atualizado:" . $idproduto . "-" . $valor_completo;
			echo '<br>';
			$insert = $con2->prepare('UPDATE cf_valor SET vlr_valores = :vlr_valores, vlr_data_de = :vlr_data_de, vlr_data_ate = :vlr_data_ate,vlr_idcomercial = :vlr_idcomercial,  vlr_hora = :vlr_hora WHERE `vlr_produto`= :vlr_produto AND `vlr_filial`= :vlr_filial');
			$insert->execute(array(
				':vlr_produto' =>  $idproduto,
				':vlr_filial' => $filial,
				':vlr_valores' => $valor_completo,
				':vlr_data_de' => $dataInicio,
				':vlr_data_ate' => $dataFim,
				':vlr_idcomercial' => '2',
				':vlr_hora' => '24:24'
			));
		} else {
			echo "Valor PROMOCIONAL Cadastrado:" . $idproduto . "-" . $valor_completo;
			echo '<br>';
			$insert = $con2->prepare('INSERT INTO cf_valor (vlr_produto,vlr_filial,vlr_empresa,vlr_valores,vlr_idcomercial,vlr_data_de,vlr_data_ate,vlr_usuario,vlr_hora) 
			VALUES(:vlr_produto,:vlr_filial,:vlr_empresa,:vlr_valores,:vlr_idcomercial,:vlr_data_de,:vlr_data_ate,:vlr_usuario,:vlr_hora)');
			$insert->execute(array(
				':vlr_produto' =>  $idproduto,
				':vlr_filial' => $filial,
				':vlr_empresa' => '1',
				':vlr_valores' => $valor_completo,
				':vlr_idcomercial' => '2',
				':vlr_data_de' => $dataInicio,
				':vlr_data_ate' => $dataFim,
				':vlr_usuario' => '1',
				':vlr_hora' => '24:24'
			));
		}


		$con1 = null;
		$con2 = null;
	}
	$con2 = new PDO('mysql:host=cartazfacilpro.ctj8bnjcqdvd.us-east-2.rds.amazonaws.com;dbname=estrela', 'cartazdb', 'tbCJShR2');
	$con2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$insert = $con2->prepare('INSERT INTO cf_logs (log_empresa,log_filial,log_usuario,log_data,log_mensagem)VALUES(:log_empresa,:log_filial,:log_usuario,:log_data,:log_mensagem)');
	$insert->execute(array(
		':log_empresa' =>  '333',
		':log_filial' => '333',
		':log_usuario' => '333',
		':log_data' =>  $today,
		':log_mensagem' => 'IMPORTACAO DE VALORES e PRODUTOS REALIZADA',
	));
} catch (Exception $e) {
}


echo "Produtos e Valores Atualizados;";
