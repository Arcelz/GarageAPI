<?php

require 'Banco.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

Class Funcionario
{

      public static function getUsuario(){
        $getUsuario = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
        $permicao = $getUsuario->usuario();
        //var_dump($permicao) ;
        return $permicao;
    }

    public static  function geraLog($argumentos, $erroMysql ){
        $arquivo = __FILE__; //pega o caminho do arquvio.
        $geraLog = new GeraLog();
        $geraLog ->grava_log_erros_banco($arquivo,$argumentos, $erroMysql, self::getUsuario());
    }

      public static function getData(){
        $getData = new GerarData();
        return $getData ->gerarDataHora();
    }


    function get_Funcionario($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT f.pk_funcionario,f.fk_cargo,f.nome as nomes,f.cpf,f.email,f.contato1,f.contato,f.avatar,f.status,c.pk_cargos,c.nome,c.status FROM funcionarios as f LEFT JOIN cargos as c ON f.fk_cargo = c.pk_cargos WHERE f.status ='ATIVO' AND f.pk_funcionario > 1";

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query = " Select f.nome AS nomes, f.*,fe.*,c.* FROM funcionarios AS f LEFT JOIN funcionarios_enderecos AS fe ON f.pk_funcionario = fe.fk_funcionario LEFT JOIN cargos as c ON f.fk_cargo = c.pk_cargos WHERE f.status = 'ATIVO' AND f.pk_funcionario > 1 AND f.pk_funcionario = :id LIMIT 1";

            }

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetchAll();


            if ($row == null) {
                $response = array(
                    'status' => 400,
                    'status_message' => 'Nao foi possivel realizar a pesquisa'
                );
                header("HTTP/1.0 400 ");

            } else {
                $stmt->execute();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //$response[]= $row;
                    array_push($response, $row);
                }

            }

        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "Pesquisando .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function insert_Funcionario()
    {
	
        try {
            $db = Banco::conexao();
            $status = 'ATIVO';
	    $data = self::getData();
            $dataEx = explode("/",$data);
	    $diretorio = "../imagens/".$dataEx[1]."-".$dataEx[0];
	    $filename_path = md5(time().uniqid()).".jpg";// salvar imagem
	    $urlIMG = substr($diretorio,2)."/".$filename_path;
            $query = "INSERT INTO funcionarios(nome,cpf,email,contato1,contato,status,fk_cargo,avatar) values (:nome,:cpf,:email,:contato1,:contato,:status,:fkCargo,'$urlIMG')";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
            $stmt->bindParam(':cpf', $_POST['cpf'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->bindParam(':contato1', $_POST['contato1'], PDO::PARAM_STR);
            $stmt->bindParam(':contato', $_POST['contato'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':fkCargo', $_POST['fkCargo'], PDO::PARAM_INT);
            $stmt->execute();

            $te = $stmt->rowCount();//retornado quantas linhas foi executada.
            //var_dump($te);
            if ($te > 0) {
                $fk = $db->lastInsertId();
                //("INSERT INTO clientes_enderecos(logradouro,bairro,cidade,estado,pais,cep,fk_cliente)
                $query2 = "INSERT INTO funcionarios_enderecos(logradouro,bairro,cidade,estado,pais,cep,fk_funcionario) 
                    values (:logradouro,:bairro,:cidade,:estado,:pais,:cep,:fk)";
                $stmt = $db->prepare($query2);

                $stmt->bindParam(':logradouro', $_POST['logradouro'], PDO::PARAM_STR);
                $stmt->bindParam(':bairro', $_POST['bairro'], PDO::PARAM_STR);
                $stmt->bindParam(':cidade', $_POST['cidade'], PDO::PARAM_STR);
                $stmt->bindParam(':estado', $_POST['estado'], PDO::PARAM_STR);
                $stmt->bindParam(':pais', $_POST['pais'], PDO::PARAM_STR);
                $stmt->bindParam(':cep', $_POST['cep'], PDO::PARAM_STR);
                $stmt->bindParam(':fk', $fk, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'status' => 200,
                    'status_message' => 'Funcionario adicionado.'
                );
 if (!is_dir($diretorio)) {   
 	 mkdir($diretorio, 0777, true);
         }
 $decoded=base64_decode($_POST['avatar']);// salvar imagem
            file_put_contents($diretorio."/".$filename_path,$decoded);// salvar imagem

                header("HTTP/1.0 200 ");
            }

        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "Inserido.....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function update_Funcionario($id)
    {

        try {
		 


            $db = Banco::conexao();
		
            parse_str(file_get_contents('php://input'), $post_vars);
		if(isset($post_vars['avatar'])){
			$data = self::getData();
			$dataEx = explode("/",$data);
	    		$diretorio = "../imagens/".$dataEx[1]."-".$dataEx[0];
		    $filename_path = md5(time().uniqid()).".jpg";// salvar imagem
		    $urlIMG = substr($diretorio,2)."/".$filename_path;

				
			$query = "UPDATE funcionarios AS f  JOIN funcionarios_enderecos AS fe ON f.pk_funcionario = fe.fk_funcionario
         		 SET f.nome=:nome,f.cpf=:cpf,f.avatar='$urlIMG', f.fk_cargo=:fkCargo,f.email=:email,f.contato1=:contato1,fe.logradouro=:logradouro,fe.bairro=:bairro,fe.cidade=:cidade,
         		   fe.estado=:estado,fe.pais=:pais,fe.cep=:cep,f.contato=:contato  WHERE f.pk_funcionario= :id";
		}else{
			  /// UPDATE funcionarios AS f LEFT JOIN funcionarios_enderecos as fe ON f.pk_funcionario = fe.fk_funcionario SET f.nome = nome
            		//WHERE f.pk_funcionario = ??
           		 $query = "UPDATE funcionarios AS f  JOIN funcionarios_enderecos AS fe ON f.pk_funcionario = fe.fk_funcionario
         		 SET f.nome=:nome,f.cpf=:cpf, f.fk_cargo=:fkCargo,f.email=:email,f.contato1=:contato1,fe.logradouro=:logradouro,fe.bairro=:bairro,fe.cidade=:cidade,
         		   fe.estado=:estado,fe.pais=:pais,fe.cep=:cep,f.contato=:contato  WHERE f.pk_funcionario= :id";
		}
          

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $post_vars['pk_funcionario'], PDO::PARAM_INT);
             $stmt->bindParam(':fkCargo', $post_vars['fkCargo'], PDO::PARAM_INT);
            $stmt->bindParam(':nome', $post_vars['nome'], PDO::PARAM_STR);
            $stmt->bindParam(':cpf', $post_vars['cpf'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $post_vars['email'], PDO::PARAM_STR);
            $stmt->bindParam(':contato', $post_vars['contato'], PDO::PARAM_STR);
            $stmt->bindParam(':contato1', $post_vars['contato1'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $post_vars['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':logradouro', $post_vars['logradouro'], PDO::PARAM_STR);
            $stmt->bindParam(':bairro', $post_vars['bairro'], PDO::PARAM_STR);
            $stmt->bindParam(':cidade', $post_vars['cidade'], PDO::PARAM_STR);
            $stmt->bindParam(':pais', $post_vars['pais'], PDO::PARAM_STR);
            $stmt->bindParam(':cep', $post_vars['cep'], PDO::PARAM_STR);
		
            $stmt->execute();
	     unset($db);
		$response = array(			
                'status' => 200,
                'status_message' => 'Funcionario Atualizado com sucesso'
		);
		
		if(isset($post_vars['avatar'])){
			if (!is_dir($diretorio)) {   
 	 mkdir($diretorio, 0777, true);
         }
 $decoded=base64_decode($post_vars['avatar']);// salvar imagem
            file_put_contents($diretorio."/".$filename_path,$decoded);// salvar imagem

		}
             header("HTTP/1.0 200 ");

        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "update .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_Funcionario($id)
    {

        try {

            $db = Banco::conexao();
            $status = 'DESATIVADO';

            $query = "SELECT * FROM funcionarios WHERE status ='ATIVO' AND pk_funcionario=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);//passando o paramento id na query.
            $stmt->execute();
            $row = $stmt->fetchAll();

            //Essa condição é para verificar se a url existe no servidor. Porque fazemos a consulta pelos funcionarios ativos
            if ($row == null) {
                $response = array(
                    'status' => 404,
                    'status_message' => 'Recurso nao encontrado'

                );

                header("HTTP/1.0 404 ");
            } else {
                $query = "UPDATE  funcionarios SET status='{$status}' WHERE pk_funcionario=:id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'status' => 200,
                    'status_message' => 'Funcionario Excluido com Sucesso'
                );
                header("HTTP/1.0 200 ");
            }


        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "delete .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        unset($db);
        header('Content-Type: application/json');
        //echo json_encode($response);
    }
}

