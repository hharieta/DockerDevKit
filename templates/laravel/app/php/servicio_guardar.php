<?php	
    require_once "../inc/session_start.php";

    require_once "main.php";
    
    //Almacenar los datos

    $producto=limpiar_cadena($_POST['producto_id']);
    $tipo=limpiar_cadena($_POST['tipo_servicio']);
    $fecha=limpiar_cadena($_POST['fecha_servicio']);
    $observaciones=limpiar_cadena($_POST['observaciones']);
    $usuario=limpiar_cadena($_POST['usuario_realizador_id']);
  


    /*== Verificando servicio==*/

        
    /*== Verificando usuario ==*/
        $check_usuario=conexion();
        $check_usuario=$check_usuario->query("SELECT usuario_id FROM usuario WHERE usuario_id='$usuario'");
        if($check_usuario->rowCount()<=0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La seleccionada no existe
                </div>
            ';
            exit();
        }
        $check_usuario=null;    

            /*== Verificando producto ==*/
        $check_producto=conexion();
        $check_producto=$check_producto->query("SELECT producto_id FROM producto WHERE producto_id='$producto'");
        if($check_producto->rowCount()<=0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La categoría seleccionada no existe
                </div> 
            ';
            exit();
    }
    $check_producto=null;

        $img_dir='../img/servicio';
    /*==Verificar si se guardo la imagen del servicio==*/
    if($_FILES['servicio_foto']['name']!="" && $_FILES['servicio_foto']['size']>0){

        /* Creando directorio de imagenes */
        if(!file_exists($img_dir)){
            if(!mkdir($img_dir,0777)){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        Error al crear el directorio de imagenes
                    </div>
                ';
                exit();
            }
        }
        $check_servicio=null;

        /* Comprobando formato de las imagenes del servicio*/
        if(mime_content_type($_FILES['servicio_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['servicio_foto']['tmp_name'])!="image/png"){
            echo '
                <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        La imagen que ha seleccionado es de un formato que no está permitido
                    </div>
                ';
                exit();
            }      

        		/* Comprobando que la imagen del servicio no supere el peso permitido */
		if(($_FILES['servicio_foto']['size']/1024)>3072){
			echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                La imagen que ha seleccionado supera el límite de peso permitido
	            </div>
	        ';
			exit();
		}

        		/* extencion de las imagenes */
		switch(mime_content_type($_FILES['servicio_foto']['tmp_name'])){
			case 'image/jpeg':
			  $img_ext=".jpg";
			break;
			case 'image/png':
			  $img_ext=".png";
			break;
		}

		/* Cambiando permisos al directorio */
		chmod($img_dir, 0777);

		/* Nombre de la imagen */
		$img_nombre=renombrar_fotos($nombre);

		/* Nombre final de la imagen */
		$foto=$img_nombre.$img_ext;

		/* Moviendo imagen al directorio */
		if(!move_uploaded_file($_FILES['servicio_foto']['tmp_name'], $img_dir.$foto)){
			echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                No podemos subir la imagen al sistema en este momento, por favor intente nuevamente
	            </div>
	        ';
			exit();
		}

	}else{
		$foto="";
	}

    /*== Guardando datos ==*/
       

    $guardar_servicio=conexion();
    $guardar_servicio=$guardar_servicio->prepare("INSERT iNTO servicio(producto_id,tipo_servicio,fecha_servicio,observaciones,servicio_foto,usuario_asignador_id,usuario_realizador_id) VALUES(:producto,:tipo,:fecha,:observaciones,:foto,:asignador,:usuario)");

    $marcadores=[
        ":producto"=>$producto,
        ":tipo"=>$tipo,
        ":fecha"=>$fecha,
        ":observaciones"=>$observaciones,
        ":foto"=>$foto,
        ":asignador"=>$_SESSION['id'],
        ":usuario"=>$usuario
    ];

    $guardar_servicio->execute($marcadores);

    if($guardar_servicio->rowCount()==1){
        echo '
            <div class="notification is-info is-light">
                <strong>¡PRODUCTO REGISTRADO!</strong><br>
                El producto se registro con exito
            </div>
        ';
    }else{

    	if(is_file($img_dir.$foto)){
			chmod($img_dir.$foto, 0777);
			unlink($img_dir.$foto);
        }

        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo registrar el producto, por favor intente nuevamente
            </div>
        ';
    }
    $guardar_servicio=null;
