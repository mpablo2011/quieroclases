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
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$( function() {
$( "#birthdate" ).datepicker(
{
    // Formato de la fecha
    dateFormat: "dd/mm/yy",
    // Primer dia de la semana El lunes
    firstDay: 1,
    // Dias Largo en castellano
    dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
    // Dias cortos en castellano
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    // Nombres largos de los meses en castellano
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
    // Nombres de los meses en formato corto 
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
    changeYear: true
 }
);
} );
</script>

<script>
 function udtuserinfo()
{
	if (window.confirm("Esta seguro que desea actualizar la informacion del usuario?")) 
	{

		var firstName = $("#firstName").val();
  		var lastName = $("#lastName").val();
  		var birthdate = $("#birthdate").val();
  		var sexID = $("#sexID").val();
  		var areaCode = $("#areaCode").val();
  		var phoneNumber = $("#phoneNumber").val();  


		$.ajax({
		type: "POST",
		url: "http://localhost/quieroservicios/servicios/v1/users.php/insertuserinformation",
	  	data: {"firstName" : firstName, "lastName" : lastName, "birthdate" : birthdate, "sexID" : sexID, "areaCode" : areaCode, "phoneNumber" : phoneNumber} ,
	  	beforeSend: function(rqt) {
            rqt.setRequestHeader("authorization", <?php echo "'".$_SESSION['token']."'";?>);
	  	},
	    success: function(data){
	    	if(data["status"].code == 200){
	    		alert("Informacion Actualizada correctamente.");
	    	}
	    	else {
	    		alert("Error al intentar actualizar la informacion");
	    	}
	    	
	        window.location.reload();
	    },
	    error: function(){
	        alert("Error al intentar actualizar la informacion");
	    }
			});
		}
 };
 </script>

 <script>
 function getcitiesbystateproviceid()
{
	var stateProvince = $("#stateProvinceID").val();

	$.ajax({
	type: "GET",
	url: "http://localhost/quieroservicios/servicios/v1/cities.php/getcitiesbystateproviceid/" + stateProvince,
    success: function(data){
    	if(data["status"].code == 200){
    		var $cityID = $('#cityID');
            $cityID.empty();
            for (var i = 0; i < data["values"].length; i++) {
            $cityID.append("<option value='"+ data["values"][i].cityID + "'>"+ data["values"][i].cityName + "</option>");
            }

            //manually trigger a change event for the contry so that the change handler will get triggered
            $cityID.change();
    	}
    	else {
    		alert("Error al intentar obtener la informacion");
    	}
    	
    },
    error: function(){
        alert("ERROR");
    }
		});
 };

 </script>

 <script>
 function udtclientlocation()
{
	if (window.confirm("Esta seguro que desea actualizar su ubicación?")) 
	{

		var stateProvinceID = $("#stateProvinceID").val();
  		var cityID = $("#cityID").val();
  		var streetAddress = $("#streetAddress").val();


		$.ajax({
		type: "POST",
		url: "http://localhost/quieroservicios/servicios/v1/clients.php/insertclientlocation",
	  	data: {"stateProvinceID" : stateProvinceID, "cityID" : cityID, "streetAddress" : streetAddress} ,
	  	beforeSend: function(rqt) {
            rqt.setRequestHeader("authorization", <?php echo "'".$_SESSION['token']."'";?>);
	  	},
	    success: function(data){
	    	if(data["status"].code == 200){
	    		alert("Ubicacion Actualizada correctamente.");
	    	}
	    	else {
	    		alert("Error al intentar actualizar la Ubicacion");
	    	}
	    	
	        window.location.reload();
	    },
	    error: function(){
	        alert("Error al intentar actualizar la Ubicacion");
	    }
			});
		}
 };
 </script>
	</head>

<body>

		<?php
