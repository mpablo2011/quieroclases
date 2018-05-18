<html>
<head>

<title>FrontEnd Test</title>

</head>

<body>

<div>
		<?php
		if(!empty($_POST))
		{
			session_start();

			$postdata = http_build_query(
			    array(
			        'projectName' => $_POST["projectName"],
			        'professionID' => $_POST["professionID"],
			        'projectDescription' => $_POST["projectDescription"]
			    )
			);

			$opts = array('http' =>
			    array(
			        'method'  => 'POST',
			        'header'  => array("Content-type: application/x-www-form-urlencoded", "Authorization: ".$_SESSION['token'].""), 
			        'content' => $postdata
			    )
			);

			$context  = stream_context_create($opts);

			$url = 'http://localhost/quieroservicios/servicios/v1/projects.php/insertProject';
			$result = file_get_contents($url, false, $context);
			$json = json_decode($result, true);
			print_r($json);
			
			if ($json['status']['code'] != '200')
			{
				echo "<h2>". ($json['status']['description']) . "</h2>";
				print_r($opts);
			}
			else
			{
				echo "<h2>Proyecto dado de alta de forma correcta</h2>";
				echo "<h4><a href='http://localhost/quieroservicios/FrontEnd/index.php'>Volver al inicio</a></h4>";

			}

		}
		else
			echo"<h2>Ingrese los datos del proyecto</h2>";
		?>
	<div>		
			<div id="contact_form">
				<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
				<div>
					<label for="projectName">Nombre del proyecto: </label>
					<input type="text" class="required input_field" id="projectName" name="projectName" />
				</div>
				<div>
					<label for="professionID">Profesion requerida: </label>
					<select id="professionID" name="professionID">
		<?php
			$opts = array('http' =>
			    array(
			        'method'  => 'GET',
			        'header'  => 'Content-type: application/x-www-form-urlencoded',
			        'content' => ''
			    )
			);

			$context  = stream_context_create($opts);

			$url = 'http://localhost/quieroservicios/servicios/v1/professions.php/getprofessions';
			$result = file_get_contents($url, false, $context);
			$json = json_decode($result, true);

			if ($json['status']['code'] == '200')
			{
				foreach ($json["values"] as $value) 
				{
				    echo "<option value='".$value["professionID"]."'>".$value["professionName"]."</option>";
				}
			}
		?>	
					</select>
				</div>
				<div>
					<label for="projectDescription">Descripcion: </label>
					<input type="text" class="required input_field" id="projectDescription" name="projectDescription" />
				</div>
			<div>
				<input type="submit" name="submit" id="submit" value="OK" />
			</div>
			</form>		

			
	</div>
</div>
	<div><a href="http://localhost/quieroservicios/FrontEnd/index.php"><button>Volver al menu</button></a></div>
</body>
</html>