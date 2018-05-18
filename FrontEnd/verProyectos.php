<html>
	<head>
		<?php	

			session_start();

			if (!isset($_SESSION['token']))
			{
				header("Location: http://localhost/quieroservicios/FrontEnd/login.php");
				die();
			}
			if (isset($_SESSION['time']) && (time() - $_SESSION['time'] > 1800)) {
			    // last request was more than 30 minutes ago
			    session_unset();     // unset $_SESSION variable for the run-time 
			    session_destroy();   // destroy session data in storage
			    header("Location: http://localhost/quieroservicios/FrontEnd/login.php");
				die();
			}
		?>
		<title>FrontEnd Test</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script>

 function ajaxcall(projectID)
{
	if (window.confirm("Esta seguro?")) 
	{
		$.ajax({
		type: "DELETE",
	  	beforeSend: function(rqt) {
	    rqt.setRequestHeader("authorization", <?php echo "'".$_SESSION['token']."'";?>);
	  	},
	  	url: "http://localhost/quieroservicios/servicios/v1/projects.php/deleteproject",
	  	data: {"projectID" : projectID} ,

	    success: function(data){
	    	if(data["status"].code == 200){
	    		alert("Proyecto eliminado.");
	    	}
	    	else {
	    		alert("Error al intentar eliminar el proyecto.");
	    	}
	    	
	        window.location.reload();
	    },
	    error: function(){
	        alert("Error al intentar eliminar el proyecto");
	    }
			});
		}
 };

 </script>
	</head>

<body>

<div>
	<h2>Listado de proyectos</h2>
		<?php

			$postdata = http_build_query(
			    array(
			    )
			);

			$opts = array('http' =>
			    array(
			        'method'  => 'GET',
			        'header'  => array("Content-type: application/x-www-form-urlencoded", "Authorization: ".$_SESSION['token'].""), 
			        'content' => $postdata
			    )
			);

			$context  = stream_context_create($opts);

			$url = 'http://localhost/quieroservicios/servicios/v1/projects.php/getProjectsByUserID';
			$result = file_get_contents($url, false, $context);
			$json = json_decode($result, true);
			
			if ($json['status']['code'] != '200')
			{
				echo "<h2>". ($json['status']['description']) . "</h2>";
			}
			else
			{
				echo "<table style='width:80%' border='1'>";
				echo "<tr>";
				echo "<th>Nombre del proyecto</th>";
				echo "<th>Profesion</th>";
				echo "<th>Fecha de alta</th>";
				echo "<th>Estado</th>";
				echo "<th>Descripcion</th>";
				echo "<th>Acciones</th>";
				echo "</tr>";

				foreach ($json["values"] as $value) 
				{
					echo "<tr>";
				    echo "<th>".$value["projectName"]."</th>";
				    echo "<th>".$value["professionName"]."</th>";
				    echo "<th>".$value["registerDate"]."</th>";
				    echo "<th>".$value["statusName"]."</th>";
				    echo "<th>".$value["projectDescription"]."</th>";
				    echo "<th><button id='project".$value["projectID"]."' onclick='ajaxcall(".$value["projectID"].")'>Eliminar proyecto</button></br><button>Ver presupuestos</button></th>";
					echo "</tr>";
				}

				echo "</table>";

			}
		?>
</div>
	<div><a href="http://localhost/quieroservicios/FrontEnd/index.php"><button>Volver al menu</button></a></div>
</body>
</html>