<?php
$id_venta_get = $_GET['id_venta'];

include ('../app/config.php');
include ('../layout/sesion.php');
include ('../layout/parte1.php'); 

include ('../app/controllers/ventas/cargar_venta.php');
include ('../app/controllers/clientes/cargar_cliente.php');





?>

<?php

if(isset($_SESSION['mensaje6'])) {
    $respuesta = $_SESSION['mensaje6']; ?>
    <script>
        Swal.fire({
         title: "JEY GT",
         text: "Producto no registrado en el carrito",
        icon: "error"
   });
</script>

<?php
  unset($_SESSION['mensaje6']);

}

?>


<?php

if(isset($_SESSION['mensaje7'])) {
    $respuesta = $_SESSION['mensaje7']; ?>
    <script>
        Swal.fire({
         title: "JEY GT",
         text: "Venta no registrada",
        icon: "error"
   });
</script>

<?php
  unset($_SESSION['mensaje7']);

}

?>


<?php

if(isset($_SESSION['mensaje1'])) {
    $respuesta = $_SESSION['mensaje1']; ?>
    <script>
        Swal.fire({
        position: "top-center",
        icon: "success",
        title: "Gracias por su compra",
        showConfirmButton: false,
        timer: 2000
   });
</script>

<?php
  unset($_SESSION['mensaje1']);

}

?>



 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0">Venta Nro <?php echo $nro_venta;?></h1>
          </div><!-- /.col -->
         
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">                  
        
      <div class="row">
          <div class="col-md-12">
            <div class="card card-outline card-primary">
              <div class="card-header">
                
                <h3 class="card-title">Detalle de la venta</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th style="background-color: #e7e7e7;text-align: center"><center>Nro</center></th>
                                        <th style="background-color: #e7e7e7;text-align: center"><center>Producto</center></th>
                                        <th style="background-color: #e7e7e7;text-align: center"><center>Detalle</center></th>
                                        <th style="background-color: #e7e7e7;text-align: center"><center>Cantidad</center></th>
                                        <th style="background-color: #e7e7e7;text-align: center"><center>Precio Unitario</center></th>
                                        <th style="background-color: #e7e7e7;text-align: center"><center>Precio Subtotal</center></th>
                                        
                                    </tr>
                                </thead>
                               
                                <tbody>

                                    <?php 
                                      $contador_de_carrito = 0;
                                     
                                      $cantidad_total = 0;
                                      $precio_unitario_total = 0;
                                      $precio_subtotal = 0;
                                      
                                      $sql_carrito = "SELECT *, pro.nombre as nombre_producto, pro.descripcion as descripcion, pro.precio_venta as precio_venta, pro.stock as stock, pro.id_producto as id_producto FROM tb_carrito AS carr INNER JOIN tb_almacen as pro ON carr.id_producto = pro.id_producto WHERE nro_venta = '$nro_venta' ORDER BY id_carrito DESC";
                                      $query_carrito = $pdo->prepare($sql_carrito);
                                      $query_carrito->execute();
                                      $datos_carrito = $query_carrito->fetchAll(PDO::FETCH_ASSOC);
                                      foreach($datos_carrito as $dato_carrito){
                                        $id_carrito = $dato_carrito['id_carrito'];
                                        $contador_de_carrito = $contador_de_carrito + 1; 
                                        $cantidad_total = $cantidad_total + $dato_carrito['cantidad'];
                                        $precio_unitario_total = $precio_unitario_total + $dato_carrito['precio_venta'];
                                        $precio_subtotal = $precio_subtotal + ($dato_carrito['cantidad'] * $dato_carrito['precio_venta']);
                                        ?>
                                        
                                      
                                        <tr>
                                          <td>
                                            <center><?php echo $contador_de_carrito; ?></center>
                                            <input type="text" id="id_producto<?php echo $contador_de_carrito; ?>" value="<?php echo $dato_carrito['id_producto'];?>" hidden>
                                          </td>
                                          <td><center><?php echo $dato_carrito['nombre_producto']; ?></center></td>
                                          <td><center><?php echo $dato_carrito['descripcion']; ?></center></td>
                                          <td><center><span id="cantidad_carrito<?php echo $contador_de_carrito; ?>"><?php echo $dato_carrito['cantidad']; ?></span></center>
                                            <input type="text" value="<?php echo $dato_carrito['stock']; ?>" id="stock_inventario<?php echo $contador_de_carrito; ?>" hidden>
                                        </td>
                                          <td><center><?php echo $dato_carrito['precio_venta']; ?></center></td>
                                          <td>
                                            <center>
                                              <?php 
                                                $cantidad = floatval($dato_carrito['cantidad']);
                                                $precio_venta = floatval($dato_carrito['precio_venta']);
                                               echo $subtotal = $cantidad * $precio_venta;
                                               
                                              ?>
                                            </center>
                                          </td>
                                          
                                        </tr>
                                          
                                    <?php  
                                      }
                                    ?>

                                    <tr>
                                        <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                                        <th>
                                          <center>
                                            <?php echo $cantidad_total; ?>
                                          </center>
                                        </th>
                                        <th>
                                          <center>
                                            <?php echo $precio_unitario_total; ?>
                                          </center>
                                        </th>
                                        <th>
                                          <center>
                                            <?php echo $precio_subtotal; ?>
                                          </center>
                                        </th>
                                    </tr>

                                </tbody>

                               </table>
                                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>

        <div class="row">
          <div class="col-md-9">
              <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Datos del Cliente </h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->

              <?php 
                foreach($datos_clientes as $dato_cliente){
                    $nombre_cliente = $dato_cliente['nombre_cliente'];
                    $nit_ci_cliente = $dato_cliente['nit_ci_cliente'];
                    $celular_cliente = $dato_cliente['celular_cliente'];
                    $email_cliente = $dato_cliente['email_cliente'];
                    $id_cliente = $dato_cliente['id_cliente'];
                }


              ?>
              <div class="card-body">
                                <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <input type="text" id="id_cliente" hidden>
                                      <label for="">Nombre del Cliente</label>
                                      <input type="text" class="form-control" value="<?php echo $nombre_cliente; ?>" id="nombre_cliente" name="nombre_cliente" disabled>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="">Nit/DPI del cliente</label>
                                      <input type="text" class="form-control" value="<?php echo $nit_ci_cliente; ?>" id="nit_cliente" name="nit_cliente" disabled>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="">Celular del cliente</label>
                                      <input type="text" class="form-control" value="<?php echo $celular_cliente; ?>" id="celular_cliente" name="celular_cliente" disabled >
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="">Correo del cliente</label>
                                      <input type="text" class="form-control" value="<?php echo $email_cliente; ?>" id="correo_cliente" name="correo_cliente" disabled>
                                    </div>
                                  </div>
                                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-3">
              <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Registrar Venta </h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                
                      <div class="form-group">
                        <label for="">Monto a Cancelar</label>
                        <input type="text" class="form-control"  id="total_cancelar" name="monto_total" value="<?php echo $precio_subtotal; ?>" disabled >
                        
                      </div>
                     
                     
                        
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
                     


        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php include ('../layout/parte2.php'); ?>



