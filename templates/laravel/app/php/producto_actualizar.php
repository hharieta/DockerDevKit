<?php
	require_once "main.php";

	/*== Almacenando id ==*/
    $id=limpiar_cadena($_POST['producto_id']);


    /*== Verificando producto ==*/
	$conexion=conexion();
	$check_producto=$conexion->query("SELECT * FROM producto WHERE producto_id='$id'");
    $check_estado=$conexion->query("SELECT estado FROM servicio WHERE producto_id='$id'");

    if($check_producto->rowCount()<=0){
    	echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El producto no existe en el sistema
            </div>
        ';
        exit();
    }else{
    	$datos=$check_producto->fetch();
        $datos_estado=$check_estado->fetch()['estado'];
    }
    $check_producto=null;
    $check_estado=null;


    /*== Almacenando datos ==*/
    $codigo=limpiar_cadena($_POST['producto_codigo']);
	$nombre=limpiar_cadena($_POST['producto_nombre']);

	$marca=limpiar_cadena($_POST['producto_marca']);
	$modelo=limpiar_cadena($_POST['producto_modelo']);
	$categoria=limpiar_cadena($_POST['producto_categoria']);
    $estado=limpiar_cadena($_POST['estado']);


	/*== Verificando campos obligatorios ==*/
    if($codigo=="" || $nombre=="" || $marca=="" || $modelo=="" || $categoria=="" || $estado==""){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }


    /*== Verificando integridad de los datos ==*/
    if(verificar_datos("[a-zA-Z0-9- ]{1,70}",$codigo)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El CODIGO de BARRAS no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}",$nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-Z0-9]{1,25}",$marca)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El PRECIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-Z0-9\s-]{1,25}",$modelo)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El STOCK no coincide con el formato solicitado
            </div>
        ';
        exit();
    }


    /*== Verificando codigo ==*/
    if($codigo!=$datos['producto_codigo']){
	    $check_codigo=conexion();
	    $check_codigo=$check_codigo->query("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
	    if($check_codigo->rowCount()>0){
	        echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                El CODIGO de BARRAS ingresado ya se encuentra registrado, por favor elija otro
	            </div>
	        ';
	        exit();
	    }
	    $check_codigo=null;
    }


    /*== Verificando nombre ==*/
    if($nombre!=$datos['producto_nombre']){
	    $check_nombre=conexion();
	    $check_nombre=$check_nombre->query("SELECT producto_nombre FROM producto WHERE producto_nombre='$nombre'");
	    if($check_nombre->rowCount()>0){
	        echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                El NOMBRE ingresado ya se encuentra registrado, por favor elija otro
	            </div>
	        ';
	        exit();
	    }
	    $check_nombre=null;
    }


    /*== Verificando categoria ==*/
    if($categoria!=$datos['categoria_id']){
	    $check_categoria=conexion();
	    $check_categoria=$check_categoria->query("SELECT categoria_id FROM categoria WHERE categoria_id='$categoria'");
	    if($check_categoria->rowCount()<=0){
	        echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                La categoría seleccionada no existe
	            </div>
	        ';
	        exit();
	    }
	    $check_categoria=null;
    }


    /*== Actualizando datos ==*/
    // $actualizar_producto=conexion();
    // $actualizar_producto=$actualizar_producto->prepare("UPDATE producto SET producto_codigo=:codigo,producto_nombre=:nombre,producto_marca=:marca,producto_modelo=:modelo,categoria_id=:categoria WHERE producto_id=:id");

    // $marcadores=[
    //     ":codigo"=>$codigo,
    //     ":nombre"=>$nombre,
    //     ":marca"=>$marca,
    //     ":modelo"=>$modelo,
    //     ":categoria"=>$categoria,
    //     ":id"=>$id
    // ];


    // if($actualizar_producto->execute($marcadores)){
    //     echo '
    //         <div class="notification is-info is-light">
    //             <strong>¡PRODUCTO ACTUALIZADO!</strong><br>
    //             El producto se actualizo con exito
    //         </div>
    //     ';
    // }else{
    //     echo '
    //         <div class="notification is-danger is-light">
    //             <strong>¡Ocurrio un error inesperado!</strong><br>
    //             No se pudo actualizar el producto, por favor intente nuevamente
    //         </div>
    //     ';
    // }
    // $actualizar_producto=null;

    try {
        $conexion = conexion();
        $conexion->beginTransaction();

        // Actualizar producto
        $actualizar_producto = $conexion->prepare("UPDATE producto SET producto_codigo=:codigo, producto_nombre=:nombre, producto_marca=:marca, producto_modelo=:modelo, categoria_id=:categoria WHERE producto_id=:id");

        $marcadores_producto = [
            ":codigo" => $codigo,
            ":nombre" => $nombre,
            ":marca" => $marca,
            ":modelo" => $modelo,
            ":categoria" => $categoria,
            ":id" => $id
        ];

        $actualizar_producto->execute($marcadores_producto);

        // Actualizar estado del servicio
        $actualizar_servicio = $conexion->prepare("UPDATE servicio SET estado=:estado WHERE producto_id=:id");

        $marcadores_servicio = [
            ":estado" => $estado,
            ":id" => $id
        ];

        $actualizar_servicio->execute($marcadores_servicio);

        $conexion->commit();
        echo "Producto y estado del servicio actualizados correctamente.";
    } catch (PDOException $e) {
        $conexion->rollBack();
        echo 'Error: ' . $e->getMessage();
    }