echo "<div id='user-info'>";
			//Obtengo la informacion del usuario
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

			$url = 'http://localhost/quieroservicios/servicios/v1/users.php/getUserInformation';
			$result = file_get_contents($url, false, $context);
			$json = json_decode($result, true);

			//Obtengo el listado de sexos
			$opts = array('http' =>
						    array(
						        'method'  => 'GET',
						        'header'  => 'Content-type: application/x-www-form-urlencoded',
						        'content' => ''
						    )
						);

						$context  = stream_context_create($opts);

						$url = 'http://localhost/quieroservicios/servicios/v1/sexTypes.php/getsextypes';
						$result = file_get_contents($url, false, $context);
						$jsonSex = json_decode($result, true);
			

			if($json['status']['code'] == '502')//No Data Found
			{
				echo "<div>";
				echo "<h2>Perfil del usuario</h2>";
					echo "<div id='user-info-tbl'>";
					echo "<table style='width:80%' border='1'>";
					echo "<tr>";
					echo "<th>Nombre</th>";
					echo "<th>Apellido</th>";
					echo "<th>Fecha de nacimiento</th>";
					echo "<th>Sexo</th>";
					echo "<th>Codigo de area</th>";
					echo "<th>Nro Telefono</th>";
					echo "<th>Acciones</th>";
					echo "</tr>";

					echo "<tr>";
				    echo "<th><input type='text' id='firstName'></th>";
				    echo "<th><input type='text' id='lastName'></th>";
				    echo "<th><input type='text' id='birthdate' value='01/01/2000'></th>";
				    echo "<th>";
				    	echo "<select id='sexID' name='sexID'>";
						if ($jsonSex['status']['code'] == '200')
						{
							foreach ($jsonSex["values"] as $a) 
							{
							    echo "<option value='".$a["sexID"]."'>".$a["SexName"]."</option>";
							}
						}
						else
						{
							echo "<option value='1'>'ERROR'</option>";	
						}

					echo '</select>';
					echo "</th>";
				    echo "<th><input type='number' id='areaCode'></th>";
				    echo "<th><input type='number' id='phoneNumber'></th>";
				    echo "<th><button id='1' onclick='udtuserinfo()'>Actualizar</button></th>";
					echo "</tr>";
					echo "</table>";
				echo "</div>";
			
			}
			else if ($json['status']['code'] == '200') //OK
			{
				echo "<div>";
				echo "<h2>Perfil del usuario</h2>";
					echo "<div id='user-info-tbl'>";
					echo "<table style='width:80%' border='1'>";
					echo "<tr>";
					echo "<th>Nombre</th>";
					echo "<th>Apellido</th>";
					echo "<th>Fecha de nacimiento</th>";
					echo "<th>Sexo</th>";
					echo "<th>Codigo de area</th>";
					echo "<th>Nro Telefono</th>";
					echo "<th>Acciones</th>";
					echo "</tr>";

					foreach ($json["values"] as $value)
					{
						echo "<tr>";
					    echo "<th><input type='text' id='firstName' value='".$value["firstName"]."'></th>";
					    echo "<th><input type='text' id='lastName' value='".$value["lastName"]."'></th>";
					    echo "<th><input type='text' id='birthdate' value='".$value["birthdate"]."'></th>";
					    //echo "<th><input type='text' id='sexID' value='".$value["sexID"]."'></th>";
					    echo "<th>";
					    	echo "<select id='sexID' name='sexID'>";
							if ($jsonSex['status']['code'] == '200')
							{
								foreach ($jsonSex["values"] as $a) 
								{
								    echo "<option ";
								    if ($value["sexID"] == $a["sexID"]) echo "selected='selected'";
								    echo " value='".$a["sexID"]."'>".$a["SexName"]."</option>";
								}
							}
							else
							{
								echo "<option value='1'>'ERROR'</option>";	
							}

						echo '</select>';
						echo "</th>";
					    echo "<th><input type='number' id='areaCode' value='".$value["areaCode"]."'></th>";
					    echo "<th><input type='number' id='phoneNumber' value='".$value["phoneNumber"]."'></th>";
					    echo "<th><button id='1' onclick='udtuserinfo()'>Actualizar</button></th>";
					echo "</tr>";
				}

				echo "</table>";
				echo "</div>";

			}
			else //error
			{
				echo "<h2>". ($json['status']['description']) . "</h2>";
			}
			echo "</div>";
