<!DOCTYPE html>
<html translate="no">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>---Waytop Inglês - Nivelamento de Inglês</title>
    <link rel="icon" href="images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,400;0,600;0,700;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
	<div class="container">           
	    <h2>OBRIGADO!</h2>
	    <p>Instruções do teste foram enviadas para seu e-mail.</p>

		<?php
			require_once ("database/conexao.php");
			$id = $_GET["id"];
			$nome = $_GET["nome"];
			$email = $_GET["email"];
			$telefone = $_GET["telefone"];
			$empresa = $_GET["empresa"];
			$satisfeito = $_GET["satisfeito"];
			$aprimorar = $_GET["aprimorar"];
			$nota_inicial = '';
			$nota_final = '';
			$nivelamento = '';
			$justificativa = '';

			if (!isset($_GET["resposta"])){
				$resposta=0;
			}else{
				$resposta =  $_GET["resposta"];
			}

			$pontos =  $_GET["pontos"];
			$totalpontos = $_GET["totalpontos"];

			// Calcular pontuação e exibir resultado
			$query3 = "SELECT nota_inicial,nota_final,nivelamento,justificativa FROM resultado where $totalpontos BETWEEN nota_inicial and nota_final";
			if ($result3 = $mysqli->query($query3)){
				/* fetch associative array */
				while ($row3 = $result3->fetch_assoc()){	
					$nota_inicial =  $row3["nota_inicial"];
					$nota_final =  $row3["nota_final"];        
					$nivelamento = $row3["nivelamento"];
					$justificativa = $row3["justificativa"];
				}
			}
			
			$result3->free(); 

			// Enviar email com resposta para o aluno	
			$from = "contato@waytop.com";
			//$to = "nivelamento@waytop.com.br";
			$to = $email;			
			$subject = "Waytop - Nivelamento (".$nome.")";
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-utf-8' . "\r\n";
			// Create email headers
			$headers .= 'De: '.$from."\r\n".'X-Mailer: PHP/' . phpversion();
			$message  = '<html><body>';
			$message .= '<h3>Olá '.$nome.', tudo bem?</h3>';
			$message .= '<p align="justify">Primeiramente agradecemos o teste realizado com a gente.</p>';
			$message .= '<p align="justify">Você sabia que existe um padrão reconhecido internacionalmente para avaliar o nível de inglês?</p>';
			$message .= '<p align="justify">Aqui no Brasil, é comum falarmos de nível de inglês básico, intermediário e avançado, mas esse tipo de classificação muda de acordo com o avaliador e não permite saber exatamente qual é o nível de conhecimento pessoal.</p>';
			$message .= '<p align="justify">Por isso, em empresas multinacionais e no mercado internacional, o nível de proficiência em idiomas é avaliado a partir do CEFR - Common European Framework of Reference for Languages.</p>';
			$message .= '<p align="justify">O CEFR divide o nível de conhecimento em um idioma em seis escalas: A1("básico"), A2; B1, B2; C1 e C2 ("proficiência").</p>';
			$message .= '<p align="justify">O interessante é que com Teste de Nivelamento realizado com a gente, você poderá ver o seu resultado em conformidade com CEFR e se a sua pontuação superar 98 pontos, vamos te dar um teste oral, 100% gratuito, com um dos nossos profissionais para avaliar ainda melhor o seu nível de conhecimento de inglês.</p>';
			$message .= '<p align="justify">No mercado corporativo, muitas empresas já utilizam o CEFR como referência para determinar critérios de contratação e promoção, quanto mais alto o nível hierárquico, maior costuma ser o nível de conhecimento em inglês exigido.</p>';
			$message .= '<p align="justify">Daremos início a sua avaliação e breve entraremos em contato, lhe passando o seu resultado e te explicando melhor essa nova maneira que o mercado tem avaliado os profissionais.</p>';
			$message .= '<p align="justify">Lembrando que em virtude da parceria com a [EMPRESA] e a WayTop, o resultado do teste de nivelamento é 100% gratuito e também 100% confidencial para você.</p>';
			$message .= '<p align="justify">Abraços,</p>';
			$message .= '<p><img src="waytop.com.br/teste/img/waytop-ceo-andre-doraciotto.png"</p>';
			$message .= '</body></html>';
			// Acentuação correta
			$message = utf8_decode($message);
			$subject = utf8_decode($subject);		
					
			if(mail($to, $subject, $message, $headers)) {	
				//echo '<br>Email enviado para o aluno com sucesso.<br>';
			} else {	
				echo '<br>Email não foi enviado..<br>';
			}
			    	
			// Enviar email e encerrar.    		
			$from = 'contato@waytop.com';
			//$to = "nivelamento@waytop.com.br";
			$to = 'andre@waytop.com';
						
			$subject = "Waytop - Nivelamento";
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-utf-8' . "\r\n";
			// Create email headers
			$headers .= 'De: '.$from."\r\n".'X-Mailer: PHP/' . phpversion();
			$message  = '<html><body>';
			$message .= '<br>Empresa: <b>'.$empresa;
			$message .= '</b><br>Nome: <b>'.$nome;
			$message .= '</b><br>Email: <b>'.$email;
			$message .= '</b><br>Telefone: <b>'.$telefone;
			$message .= '</b><br>Total de pontos: <b>'.$totalpontos;
			$message .= '</b><br><br>Satisfeito com o próprio conhecimento: <b>'.$satisfeito;
			$message .= '</b><br>Deseja aprimorar o próprio conhecimento: <b>'.$aprimorar;
			$message .= '</b><br><br>Nivelamento: <b>'.$nivelamento;
			$message .= '</b><br>Justificativa: <b>'.$justificativa;
			$message .= '</body></html>';
			// Acentuação correta
			$message = utf8_decode($message);
			$subject = utf8_decode($subject);			
						
			//echo"to=".$to." subject=".$subject." message=".$message." headers=".$headers;
			//die();			
						
			if(mail($to, $subject, $message, $headers)) {	
				//echo '<br>Email enviado para o comercial.<br>';
				} else {	
				echo '<br>Email não foi enviado..<br>';
				}
			die();   		
		?>
	</div>
</body>
</html>