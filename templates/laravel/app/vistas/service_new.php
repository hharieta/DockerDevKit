<div class="container is-fluid mb-6">
	<h1 class="title">Servicio</h1>
	<h2 class="subtitle">Nuevo Servicio</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
		include "./inc/btn_back.php";

		
		require_once "./php/main.php";
		$id = (isset($_GET['product_id_up'])) ? $_GET['product_id_up'] : 0;
		$id=limpiar_cadena($id);

		/*== Verificando producto ==*/
    	$check_producto=conexion();
    	$check_producto=$check_producto->query("SELECT * FROM producto WHERE producto_id='$id'");

        if($check_producto->rowCount()>0){
        	$datos=$check_producto->fetch();
	?>





	<form action="./php/servicio_guardar.php" method="POST" class="FormularioAjax" autocomplete="off" enctype="multipart/form-data" >
	<div class="form-rest mb-6 mt-6"></div>
	<h2 class="title has-text-centered"><?php echo $datos['producto_nombre']; ?></h2>
	<input type="hidden" name="producto_id" value="<?php echo $datos['producto_id']; ?>">

		<div class="columns">
		  	<div class="columnn">
				<div>Tipo</div>
					<div class="select is-rounded">
						<select name="tipo_servicio">
						<option value="Preventivo">Preventivo</option>
    					<option value="Correctivo">Correctivo</option>
							<option>Selecciona un servicio</option>
						</select>
					</div>
				
		  	</div>
			<div class="column">
				<label for="fecha_servicio">Selecciona una fecha:</label><br>
    			<input type="text" id="fecha_servicio" name="fecha_servicio">	
					<script>
						$(function() {
							// Inicializa el datepicker
							$("#fecha_servicio").datepicker({
								dateFormat: 'yy-mm-dd' // Formato de fecha
							});
						});
					</script>

			</div>
		</div>


		<div class="columns"> 
			<div class="column">
		    	<div class="control">
					<label>observaciones</label>
				  	<input class="input" type="text" name="observaciones" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}" maxlength="70" required >


			</div>
			
		  	</div>
		</div>

		<div class="columns">
		  	<div class="column">	
				<label>Tecnico</label><br>
		    	<div class="select is-rounded">
						<select name="usuario_realizador_id" >
							<option value="" selected="" >Seleccione un Técnico</option>
							<?php
								$usuarios=conexion();
								$usuarios=$usuarios->query("SELECT * FROM usuario");
								if($usuarios->rowCount()>0){
									$usuarios=$usuarios->fetchAll();
									foreach($usuarios as $row){
										echo '<option value="'.$row['usuario_id'].'">'.$row['usuario_nombre'].'</option>';
									}	
								}
								$usuarios=null;
							?>
						</select>
					</div>
		  	</div>
		</div>
					
		<div class="columns">
			<div class="column">
				<label>Foto o imagen del producto</label><br>
				<div class="file is-small has-name">
				  	<label class="file-label">
				    	<input class="file-input" type="file" name="servicio_foto" accept=".jpg, .png, .jpeg" >
				    	<span class="file-cta">
				      		<span class="file-label">Imagen</span>
				    	</span>
				    	<span class="file-name">JPG, JPEG, PNG. (MAX 3MB)</span>
				  	</label>
				</div>
			</div>
		</div>
						</div>
		<p class="has-text-centered">
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>

	</form>
		<?php 
		}else{
			include "./inc/error_alert.php";
		}
		$check_producto=null;
	?>
</div>
