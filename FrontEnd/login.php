<html>
<head>
		<?php	
			if ( isset($_SESSION['token']))
			{
				header("Location: http://localhost/quieroservicios/FrontEnd/index.php");
				die();
			}
		?>
<title>FrontEnd Test</title>

</head>

<body>

<div>
		<?php
		if(!empty($_POST))
		{

			$postdata = http_build_query(
			    array(
			        'userEmail' => $_POST["userEmail"],
			        'userPassword' => $_POST["userPassword"]
			    )
			);

			$opts = array('http' =>
			    array(
			        'method'  => 'POST',
			        'header'  => 'Content-type: application/x-www-form-urlencoded',
			        'content' => $postdata
			    )
			);

			$context  = stream_context_create($opts);

			$url = 'http://localhost/quieroservicios/servicios/v1/authentication.php/authenticateUser';
			$result = file_get_contents($url, false, $context);
			$json = json_decode($result, true);
			
			if ($json['status']['code'] != '200')
				echo "<h2>". ($json['status']['description']) . "</h2>";
			else
			{
				echo "<h2>Bienvenido</h2>";
				session_start();
				$_SESSION['token'] = $json['token'];
				$_SESSION['time'] = time();
				header("Location: http://localhost/quieroservicios/FrontEnd/index.php");

			}

		}
		else
			echo"<h2>Por favor ingrese su usuario y password.</h2>";
		?>
	<div>		
			<div id="contact_form">
				<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
				<div>
					<label for="userEmail">Correo electr&oacute;nico: </label>
					<input type="text" class="required input_field" id="userEmail" name="userEmail" />
				</div>
				<div>
					<label>Contrase&ntilde;a: </label>
					<input type="userPassword" class="required input_field" id="password" name="userPassword" size="36px" />
				</div>
			<div>
				<input type="submit" name="submit" id="submit" value="Ingresar" />
			</div>
			</form>		

			
	</div>
</div>
	<div><a href="http://localhost/quieroservicios/FrontEnd/crearCuenta.php"><button>Crear cuenta</button></a></div>
</body>
</html>