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
			        'userPassword' => $_POST["userPassword"],
			        'roleID' => $_POST["roleID"]
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

			$url = 'http://localhost/quieroservicios/servicios/v1/users.php/insertuser';
			$result = file_get_contents($url, false, $context);
			$json = json_decode($result, true);
			
			if ($json['status']['code'] != '200')
			{
				echo "<h2>".$json['status']['description']. "</h2>";
				echo "	<div><a href='http://localhost/quieroservicios/FrontEnd/crearCuenta.php'><button>Reintentar</button></a></div>";
			}
			else
			{
				echo "<h2>Usuario dado de alta de forma correcta</h2>";
				echo "<h3>Se ha enviado un mail de autenticacion a su casilla de correo</h3>";
				echo "	<div><a href='http://localhost/quieroservicios/FrontEnd/login.php'><button>Volver</button></a></div>";

			}

		}
		else
		{
		echo"<h2>Ingrese un email y password.</h2>";
		
		echo "<div>";
		echo	"<div id='contact_form'>";
		echo		"<form action=".$_SERVER['PHP_SELF']." method='post'>";
		echo		'<div>';
		echo			"<label for='userEmail'>Correo electr&oacute;nico: </label>";
		echo			"<input type='text' class='required input_field' id='userEmail' name='userEmail' />";
		echo		'</div>';
		echo		'<div>';
		echo			'<label>Contrase&ntilde;a: </label>';
		echo			"<input type='userPassword' class='required input_field' id='password' name='userPassword' size='36px' />";
		echo		"</div>";
		echo						'<div>';
		echo			"<label for='roleID'>Tipo de cuenta: </label>";
		echo			"<select id='roleID' name='roleID'>";

			$opts = array('http' =>
			    array(
			        'method'  => 'GET',
			        'header'  => 'Content-type: application/x-www-form-urlencoded',
			        'content' => ''
			    )
			);

			$context  = stream_context_create($opts);

			$url = 'http://localhost/quieroservicios/servicios/v1/roles.php/getroles';
			$result = file_get_contents($url, false, $context);
			$json = json_decode($result, true);

			if ($json['status']['code'] == '200')
			{
				foreach ($json["values"] as $value) 
				{
				    echo "<option value='".$value["roleID"]."'>".$value["roleDescription"]."</option>";
				}
			}

		echo			'</select>';
		echo		'</div>';
		echo	'<div>';
		echo		"<input type='submit' name='submit' id='submit' value='Crear cuenta' />";
		echo	'</div>';
		echo		'</form>';		

			
		echo "</div>";
	}
?>
</div>
</body>
</html>