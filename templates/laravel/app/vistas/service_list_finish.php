<div class="container is-fluid m6">
    <h1 class="title">Servicio a Dispositivos</h1>
    <h2 class="subtitle">Lista de Servicios Terminados</h2>

</div>
<div class="container is-fluid m4">
    <a href="../php/generar_reporte.php" class="button is-primary is-large">Generar Reporte en PDF</a>
    </div>
    <div class="container pb-4 pt4">
        <?php
        require_once "./php/main.php";
        // Aquí puedes agregar el código para mostrar la lista de servicios terminados
        ?>
    </div>

<div class="container pb-6 pt6">
    <?php
    
       require_once "./php/main.php";

       # Eliminar producto #
       if(isset($_GET['product_id_del'])){
           require_once "./php/producto_eliminar.php";
       }

       if(!isset($_GET['page'])){
           $pagina=1;
       }else{
           $pagina=(int) $_GET['page'];
           if($pagina<=1){
               $pagina=1;
           }
       }

       $categoria_id = (isset($_GET['category_id'])) ? $_GET['category_id'] : 0;

       $pagina=limpiar_cadena($pagina);
       $url="index.php?vista=product_list&page="; /* <== */
       $registros=15;
       $busqueda="";

       # Paginador producto #
       require_once "./php/servicio_lista_terminado.php";
    ?>
</div>