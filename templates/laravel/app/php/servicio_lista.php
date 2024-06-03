<?php
$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";
//Funcion para consultar el usuario que va a realizar la tarea
function obtenerNombreUsuario($usuario_id, $conexion) {
    $consulta_usuario = "SELECT usuario_nombre, usuario_apellido FROM usuario WHERE usuario_id = :usuario_id";
    $stmt = $conexion->prepare($consulta_usuario);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    return $usuario['usuario_nombre'] . ' ' . $usuario['usuario_apellido'];
}

$campos = "servicio.servicio_id, servicio.producto_id, servicio.tipo_servicio, servicio.fecha_servicio, servicio.observaciones, servicio.servicio_foto, servicio.usuario_asignador_id, servicio.usuario_realizador_id, servicio.estado, producto.producto_codigo, producto.producto_nombre, producto.producto_marca, producto.producto_modelo, producto.producto_foto, producto.categoria_id, producto.usuario_id, categoria.categoria_id, categoria.categoria_nombre, usuario.usuario_id, usuario.usuario_nombre, usuario.usuario_apellido";

$consulta_datos = "SELECT $campos FROM servicio 
INNER JOIN producto ON servicio.producto_id = producto.producto_id 
INNER JOIN categoria ON producto.categoria_id = categoria.categoria_id 
INNER JOIN usuario ON producto.usuario_id = usuario.usuario_id 
WHERE servicio.estado IN ('Pendiente', 'Terminado') 
ORDER BY servicio.servicio_id ASC LIMIT $inicio,$registros";

$consulta_total = "SELECT COUNT(*) FROM servicio 
WHERE estado IN ('Pendiente', 'Terminado')";

$consulta_usuario = "SELECT usuario_nombre, usuario_apellido FROM usuario WHERE usuario_id = :usuario_id";

$conexion = conexion();

$datos = $conexion->query($consulta_datos);
$datos = $datos->fetchAll();

$total = $conexion->query($consulta_total);
$total = (int) $total->fetchColumn();

$Npaginas = ceil($total / $registros);

if ($total >= 1 && $pagina <= $Npaginas) {
    $contador = $inicio + 1;
    $pag_inicio = $inicio + 1;
    foreach ($datos as $rows) {
        $tabla .= '
            <article class="media">
                <figure class="media-left">
                    <p class="image is-64x64">';
        if (is_file("./img/producto/" . $rows['producto_foto'])) {
            $tabla .= '<img src="./img/producto/' . $rows['producto_foto'] . '">';
        } else {
            $tabla .= '<img src="./img/producto.png">';
        }
        $tabla .= '</p>
                </figure>
                <div class="media-content">
                    <div class="content">
                        <p>
                        <strong>Producto:</strong> ' . $rows['producto_nombre'] . '<br>
                        <strong>Código:</strong> ' . $rows['producto_codigo'] . '<br>
                        <strong>Marca:</strong> ' . $rows['producto_marca'] . '<br>
                        <strong>Modelo:</strong> ' . $rows['producto_modelo'] . '<br>
                        <strong>Categoría:</strong> ' . $rows['categoria_nombre'] . '<br>
                        <strong>Asignado por:</strong> ' . $rows['usuario_nombre'] . ' ' . $rows['usuario_apellido'] . '<br>
                        <strong>Estado:</strong> ' . $rows['estado'] . '<br>
                        <strong>Tecnico Asignado:</strong> ' . obtenerNombreUsuario($rows['usuario_realizador_id'], $conexion) . '<br>

                        <strong>Fecha de Servicio:</strong> ' . ($rows['fecha_servicio'] ? $rows['fecha_servicio'] : 'Sin definir') . '
                        </p>
                            
                    </div>
                    <div class="has-text-right">
                        <a href="index.php?vista=product_update&product_id_up=' . $rows['producto_id'] . '" class="button is-success is-rounded is-small">Visualizar</a>
                        <a href="' . $url . $pagina . '&product_id_del=' . $rows['producto_id'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
                    </div>
                </div>
            </article>

            <hr>
        ';
        $contador++;
    }
    $pag_final = $contador - 1;
} else {
    if ($total >= 1) {
        $tabla .= '
            <p class="has-text-centered" >
                <a href="' . $url . '1" class="button is-link is-rounded is-small mt-4 mb-4">
                    Haga clic acá para recargar el listado
                </a>
            </p>
        ';
    } else {
        $tabla .= '
            <p class="has-text-centered" >No hay registros en el sistema</p>
        ';
    }
}

if ($total > 0 && $pagina <= $Npaginas) {
    $tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';
}

$conexion = null;
echo $tabla;

if ($total >= 1 && $pagina <= $Npaginas) {
    echo paginador_tablas($pagina, $Npaginas, $url, 7);
}
?>
