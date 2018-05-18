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
	</head>

	<body>

		<div>
			<h2>Bienvenido</h2>
			<div>
			</h3>Perfil del cliente</h3>
				<ul>
					<li><a href="altaProyecto.php">Iniciar un nuevo proyecto</a></li>
					<li><a href="verProyectos.php">Ver proyectos</a></li>
					<li><a href="editarPerfil.php">Editar perfil</a></li>
				</ul>
			</div>	
		</div>

	</body>
</html>