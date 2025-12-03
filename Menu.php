<?php
  include ('Config/conexion.php');
  include "Config/Header.php";

$fecha =  getdate();
$hora = date("Y-m-d");

?>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
    moment.locale('es');
    var upDate = function() {
      var elFecha = document.querySelector("#fecha");
      var elHora = document.querySelector("#hora");
      var nowDate = moment(new Date());
      elHora.textContent = nowDate.format('HH:mm:ss');
      elFecha.textContent =
      nowDate.format('dddd DD [de] MMMM [de] YYYY ');
    }m     kkkkkkk,l.
    setInterval(upDate, 4000);
});
</script>
<div class="container-fluid">
    <div style="text-align: right;">
        <div class="nowDateTime">
			<p>
			    <span style="color: orange;" id="fecha"></span><br/>
			    <span id="hora"></span>
			</p>
		</div>
	</div>
    <h1 class="h3 mb-4 text-gray-800"></h1>
		<div class="row">
			<div class="column">
				<div class="card">
					<h3>POLIGONO A</h3>
						<div class="table">
							<div class="Row">
        						<div class="Cell">
        							<p style="color: darkorange;">EN SITIO:</p> 
								</div>
        						<div class="Cell">
        							<p style="color: green;">INGRESOS:</p>
								</div>
        						<div class="Cell">
        							<p style="color: red;">EGRESOS:</p>
        						</div>
							</div>
						   <div class="Row">
        						<div class="Cell">
							    	<img src="img/Personal.png" width="50%">
							      	<?php 
		             				$sql = "select COUNT(*) as PolA from regentsalper where IdUbicacion=1 and FolMovSal =0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: darkorange;'>".$PolA=$row['PolA']."</p>";
												 endwhile; 
									?> 
								</div>
								<div class="Cell">
						      		<img src="img/Personal.png" width="50%">
							      	<?php 
		             				$sql = "select COUNT(*) as PolA from regentsalper where IdUbicacion=1";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: green;'>".$PolA=$row['PolA']."</p>";
												 endwhile; 
									?> 
								</div>
								<div class="Cell">
						      		<img src="img/Personal.png" width="40%">
							      	<?php 
		             				$sql = "select COUNT(*) as PolA from regentsalper where IdUbicacion=1 and FolMovSal <>0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: red;'>".$PolA=$row['PolA']."</p>";
												 endwhile; 
									?> 
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Vehiculos.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as PolA from regentsalveh where IdUbicacion=1 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$PolA=$row['PolA']."</p>";
													 endwhile; 
									?>
						    	</div>
						    	<div class="Cell">
									<img src="img/Vehiculos.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolA from regentsalveh where IdUbicacion=1";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$PolA=$row['PolA']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Vehiculos.png" width="40%">
									<?php 
		             				$sql = "select COUNT(*) as PolA from regentsalveh where IdUbicacion=1 and FolMovSal <>0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: red;'>".$PolA=$row['PolA']."</p>";
												 endwhile; 
									?>
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Visitantes.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as PolA from regentsalvis where IdUbicacion=1 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$PolA=$row['PolA']."</p>";
													 endwhile; 
									?>
								</div>
								<div class="Cell">
									<img src="img/Visitantes.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolA from regentsalvis where IdUbicacion=1";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$PolA=$row['PolA']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Visitantes.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolA from regentsalvis where IdUbicacion=1 and FolMovSal <>0";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: red;'>".$PolA=$row['PolA']."</p>";
														 endwhile; 
										?>
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Maniobras.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as PolA from regentsalman where IdUbicacion=1 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$PolA=$row['PolA']."</p>";
													 endwhile; 
									?>
								</div>
								<div class="Cell">
									<img src="img/Maniobras.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolA from regentsalman where IdUbicacion=1";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$PolA=$row['PolA']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Maniobras.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolA from regentsalman where IdUbicacion=1 and FolMovSal <>0";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: red;'>".$PolA=$row['PolA']."</p>";
														 endwhile; 
										?>
								</div>
							</div>
						</div>
				</div>
			</div>
			<div class="column">
				<div class="card">
					<h3>POLIGONO B</h3>
						<div class="table">
							<div class="Row">
        						<div class="Cell">
        							<p style="color: darkorange;">EN SITIO:</p> 
								</div>
        						<div class="Cell">
        							<p style="color: green;">INGRESOS:</p>
								</div>
        						<div class="Cell">
        							<p style="color: red;">EGRESOS:</p>
        						</div>
							</div>
						   <div class="Row">
        						<div class="Cell">
							    	<img src="img/Personal.png" width="50%">
							      	<?php 
		             				$sql = "select COUNT(*) as PolB from regentsalper where IdUbicacion=2 and FolMovSal =0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: darkorange;'>".$PolB=$row['PolB']."</p>";
												 endwhile; 
									?> 
								</div>
								<div class="Cell">
						      		<img src="img/Personal.png" width="50%">
							      	<?php 
		             				$sql = "select COUNT(*) as PolB from regentsalper where IdUbicacion=2";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: green;'>".$PolB=$row['PolB']."</p>";
												 endwhile; 
									?> 
								</div>
								<div class="Cell">
						      		<img src="img/Personal.png" width="40%">
							      	<?php 
		             				$sql = "select COUNT(*) as PolB from regentsalper where IdUbicacion=2 and FolMovSal <>0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: red;'>".$PolB=$row['PolB']."</p>";
												 endwhile; 
									?> 
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Vehiculos.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as PolB from regentsalveh where IdUbicacion=2 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$PolB=$row['PolB']."</p>";
													 endwhile; 
									?>
						    	</div>
						    	<div class="Cell">
									<img src="img/Vehiculos.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolB from regentsalveh where IdUbicacion=2";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$PolB=$row['PolB']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Vehiculos.png" width="40%">
									<?php 
		             				$sql = "select COUNT(*) as PolB from regentsalveh where IdUbicacion=2 and FolMovSal <>0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: red;'>".$PolB=$row['PolB']."</p>";
												 endwhile; 
									?>
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Visitantes.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as PolB from regentsalvis where IdUbicacion=2 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$PolB=$row['PolB']."</p>";
													 endwhile; 
									?>
								</div>
								<div class="Cell">
									<img src="img/Visitantes.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolB from regentsalvis where IdUbicacion=2";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$PolB=$row['PolB']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Visitantes.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolB from regentsalvis where IdUbicacion=2 and FolMovSal <>0";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: red;'>".$PolB=$row['PolB']."</p>";
														 endwhile; 
										?>
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Maniobras.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as PolB from regentsalman where IdUbicacion=2 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$PolB=$row['PolB']."</p>";
													 endwhile; 
									?>
								</div>
								<div class="Cell">
									<img src="img/Maniobras.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolB from regentsalman where IdUbicacion=2";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$PolB=$row['PolB']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Maniobras.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolB from regentsalman where IdUbicacion=2 and FolMovSal <>0";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: red;'>".$PolB=$row['PolB']."</p>";
														 endwhile; 
										?>
								</div>
							</div>
						</div>
				</div>
			</div>
			<div class="column">
				<div class="card">
					<h3>POLIGONO D</h3>
						<div class="table">
							<div class="Row">
        						<div class="Cell">
        							<p style="color: darkorange;">EN SITIO:</p> 
								</div>
        						<div class="Cell">
        							<p style="color: green;">INGRESOS:</p>
								</div>
        						<div class="Cell">
        							<p style="color: red;">EGRESOS:</p>
        						</div>
							</div>
						   <div class="Row">
        						<div class="Cell">
							    	<img src="img/Personal.png" width="50%">
							      	<?php 
		             				$sql = "select COUNT(*) as PolD from regentsalper where IdUbicacion=9 and FolMovSal =0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: darkorange;'>".$PolD=$row['PolD']."</p>";
												 endwhile; 
									?> 
								</div>
								<div class="Cell">
						      		<img src="img/Personal.png" width="50%">
							      	<?php 
		             				$sql = "select COUNT(*) as PolD from regentsalper where IdUbicacion=9";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: green;'>".$PolD=$row['PolD']."</p>";
												 endwhile; 
									?> 
								</div>
								<div class="Cell">
						      		<img src="img/Personal.png" width="40%">
							      	<?php 
		             				$sql = "select COUNT(*) as PolD from regentsalper where IdUbicacion=9 and FolMovSal <>0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: red;'>".$PolD=$row['PolD']."</p>";
												 endwhile; 
									?> 
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Vehiculos.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as PolD from regentsalveh where IdUbicacion=9 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$PolD=$row['PolD']."</p>";
													 endwhile; 
									?>
						    	</div>
						    	<div class="Cell">
									<img src="img/Vehiculos.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolD from regentsalveh where IdUbicacion=9";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$PolD=$row['PolD']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Vehiculos.png" width="40%">
									<?php 
		             				$sql = "select COUNT(*) as PolD from regentsalveh where IdUbicacion=9 and FolMovSal <>0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: red;'>".$PolD=$row['PolD']."</p>";
												 endwhile; 
									?>
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Visitantes.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as PolD from regentsalvis where IdUbicacion=9 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$PolD=$row['PolD']."</p>";
													 endwhile; 
									?>
								</div>
								<div class="Cell">
									<img src="img/Visitantes.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolD from regentsalvis where IdUbicacion=9";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$PolD=$row['PolD']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Visitantes.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolD from regentsalvis where IdUbicacion=9 and FolMovSal <>0";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: red;'>".$PolD=$row['PolD']."</p>";
														 endwhile; 
										?>
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Maniobras.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as PolD from regentsalman where IdUbicacion=9 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$PolD=$row['PolD']."</p>";
													 endwhile; 
									?>
								</div>
								<div class="Cell">
									<img src="img/Maniobras.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolD from regentsalman where IdUbicacion=9";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$PolD=$row['PolD']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Maniobras.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as PolD from regentsalman where IdUbicacion=9 and FolMovSal <>0";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: red;'>".$PolD=$row['PolD']."</p>";
														 endwhile; 
										?>
								</div>
							</div>
						</div>
				</div>
			</div>	  
			<div class="column">
				<div class="card">
					<h3>CEBU</h3>
						<div class="table">
							<div class="Row">
        						<div class="Cell">
        							<p style="color: darkorange;">EN SITIO:</p> 
								</div>
        						<div class="Cell">
        							<p style="color: green;">INGRESOS:</p>
								</div>
        						<div class="Cell">
        							<p style="color: red;">EGRESOS:</p>
        						</div>
							</div>
						   <div class="Row">
        						<div class="Cell">
							    	<img src="img/Personal.png" width="50%">
							      	<?php 
		             				$sql = "select COUNT(*) as CEBU from regentsalper where IdUbicacion=3 and FolMovSal =0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: darkorange;'>".$CEBU=$row['CEBU']."</p>";
												 endwhile; 
									?> 
								</div>
								<div class="Cell">
						      		<img src="img/Personal.png" width="50%">
							      	<?php 
		             				$sql = "select COUNT(*) as CEBU from regentsalper where IdUbicacion=3";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: green;'>".$CEBU=$row['CEBU']."</p>";
												 endwhile; 
									?> 
								</div>
								<div class="Cell">
						      		<img src="img/Personal.png" width="40%">
							      	<?php 
		             				$sql = "select COUNT(*) as CEBU from regentsalper where IdUbicacion=3 and FolMovSal <>0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: red;'>".$CEBU=$row['CEBU']."</p>";
												 endwhile; 
									?> 
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Vehiculos.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as CEBU from regentsalveh where IdUbicacion=3 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$CEBU=$row['CEBU']."</p>";
													 endwhile; 
									?>
						    	</div>
						    	<div class="Cell">
									<img src="img/Vehiculos.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as CEBU from regentsalveh where IdUbicacion=3";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$CEBU=$row['CEBU']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Vehiculos.png" width="40%">
									<?php 
		             				$sql = "select COUNT(*) as CEBU from regentsalveh where IdUbicacion=3 and FolMovSal <>0";
								       $result = mysqli_query($mysqli,$sql);
								        while($row = mysqli_fetch_assoc($result)):
								             echo "<p style='color: red;'>".$CEBU=$row['CEBU']."</p>";
												 endwhile; 
									?>
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Visitantes.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as CEBU from regentsalvis where IdUbicacion=3 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$CEBU=$row['CEBU']."</p>";
													 endwhile; 
									?>
								</div>
								<div class="Cell">
									<img src="img/Visitantes.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as CEBU from regentsalvis where IdUbicacion=3";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$CEBU=$row['CEBU']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Visitantes.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as CEBU from regentsalvis where IdUbicacion=3 and FolMovSal <>0";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: red;'>".$CEBU=$row['CEBU']."</p>";
														 endwhile; 
										?>
								</div>
							</div>

							<div class="Row">
        						<div class="Cell">
							    	<img src="img/Maniobras.png" width="40%">
									<?php 
			             				$sql = "select COUNT(*) as CEBU from regentsalman where IdUbicacion=3 and FolMovSal =0";
									       $result = mysqli_query($mysqli,$sql);
									        while($row = mysqli_fetch_assoc($result)):
									             echo "<p style='color: darkorange;'>".$CEBU=$row['CEBU']."</p>";
													 endwhile; 
									?>
								</div>
								<div class="Cell">
									<img src="img/Maniobras.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as CEBU from regentsalman where IdUbicacion=3";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: green;'>".$CEBU=$row['CEBU']."</p>";
														 endwhile; 
										?>
								</div>
								<div class="Cell">
									<img src="img/Maniobras.png" width="40%">
										<?php 
				             				$sql = "select COUNT(*) as CEBU from regentsalman where IdUbicacion=3 and FolMovSal <>0";
										       $result = mysqli_query($mysqli,$sql);
										        while($row = mysqli_fetch_assoc($result)):
										             echo "<p style='color: red;'>".$CEBU=$row['CEBU']."</p>";
														 endwhile; 
										?>
								</div>
							</div>
						</div>
				</div>
			</div>		 
		</div>
</div>
<?php
include "Config/Footer.php";
?>
<style type="text/css">
    .Table
    {
        display: table;
    }
    .Row
    {
        display: table-row;
    }
    .Cell
    {
        display: table-cell;
    
</style>