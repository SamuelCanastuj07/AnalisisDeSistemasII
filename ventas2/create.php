<?php
include ('../app/config.php');
include ('../layout/sesion.php');
include ('../layout/parte1.php'); 

include ('../app/controllers/ventas/listado_de_ventas.php');
include('../app/controllers/almacen/listado_de_productos.php');
include('../app/controllers/clientes/listado_de_clientes.php');




?>

<?php
// Inicializaciones para evitar warnings por variables no definidas reutilizadas del flujo de ventas clásico
if (!isset($precio_subtotal)) { $precio_subtotal = 0; }
if (!isset($contador_de_carrito)) { $contador_de_carrito = 0; }
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
            <h1 class="m-0">Ventas</h1>
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
                <?php 
                    $contador_de_ventas = 0;
                    foreach($datos_ventas as $dato_ventas){
                        $contador_de_ventas = $contador_de_ventas + 1;
                    }
                ?>
                <h3 class="card-title">Venta nro  
                    <input type="text" style="text-align: center" value="<?php echo $contador_de_ventas + 1;?>" disabled> </h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                
                <b>Carrito </b>

        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-buscar-producto">
          <i class="fa fa-search"></i> Buscar Producto
        </button>

        <!-- Modal para visualizar productos desde almacen2 -->
        <div class="modal fade" id="modal-buscar-producto">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header" style="background-color: #0155b6ff; color: black;" >
                <h4 class="modal-title">Buscar Producto (Almacén Externo)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <?php
                // Cargar productos desde almacen2 view adapter
                include_once __DIR__ . '/../app/controllers/almacen2/listado_de_productos_view.php';
                // $datos_productos debe ser proporcionado por el include
                ?>

                <div class="table-responsive">
                  <table id="example_almacen2" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th><center>No</center></th>
                        <th><center>id_producto</center></th>
                        <th><center>Nombre</center></th>
                        <th><center>Precio</center></th>
                        <th><center>Stock total</center></th>
                        <th><center>Ubicaciones</center></th>
                        <th><center>Seleccionar</center></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $contador = 0;
                      foreach (($datos_productos ?? []) as $dato_productos) {
                        $id_producto = $dato_productos['id_producto'] ?? '';
                        ?>
                        <tr>
                          <td><?php echo ++$contador; ?></td>
                          <td><?php echo htmlspecialchars($dato_productos['id_producto'] ?? ''); ?></td>
                          <td><?php echo htmlspecialchars($dato_productos['nombre'] ?? ''); ?></td>
                          <td><?php echo htmlspecialchars($dato_productos['precio_venta'] ?? ''); ?></td>
                          <td><?php echo htmlspecialchars($dato_productos['stock'] ?? ''); ?></td>
                          <td>
                            <?php
                            $ubic = $dato_productos['ubicaciones'] ?? [];
                            $parts = [];
                            foreach ($ubic as $u) {
                              $uid = $u['id_ubicacion'] ?? ($u['id'] ?? '');
                              $ust = isset($u['stock']) ? $u['stock'] : (isset($u['stock_total']) ? $u['stock_total'] : '');
                              $parts[] = $uid . ':' . $ust;
                            }
                            $ubic_str = implode(', ', $parts);
                            echo htmlspecialchars($ubic_str);
                            ?>
                          </td>
                          <td>
                            <button class="btn btn-info btn-sm btn-seleccionar-externo" data-id="<?php echo htmlspecialchars($id_producto); ?>" data-nombre="<?php echo htmlspecialchars($dato_productos['nombre'] ?? ''); ?>" data-precio="<?php echo htmlspecialchars($dato_productos['precio_venta'] ?? ''); ?>" data-ubicaciones="<?php echo htmlspecialchars($ubic_str); ?>">Seleccionar</button>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>

                <div class="row mt-3">
                  <div class="col-md-3">
                    <input type="text" id="id_producto" class="form-control" hidden>
                    <label for="">Producto</label>
                    <input type="text" id="producto_v" class="form-control" disabled>
                  </div>
                  <div class="col-md-3">
                    <label for="">Detalle</label>
                    <input type="text" id="detalle_v" class="form-control" disabled>
                  </div>
                  <div class="col-md-3">
                    <label for="">Cantidad</label>
                    <input type="number" id="cantidad_v" class="form-control">
                  </div>
                  <div class="col-md-3">
                    <label for="">Precio Unitario</label>
                    <input type="text" id="precio_v" class="form-control" disabled>
                  </div>
                </div>

                <!-- hidden ubicacion value populated when selecting -->
                <input type="hidden" id="ubicacion_v" value="">

                <br>
                <button style="float: right" id="btn_registrar_carrito" class="btn btn-primary">Registrar</button>
                <div id="respuesta_carrito"></div>

              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

        <script>
        // Inicializar DataTable para el modal (evita conflicto con otros tables)
        $('#modal-buscar-producto').on('shown.bs.modal', function () {
          if (!$.fn.DataTable.isDataTable('#example_almacen2')) {
            $('#example_almacen2').DataTable({
              "pageLength": 5,
              "responsive": true, "lengthChange": true, "autoWidth": false,
              language: {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Productos",
                "lengthMenu": "Mostrar _MENU_ Productos",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {"first": "Primero","last": "Ultimo","next": "Siguiente","previous": "Anterior"}
              }
            });
          }
        });

        // Manejar selección de producto externo
        $(document).on('click', '.btn-seleccionar-externo', function(){
          var id = $(this).data('id');
          var nombre = $(this).data('nombre');
          var precio = $(this).data('precio');
          var ubicaciones = $(this).data('ubicaciones') || '';
          $('#id_producto').val(id);
          $('#producto_v').val(nombre);
          $('#detalle_v').val('');
          if (precio !== undefined && precio !== null && precio !== '') {
            $('#precio_v').val(precio);
          } else {
            $('#precio_v').val('');
          }
          $('#ubicacion_v').val(ubicaciones);
          $('#cantidad_v').focus();
         // $('#modal-buscar-producto').modal('hide');
        });

        // Reutilizar botón registrar carrito existente: envía ubicacion y agrega fila al DOM sin recargar
        $('#btn_registrar_carrito').off('click').on('click', function(){
          var nro_venta = '<?php echo $contador_de_ventas + 1;?>';
          var id_producto = $('#id_producto').val();
          var cantidad = $('#cantidad_v').val();
          var producto = $('#producto_v').val();
          var ubicacion_val = $('#ubicacion_v').val();
          var precio_val = $('#precio_v').val();
          var subtotal_val = (parseFloat(cantidad)||0) * (parseFloat(precio_val)||0);
          var nombre_env = producto; // enviar también nombre al backend

          if(id_producto == ""){
            alert("Debe seleccionar un producto");
            return;
          }
          if(cantidad == "" || isNaN(parseFloat(cantidad)) || parseFloat(cantidad) <= 0){
            alert("Debe ingresar una cantidad valida");
            return;
          }

          var url = "../app/controllers/ventas/registrar_carrito2.php";
          $.get(url, {nro_venta:nro_venta,id_producto:id_producto,cantidad:cantidad, ubicacion: ubicacion_val, nombre: nombre_env}, function(datos){
            // No mostrar la respuesta completa en el modal para permitir seguir agregando
            // Puedes registrar logs en consola si lo deseas:
            console.log('registrar_carrito2 response:', datos);

            // Agregar fila al carrito en la vista
            var success = (typeof datos === 'object' && datos && datos.success === true);
            if (!success) {
              alert('No se pudo agregar al carrito.');
              return;
            }
            var insertId = datos.id || '';
            var row = '<tr data-carrito-externo-id="' + insertId + '" data-precio="' + $('<div>').text(precio_val).html() + '" data-cantidad="' + $('<div>').text(cantidad).html() + '">' +
                      '<td><center>' + $('<div>').text(id_producto).html() + '</center></td>' +
                      '<td><center>' + $('<div>').text(producto).html() + '</center></td>' +
                      '<td><center>' + $('<div>').text(cantidad).html() + '</center></td>' +
                      '<td><center>' + $('<div>').text(precio_val).html() + '</center></td>' +
                       '<td><center>' + $('<div>').text(subtotal_val.toFixed(2)).html() + '</center></td>' +
                      '<td><center>' + $('<div>').text(ubicacion_val).html() + '</center></td>' +
                      '<td><center>' +
                        '<button type="button" class="btn btn-danger btn-sm btn-borrar-externo" data-id="' + insertId + '"><i class="fa fa-trash"></i> Eliminar</button>' +
                      '</center></td>' +
                      '</tr>';
            $('#carrito_body').append(row);

            // limpiar campos
            $('#id_producto').val('');
            $('#producto_v').val('');
            $('#detalle_v').val('');
            $('#precio_v').val('');
            $('#cantidad_v').val('');
            $('#ubicacion_v').val('');

            // Cerrar modal después de registrar exitosamente
            $('#modal-buscar-producto').modal('hide');

            // Recalcular totales
            recalcCarritoTotals();

          });
        });
        </script>
                                <br><br>
                               
                                <div class="table-responsive">
          <table id="carrito_table" class="table table-bordered table-striped table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th style="background-color: #e7e7e7;text-align: center"><center>id_producto</center></th>
                                                <th style="background-color: #e7e7e7;text-align: center"><center>Producto</center></th>
                                                <th style="background-color: #e7e7e7;text-align: center"><center>Cantidad</center></th>
                        <th style="background-color: #e7e7e7;text-align: center"><center>Precio</center></th>
                        <th style="background-color: #e7e7e7;text-align: center"><center>Precio Subtotal</center></th>
                                                <th style="background-color: #e7e7e7;text-align: center"><center>Ubicación</center></th>
                        <th style="background-color: #e7e7e7;text-align: center"><center>Acción</center></th>
                                            </tr>
                                        </thead>
                                        <tbody id="carrito_body">
                                            <?php
                                            // Renderizar elementos ya guardados en tb_carrito_externo
                                            try {
                                              $nroVentaTmp = strval(($contador_de_ventas ?? 0) + 1);
                                              // asegurar existencia de tabla para evitar error en select
                                              $pdo->exec("CREATE TABLE IF NOT EXISTS tb_carrito_externo (
                                                id_carrito_externo INT AUTO_INCREMENT PRIMARY KEY,
                                                nro_venta VARCHAR(100) DEFAULT '',
                                                external_id VARCHAR(255) DEFAULT '',
                                                nombre VARCHAR(255) DEFAULT '',
                                                cantidad DECIMAL(10,2) DEFAULT 0,
                                                ubicacion TEXT,
                                                fyh_creacion DATETIME
                                              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

                                              $stmtCE = $pdo->prepare("SELECT * FROM tb_carrito_externo WHERE nro_venta = :nv ORDER BY id_carrito_externo ASC");
                                              $stmtCE->bindParam(':nv', $nroVentaTmp);
                                              $stmtCE->execute();
                                              $rowsCE = $stmtCE->fetchAll(PDO::FETCH_ASSOC);
                                              foreach ($rowsCE as $r) {
                                                $rid = $r['id_carrito_externo'];
                                                $ext = $r['external_id'];
                                                $nom = $r['nombre'];
                                                $cant = $r['cantidad'];
                                                $ubi = $r['ubicacion'];
                                                echo '<tr data-carrito-externo-id="'.htmlspecialchars($rid).'">';
                                                echo '<td><center>'.htmlspecialchars($ext).'</center></td>';
                                                echo '<td><center>'.htmlspecialchars($nom).'</center></td>';
                                                echo '<td><center>'.htmlspecialchars($cant).'</center></td>';
                                                echo '<td><center></center></td>';
                                                echo '<td><center></center></td>';
                                                echo '<td><center>'.htmlspecialchars($ubi).'</center></td>';
                                                echo '<td><center><button type="button" class="btn btn-danger btn-sm btn-borrar-externo" data-id="'.htmlspecialchars($rid).'"><i class="fa fa-trash"></i> Eliminar</button></center></td>';
                                                echo '</tr>';
                                              }
                                            } catch (Exception $e) { /* ignorar errores de renderizado */ }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                          <tr id="carrito_totals_row">
                                            <td></td>
                                            <td style="text-align:right"><b>Total:</b></td>
                                            <td id="total_cantidad_cell"><b>0</b></td>
                                            <td id="total_precio_unit_cell"><b>0</b></td>
                                            <td id="total_subtotal_cell"><b>0</b></td>
                                            <td></td>
                                            <td></td>
                                          </tr>
                                        </tfoot>
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
              <div class="card-body">
                
                <b>Cliente </b>

                <button type="button" class="btn btn-primary" data-toggle="modal" 
                                    data-target="#modal-buscar-cliente">
                                    <i class="fa fa-search"></i>
                                    Buscar Cliente
                                </button>
                                 <!-- Modal para visualizar clientes -->
                                 <div class="modal fade" id="modal-buscar-cliente">
                                    <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #0164d6ff; color: black;" >
                                        <h4 class="modal-title">Buscar Cliente</h4>
                                        <div style="width: 10px;"></div>
                                        <button type="button" class="btn btn-warning" data-toggle="modal" 
                                    data-target="#modal-agregar-cliente">
                                    <i class="fa fa-user-plus"></i>
                                    Agregar Cliente
                                </button>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">

                                           <div class="table-responsive">
                  <table id="example3" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                        <th><center>No</center></th>
                        <th><center>Nombre del Cliente</center></th>
                        <th><center>NIT/DPI</center></th>
                        <th><center>Celular</center></th>
                        <th><center>Email</center></th>
                        <th><center>Seleccionar</center></th>
                  </tr>
                  </thead>
                   <tbody>
                      <?php
                      
                        $contador_cliente = 0;
                      foreach ($datos_clientes as $dato_clientes){ 
                        $id_cliente = $dato_clientes['id_cliente'];
                        $contador_cliente = $contador_cliente + 1;
                        ?>

                        <tr>
                            <td><center><?php echo $contador_cliente;?></center></td>
                            <td><center><?php echo $dato_clientes['nombre_cliente'];?></center></td>
                            <td><center><?php echo $dato_clientes['nit_ci_cliente'];?></center></td>
                            <td><center><?php echo $dato_clientes['celular_cliente'];?></center></td>
                            <td><center><?php echo $dato_clientes['email_cliente'];?></center></td>
                            <td><center>
                              <button id="btn_cliente_<?php echo $id_cliente; ?>" class="btn btn-info">Seleccionar</button>
                              <script>
                                $('#btn_cliente_<?php echo $id_cliente; ?>').click(function() {

                                  var id_cliente = "<?php echo $dato_clientes['id_cliente']?>";
                                  $('#id_cliente').val(id_cliente);

                                  var nombre_cliente = "<?php echo $dato_clientes['nombre_cliente']?>";
                                  $('#nombre_cliente').val(nombre_cliente);

                                  var nit_cliente = "<?php echo $dato_clientes['nit_ci_cliente']?>";
                                  $('#nit_cliente').val(nit_cliente);

                                  var celular_cliente = "<?php echo $dato_clientes['celular_cliente']?>";
                                  $('#celular_cliente').val(celular_cliente);

                                  var correo_cliente = "<?php echo $dato_clientes['email_cliente']?>";
                                  $('#correo_cliente').val(correo_cliente);

                                  $('#modal-buscar-cliente').modal('hide');
                                });
                              </script>
                            </center></td>

                        </tr>
                        
                      <?php  
                      }
                      ?>
                    </tbody>
                
                </table>
                      
                 </div>
                                
                                        </div>
                                        
                                    </div>
                                    <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <br><br>
                                <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <input type="text" id="id_cliente" hidden>
                                      <label for="">Nombre del Cliente</label>
                                      <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" disabled>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="">Nit/DPI del cliente</label>
                                      <input type="text" class="form-control" id="nit_cliente" name="nit_cliente" disabled>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="">Celular del cliente</label>
                                      <input type="text" class="form-control" id="celular_cliente" name="celular_cliente" disabled >
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="">Correo del cliente</label>
                                      <input type="text" class="form-control" id="correo_cliente" name="correo_cliente" disabled>
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
                     <div class="row">
                      <div class="col-md-6">
                         <div class="form-group">
                        <label for="">Total Pagado</label>
                        <input type="text" class="form-control" id="total_pagado" name="monto_pagado">
                        <script>
                            $('#total_pagado').keyup(function(){
                              var total_cancelar = $('#total_cancelar').val();
                              var total_pagado = $('#total_pagado').val();
                              var cambio = parseFloat(total_pagado) - parseFloat(total_cancelar);
                              $('#cambio').val(cambio);
                            });
                        </script>
                      </div>
                      </div>
                      <div class="col-md-6">
                         <div class="form-group">
                        <label for="">Cambio</label>
                        <input type="text" class="form-control" id="cambio" name="cambio" disabled>
                      </div>
                      </div>
                     </div>
                     <hr>
                        <div class="form-group">
                           <button id="btn_guardar_venta" class="btn btn-primary btn-block">Guardar Venta</button>
                            <div id="respuesta_registro_venta"></div>
                           <script>
                            $('#btn_guardar_venta').click(function(){

                              var nro_venta = '<?php echo $contador_de_ventas + 1;?>';
                              var id_cliente = $('#id_cliente').val();
                              var total_cancelar = $('#total_cancelar').val();

                              if(id_cliente == ""){
                                alert("Debe seleccionar un cliente");
                              } else {
                                guardar_venta_externas();
                              }

                              function guardar_venta_externas(){
                                var url = "../app/controllers/ventas/registro_de_ventas_externas.php";
                                var monto_total = $('#total_cancelar').val();
                                $.get(url, {nro_venta:nro_venta, id_cliente: id_cliente, monto_total:monto_total}, function(datos){
                                  $('#respuesta_registro_venta').html(datos);
                                });
                              }
                              
                            });
                           </script>
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

  // Recalcula totales del carrito (cantidad total, suma de precios unitarios, subtotal) y actualiza Monto a Cancelar
  function recalcCarritoTotals(){
    var totalCantidad = 0;
    var totalPrecioUnit = 0;
    var totalSubtotal = 0;

    $('#carrito_body tr').each(function(){
      var $tr = $(this);
      var $cells = $tr.find('td');
      // Columnas: 0:id, 1:producto, 2:cantidad, 3:precio, 4:subtotal, 5:ubicación, 6:acción
      var cantidadTxt = $cells.eq(2).text().trim();
      var precioTxt = $cells.eq(3).text().trim();
      var cantidad = parseFloat(cantidadTxt);
      var precio = parseFloat(precioTxt);

      if (isNaN(cantidad)) { cantidad = parseFloat($tr.data('cantidad')); }
      if (isNaN(precio)) { precio = parseFloat($tr.data('precio')); }
      if (isNaN(cantidad)) { cantidad = 0; }
      if (isNaN(precio)) { precio = 0; }

      var subtotal = cantidad * precio;
      // Escribir subtotal en su celda si existe esa columna
      if ($cells.length > 4) {
        $cells.eq(4).text(subtotal.toFixed(2));
      }

      totalCantidad += cantidad;
      totalPrecioUnit += precio;
      totalSubtotal += subtotal;
    });

    $('#total_cantidad_cell').html('<b>' + totalCantidad + '</b>');
    $('#total_precio_unit_cell').html('<b>' + totalPrecioUnit.toFixed(2) + '</b>');
    $('#total_subtotal_cell').html('<b>' + totalSubtotal.toFixed(2) + '</b>');

    // Actualizar Monto a Cancelar
    $('#total_cancelar').val(totalSubtotal.toFixed(2));

    // Recalcular cambio si ya hay pago
    var total_pagado = parseFloat($('#total_pagado').val());
    if (!isNaN(total_pagado)) {
      var cambio = total_pagado - totalSubtotal;
      $('#cambio').val(cambio.toFixed(2));
    }
  }

  // Recalcular al cargar la página por si ya hay filas
  $(document).ready(function(){
    recalcCarritoTotals();
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

  // Delegado para eliminar ítems del carrito externo
  $(document).on('click', '.btn-borrar-externo', function(){
    var btn = $(this);
    var id = btn.data('id') || btn.closest('tr').data('carrito-externo-id');
    if(!id){
      alert('No se encontró el identificador del carrito externo.');
      return;
    }
    if(!confirm('¿Eliminar este producto del carrito?')) return;

    $.ajax({
      url: '../app/controllers/ventas/borrar_carrito_externo.php',
      method: 'POST',
      data: { id_carrito_externo: id },
      headers: { 'X-Requested-With':'XMLHttpRequest' }
    }).done(function(resp){
      try { if (typeof resp === 'string') resp = JSON.parse(resp); } catch(e) { /* ignore */ }
      if(resp && resp.success){
        btn.closest('tr').remove();
        recalcCarritoTotals();
      } else {
        // Fallback: redirigir por GET si el backend no devolvió JSON success
        window.location.href = '../app/controllers/ventas/borrar_carrito_externo.php?id_carrito_externo=' + encodeURIComponent(id);
      }
    }).fail(function(){
      // Fallback en error de red o AJAX
      window.location.href = '../app/controllers/ventas/borrar_carrito_externo.php?id_carrito_externo=' + encodeURIComponent(id);
    });
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
                                     
                                