echo "</div>";
//fin de informacion del usuario

//Inicio de informacion de cliente o profesional
echo "<div id='clipfn-info'>";

			//Obtengo los roles del usuario
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

			$url = 'http://localhost/quieroservicios/servicios/v1/roles.php/getrolesByUserID';
			$result = file_get_contents($url, false, $context);
			$urseroles = json_decode($result, true);


if ($urseroles['status']['code'] == '200')
{
	foreach ($urseroles["values"] as $role) 
	{
	    if ($role["roleID"] == 3 || $role["roleID"] == 1) 
	    	{
				//Obtengo la ubicacion del cliente
				$opts = array('http' =>
							    array(
							        'method'  => 'GET',
							        'header'  => array("Content-type: application/x-www-form-urlencoded", "Authorization: ".$_SESSION['token'].""), 
							        'content' => ''
							    )
							);

							$context  = stream_context_create($opts);

							$url = 'http://localhost/quieroservicios/servicios/v1/clients.php/getClientLocation';
							$result = file_get_contents($url, false, $context);
							$jsonClientLocation = json_decode($result, true);

				if ($jsonClientLocation['status']['code'] == '502')
				{
					//Obtengo el listado de provincias
					$opts = array('http' =>
								    array(
								        'method'  => 'GET',
								        'header'  => 'Content-type: application/x-www-form-urlencoded',
								        'content' => ''
								    )
								);

								$context  = stream_context_create($opts);

								$url = 'http://localhost/quieroservicios/servicios/v1/stateProvinces.php/getstateprovinces';
								$result = file_get_contents($url, false, $context);
								$jsonstateProvinces = json_decode($result, true);

					//Obtengo el listado de ciudades
					$opts = array('http' =>
								    array(
								        'method'  => 'GET',
								        'header'  => 'Content-type: application/x-www-form-urlencoded',
								        'content' => ''
								    )
								);
								$context  = stream_context_create($opts);

								$url = 'http://localhost/quieroservicios/servicios/v1/cities.php/getcitiesbystateproviceid/3';
								$result = file_get_contents($url, false, $context);
								$jsonCities = json_decode($result, true);



						    		echo "<h2>Ubicacion del cliente</h2>";
										echo "<div id='clientL-location-tbl'>";
										echo "<table style='width:80%' border='1'>";
											echo "<tr>";
											echo "<th>Pais</th>";
											echo "<th>Provincia</th>";
											echo "<th>Ciudad</th>";
											echo "<th>Dirección</th>";
											echo "<th>Acciones</th>";
											echo "</tr>";

											echo "<tr>";
										    echo "<th><input type='text' id='countryID' value='Argentina' value='10' readonly></th>";
					//Inicio Provincia
											echo "<th>";
												echo "<select onchange='getcitiesbystateproviceid()' id='stateProvinceID' name='stateProvinceID'>";
												if ($jsonstateProvinces['status']['code'] == '200')
												{
													foreach ($jsonstateProvinces["values"] as $a) 
													{
														echo "<option ";
													    if ($a["stateProvinceID"] == 3) echo "selected='selected'";
													    echo " value='".$a["stateProvinceID"]."'>".$a["stateProvinceName"]."</option>";
													}
												}
												else
												{
													echo "<option value=''>'ERROR'</option>";	
												}

											echo '</select>';
											echo "</th>";
					//Fin Provincia
					// Ciudad
											echo "<th>";
												echo "<select id='cityID' name='cityID'>";
												if ($jsonCities['status']['code'] == '200')
												{
													foreach ($jsonCities["values"] as $a) 
													{
														echo "<option value='".$a["cityID"]."'>".$a["cityName"]."</option>";
													}
												}
												else
												{
													echo "<option value=''>'ERROR'</option>";	
												}

											echo '</select>';
											echo "</th>";
					//Fin Ciudad
										    echo "<th><input type='text' id='Calle' value=''></th>";
										    echo "<th><button id='1' onclick='udtclientlocation()'>Actualizar</button></th>";
											echo "</tr>";

										echo "</table>";
										echo "</div>";
	    		}
	    		if ($jsonClientLocation['status']['code'] == '200')
	    		{
					//Obtengo el listado de provincias
					$opts = array('http' =>
								    array(
								        'method'  => 'GET',
								        'header'  => 'Content-type: application/x-www-form-urlencoded',
								        'content' => ''
								    )
								);

								$context  = stream_context_create($opts);

								$url = 'http://localhost/quieroservicios/servicios/v1/stateProvinces.php/getstateprovinces';
								$result = file_get_contents($url, false, $context);
								$jsonstateProvinces = json_decode($result, true);

					//Obtengo el listado de ciudades
					$opts = array('http' =>
								    array(
								        'method'  => 'GET',
								        'header'  => 'Content-type: application/x-www-form-urlencoded',
								        'content' => ''
								    )
								);
								$context  = stream_context_create($opts);

								$url = 'http://localhost/quieroservicios/servicios/v1/cities.php/getcitiesbystateproviceid/'.
										$jsonClientLocation['values']['stateProvinceID'];
								$result = file_get_contents($url, false, $context);
								$jsonCities = json_decode($result, true);



						    		echo "<h2>Ubicacion del cliente</h2>";
										echo "<div id='clientL-location-tbl'>";
										echo "<table style='width:80%' border='1'>";
											echo "<tr>";
											echo "<th>Pais</th>";
											echo "<th>Provincia</th>";
											echo "<th>Ciudad</th>";
											echo "<th>Dirección</th>";
											echo "<th>Acciones</th>";
											echo "</tr>";

											echo "<tr>";
										    echo "<th><input type='text' id='countryID' value='Argentina' value='10' readonly></th>";
					//Inicio Provincia
											echo "<th>";
												echo "<select onchange='getcitiesbystateproviceid()' id='stateProvinceID' name='stateProvinceID'>";
												if ($jsonstateProvinces['status']['code'] == '200')
												{
													foreach ($jsonstateProvinces["values"] as $a) 
													{
														echo "<option ";
													    if ($a["stateProvinceID"] == $jsonClientLocation['values']['stateProvinceID']) echo "selected='selected'";
													    echo " value='".$a["stateProvinceID"]."'>".$a["stateProvinceName"]."</option>";
													}
												}
												else
												{
													echo "<option value=''>'ERROR'</option>";	
												}

											echo '</select>';
											echo "</th>";
					//Fin Provincia
					// Ciudad
											echo "<th>";
												echo "<select id='cityID' name='cityID'>";
												if ($jsonCities['status']['code'] == '200')
												{
													foreach ($jsonCities["values"] as $a) 
													{
														echo "<option ";
													    if ($a["cityID"] == $jsonClientLocation['values']['cityID']) echo "selected='selected'";
													    echo " value='".$a["cityID"]."'>".$a["cityName"]."</option>";
													}
												}
												else
												{
													echo "<option value=''>'ERROR'</option>";	
												}

											echo '</select>';
											echo "</th>";
					//Fin Ciudad
										    echo "<th><input type='text' id='streetAddress' value='".$jsonClientLocation['values']['streetAddress']."'></th>";
										    echo "<th><button id='1' onclick='udtclientlocation()'>Actualizar</button></th>";
											echo "</tr>";

										echo "</table>";
										echo "</div>";
	    		}
	    	}
	    	if ($role["roleID"] == 4 || $role["roleID"] == 1) 
	    	{
	    		echo "<h2>Ubicacion del profesional</h2>";
	    	}
	}//Fin Foreach
} //fin if userRoles
else
{
	echo "<option value='1'>'ERROR'</option>";	
}

//Fin del informacion cliente o profesional
echo "</div>";
echo"<div><a href='http://localhost/quieroservicios/FrontEnd/index.php'><button>Volver al menu</button></a></div>"
?>
</body>
</html>