<script>
  $(function () {
    $("#example1").DataTable({
        "pageLength": 5,
          language: {
              "emptyTable": "No hay información",
              "decimal": "",
              "info": "Mostrando _START_ a _END_ de _TOTAL_ Prodcutos",
              "infoEmpty": "Mostrando 0 to 0 of 0 Productos",
              "infoFiltered": "(Filtrado de _MAX_ total Productos)",
              "infoPostFix": "",
              "thousands": ",",
              "lengthMenu": "Mostrar _MENU_ Productos",
              "loadingRecords": "Cargando...",
              "processing": "Procesando...",
              "search": "Buscador:",
              "zeroRecords": "Sin resultados encontrados",
              "paginate": {
                  "first": "Primero",
                  "last": "Ultimo",
                  "next": "Siguiente",
                  "previous": "Anterior"
              }
             },
      "responsive": true, "lengthChange": true, "autoWidth": false,
       
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });




  $(function () {
    $("#example2").DataTable({
        "pageLength": 5,
          language: {
              "emptyTable": "No hay información",
              "decimal": "",
              "info": "Mostrando _START_ a _END_ de _TOTAL_ Proveedores",
              "infoEmpty": "Mostrando 0 to 0 of 0 Proveedores",
              "infoFiltered": "(Filtrado de _MAX_ total Proveedores)",
              "infoPostFix": "",
              "thousands": ",",
              "lengthMenu": "Mostrar _MENU_ Proveedores",
              "loadingRecords": "Cargando...",
              "processing": "Procesando...",
              "search": "Buscador:",
              "zeroRecords": "Sin resultados encontrados",
              "paginate": {
                  "first": "Primero",
                  "last": "Ultimo",
                  "next": "Siguiente",
                  "previous": "Anterior"
              }
             },
      "responsive": true, "lengthChange": true, "autoWidth": false,
       
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });


   $(function () {
    $("#example3").DataTable({
        "pageLength": 5,
          language: {
              "emptyTable": "No hay información",
              "decimal": "",
              "info": "Mostrando _START_ a _END_ de _TOTAL_ Clientes",
              "infoEmpty": "Mostrando 0 to 0 of 0 Clientes",
              "infoFiltered": "(Filtrado de _MAX_ total Clientes)",
              "infoPostFix": "",
              "thousands": ",",
              "lengthMenu": "Mostrar _MENU_ Clientes",
              "loadingRecords": "Cargando...",
              "processing": "Procesando...",
              "search": "Buscador:",
              "zeroRecords": "Sin resultados encontrados",
              "paginate": {
                  "first": "Primero",
                  "last": "Ultimo",
                  "next": "Siguiente",
                  "previous": "Anterior"
              }
             },
      "responsive": true, "lengthChange": true, "autoWidth": false,
       
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>


 <!-- Modal para Crear clientes -->
                                 <div class="modal fade" id="modal-agregar-cliente">
                                    <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #FDD600; color: black;" >
                                        <h4 class="modal-title">Nuevo Cliente</h4>
                                        <div style="width: 10px;"></div>
                                        
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">

                                           <form action="../app/controllers/clientes/guardar_clientes.php" method="post">
                                            <div class="form-group">
                                              <label for="">Nombre del Cliente</label>
                                              <input type="text" class="form-control" name="nombre_cliente" required>
                                            </div>
                                            <div class="form-group">
                                              <label for="">NIT/DPI del Cliente</label>
                                              <input type="text" class="form-control" name="nit_ci_cliente" required>
                                            </div>
                                            <div class="form-group">
                                              <label for="">Celular del Cliente</label>
                                              <input type="text" class="form-control" name="celular_cliente" required>
                                            </div>
                                            <div class="form-group">
                                              <label for="">Correo del Cliente</label>
                                              <input type="email" class="form-control" name="email_cliente" required>
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                              <button type="submit" class="btn btn-warning btn-block">Guardar Cliente</button>
                                            </div>
                                           </form>
                                
                                        </div>
                                        
                                    </div>
                                    <!-- /.modal-content -->

                                    </div>
                                    <!-- /.modal-dialog -->
                                     
                                


