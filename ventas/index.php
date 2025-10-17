<?php
include ('../app/config.php');
include ('../layout/sesion.php');
include ('../layout/parte1.php'); 
include('../app/controllers/ventas/listado_de_ventas.php');
include('../app/controllers/ventas/listado_de_ventas_externas.php');


if(isset($_SESSION['mensaje1'])) {
    $respuesta = $_SESSION['mensaje1']; ?>
    <script>
        Swal.fire({
        position: "top-center",
        icon: "success",
        title: "Venta eliminada correctamente",
        showConfirmButton: false,
        timer: 2000
   });
</script>

<?php
  unset($_SESSION['mensaje1']);

}

if(isset($_SESSION['mensaje6'])) {
    $respuesta = $_SESSION['mensaje6']; ?>
    <script>
        Swal.fire({
        position: "top-center",
        icon: "error",
        title: "No se elimino la venta",
        showConfirmButton: false,
        timer: 2000
   });
</script>

<?php
  unset($_SESSION['mensaje6']);

}


?>

 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0">Listado de Ventas</h1>
          </div><!-- /.col -->
         
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

            <!-- Contenido -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Ventas Registradas</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body" style="display: block;">
                 <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                        <th><center>Nro</center></th>
                        <th><center>Nro Venta</center></th>
                        <th><center>Monto Total</center></th>
                        <th><center>Productos</center></th>
                         <th><center>Cliente</center></th>
                        <th><center>Acciones</center></th>
                  </tr>
                  </thead>
                   <tbody>
                      <?php
                      
                        $contador_ventas = 0;
                      foreach ($datos_ventas as $dato_ventas){ 
                        $id_venta = $dato_ventas['id_venta'];
                         $id_cliente = $dato_ventas['id_cliente'];
                        $contador_ventas = $contador_ventas + 1;
                        ?>

                        <tr>
                           <td><center><?php echo $contador_ventas;?></center></td>
                           <td><center><?php echo $dato_ventas['nro_venta'];?></center></td>
                            <td><center>Q<?php echo $dato_ventas['total_pagado'];?></center></td>
                            <td>
                                <center>
                                                                        <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_productos<?php echo $id_venta; ?>">
                                    Productos
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="modal_productos<?php echo $id_venta; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                        <div class="modal-header" style="background-color: #08c2ec;">
                                            <h5 class="modal-title" id="exampleModalLabel">Productos de la Venta <?php echo $dato_ventas['nro_venta']; ?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
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
                                      $nro_venta = $dato_ventas['nro_venta'];
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
                                        
                                        </div>
                                    </div>
                                    </div>

                                </center>
                            </td>

                            <td>
                                <center>
                                                                        <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal_cliente<?php echo $id_venta; ?>">
                                    <?php echo $dato_ventas['nombre_cliente']; ?>
                                    </button>

                                    <!-- Modal -->
                                    
 <!-- Modal para Crear clientes -->
                                 <div class="modal fade" id="modal_cliente<?php echo $id_venta; ?>">
                                    <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #FDD600; color: black;" >
                                        <h4 class="modal-title">Cliente</h4>
                                        <div style="width: 10px;"></div>
                                        
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <?php 

                                        $sql_clientes = "SELECT * FROM tb_clientes WHERE id_cliente = '$id_cliente'";
                                        $query_clientes = $pdo->prepare($sql_clientes);
                                        $query_clientes->execute();
                                        $datos_clientes = $query_clientes->fetchAll(PDO::FETCH_ASSOC);

                                            foreach($datos_clientes as $dato_cliente){
                                                if($dato_cliente['id_cliente'] == $dato_ventas['id_cliente']){
                                                    $nombre_cliente = $dato_cliente['nombre_cliente'];
                                                    $nit_ci_cliente = $dato_cliente['nit_ci_cliente'];
                                                    $celular_cliente = $dato_cliente['celular_cliente'];
                                                    $email_cliente = $dato_cliente['email_cliente'];
                                                }
                                            }
                                        ?>
                                        <div class="modal-body">

                                           
                                            <div class="form-group">
                                              <label for="">Nombre del Cliente</label>
                                              <input type="text" class="form-control" name="nombre_cliente" value="<?php echo $nombre_cliente; ?>" disabled>
                                            </div>
                                            <div class="form-group">
                                              <label for="">NIT/DPI del Cliente</label>
                                              <input type="text" class="form-control" name="nit_ci_cliente" value="<?php echo $nit_ci_cliente; ?>" disabled>
                                            </div>
                                            <div class="form-group">
                                              <label for="">Celular del Cliente</label>
                                              <input type="text" class="form-control" name="celular_cliente" value="<?php echo $celular_cliente; ?>" disabled>
                                            </div>
                                            <div class="form-group">
                                              <label for="">Correo del Cliente</label>
                                              <input type="email" class="form-control" name="email_cliente" value="<?php echo $email_cliente; ?>" disabled>
                                            </div>
                                            
                                           
                                
                                        </div>
                                        
                                    </div>
                                    <!-- /.modal-content -->

                                    </div>
                                    <!-- /.modal-dialog -->
                                </center>
                            </td>

                            <td>
                                <center>
                                    <a href="show.php?id_venta=<?php echo $dato_ventas['id_venta']; ?>" class="btn btn-primary">Mostrar</a>
                                    <a href="delete.php?id_venta=<?php echo $dato_ventas['id_venta']; ?>&nro_venta=<?php echo $nro_venta;?>" class="btn btn-danger">Eliminar</a>
                                    
                                </center>
                            </td>

                            
                        </tr>
                        
                      <?php  
                      }
                      ?>
                    </tbody>
                
                </table>
                 </div>
              </div>
              <!-- /.card-body -->
            </div>
                </div>
                    
            </div>

        <!-- /.row -->
            <div class="row">
              <div class="col-md-12">
                <div class="card card-outline card-secondary">
                  <div class="card-header">
                    <h3 class="card-title">Ventas Externas (Railway)</h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="example_ext" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th><center>#</center></th>
                            <th><center>Nro Venta</center></th>
                            <th><center>Monto Total</center></th>
                            <th><center>Productos</center></th>
                            <th><center>Cliente</center></th>
                            <th><center>Acciones</center></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $i = 0;
                          if (($datos_ventas_externas['modo'] ?? '') === 'agrupado') {
                            foreach (($datos_ventas_externas['grupos'] ?? []) as $grp) {
                              $i++;
                              $nro = htmlspecialchars($grp['nro_venta']);
                              $idCli = (int)$grp['id_cliente'];
                              $nombreCli = htmlspecialchars($datos_ventas_externas['clientes'][$idCli] ?? '');
                              $montoTotal = htmlspecialchars($grp['monto_total']);
                          ?>
                            <tr>
                              <td><center><?php echo $i; ?></center></td>
                              <td><center><?php echo $nro; ?></center></td>
                              <td><center>Q<?php echo $montoTotal; ?></center></td>
                              <td>
                                <center>
                                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_ext_prod_<?php echo $nro; ?>">Productos</button>
                                  <div class="modal fade" id="modal_ext_prod_<?php echo $nro; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                      <div class="modal-content">
                                        <div class="modal-header" style="background-color:#08c2ec;">
                                          <h5 class="modal-title">Productos de la Venta Externa <?php echo $nro; ?></h5>
                                          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                          <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-sm table-hover">
                                              <thead>
                                                <tr>
                                                  <th><center>#</center></th>
                                                  <th><center>Producto</center></th>
                                                  <th><center>Cantidad</center></th>
                                                  <th><center>Ubicaciones</center></th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                                <?php
                                                $sql = "SELECT * FROM tb_ventas_externas WHERE nro_venta = :nv ORDER BY id_producto ASC";
                                                $st = $pdo->prepare($sql);
                                                $st->execute([':nv' => $grp['nro_venta']]);
                                                $rows = $st->fetchAll(PDO::FETCH_ASSOC);
                                                $c=0; $cant_t=0;
                                                foreach($rows as $r){ $c++; $cant_t += (float)$r['stock_total']; ?>
                                                  <tr>
                                                    <td><center><?php echo $c; ?></center></td>
                                                    <td><center><?php echo htmlspecialchars($r['nombre']); ?></center></td>
                                                    <td><center><?php echo htmlspecialchars($r['stock_total']); ?></center></td>
                                                    <td><center><?php echo htmlspecialchars($r['ubicaciones']); ?></center></td>
                                                  </tr>
                                                <?php } ?>
                                                <tr>
                                                  <td colspan="2" style="text-align:right;font-weight:bold;">Total:</td>
                                                  <td><b><center><?php echo $cant_t; ?></center></b></td>
                                                  <td></td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </center>
                              </td>
                              <td>
                                <center>
                                  <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal_ext_cli_<?php echo $nro; ?>"><?php echo $nombreCli ?: '—'; ?></button>
                                  <div class="modal fade" id="modal_ext_cli_<?php echo $nro; ?>">
                                    <div class="modal-dialog modal-sm">
                                      <div class="modal-content">
                                        <div class="modal-header" style="background-color:#FDD600; color:black;">
                                          <h4 class="modal-title">Cliente</h4>
                                          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                          <?php
                                            $cli = ['nombre_cliente'=>'','nit_ci_cliente'=>'','celular_cliente'=>'','email_cliente'=>''];
                                            if ($idCli > 0) {
                                              $qc = $pdo->prepare("SELECT * FROM tb_clientes WHERE id_cliente = :id LIMIT 1");
                                              $qc->execute([':id'=>$idCli]);
                                              $cli = $qc->fetch(PDO::FETCH_ASSOC) ?: $cli;
                                            }
                                          ?>
                                          <div class="form-group">
                                            <label>Nombre del Cliente</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($cli['nombre_cliente']); ?>" disabled>
                                          </div>
                                          <div class="form-group">
                                            <label>NIT/DPI del Cliente</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($cli['nit_ci_cliente']); ?>" disabled>
                                          </div>
                                          <div class="form-group">
                                            <label>Celular del Cliente</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($cli['celular_cliente']); ?>" disabled>
                                          </div>
                                          <div class="form-group">
                                            <label>Correo del Cliente</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($cli['email_cliente']); ?>" disabled>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </center>
                              </td>
                              <td>
                                <center>
                                  <a href="../app/controllers/ventas/delete_venta_externa_group.php?nro_venta=<?php echo urlencode($grp['nro_venta']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar toda la venta externa y revertir stock?');">Eliminar</a>
                                </center>
                              </td>
                            </tr>
                          <?php }
                          } else {
                            // Modo simple (compatibilidad)
                            $i = 0; foreach((($datos_ventas_externas['filas'] ?? [])) as $ve){ ?>
                              <tr>
                                <td><center><?php echo ++$i; ?></center></td>
                                <td><center>—</center></td>
                                <td><center><?php echo htmlspecialchars($ve['precio']); ?></center></td>
                                <td><center><?php echo htmlspecialchars($ve['nombre']); ?> (x<?php echo htmlspecialchars($ve['stock_total']); ?>)</center></td>
                                <td><center>—</center></td>
                                <td><center>
                                  <a href="../app/controllers/ventas/delete_venta_externa.php?id_producto=<?php echo urlencode($ve['id_producto']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta venta externa y revertir el stock?');">Eliminar</a>
                                </center></td>
                              </tr>
                          <?php } }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <script>
  $(function () {
    $("#example_ext").DataTable({
        "pageLength": 5,
          language: {
              "emptyTable": "No hay información",
              "decimal": "",
              "info": "Mostrando _START_ a _END_ de _TOTAL_ ventas externas",
              "infoEmpty": "Mostrando 0 to 0 of 0 ventas externas",
              "infoFiltered": "(Filtrado de _MAX_ total ventas externas)",
              "infoPostFix": "",
              "thousands": ",",
              "lengthMenu": "Mostrar _MENU_ ventas externas",
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
        buttons: [{
                        extend: 'collection',
                        text: 'Reportes',
                        orientation: 'landscape',
                        buttons: [{
                            text: 'Copiar',
                            extend: 'copy'
                        }, {
                            extend: 'pdf',
                        }, {
                            extend: 'csv',
                        }, {
                            extend: 'excel',
                        }, {
                            text: 'Imprimir',
                            extend: 'print'
                        }
                        ]
                    },
                        {
                            extend: 'colvis',
                            text: 'Visor de columnas'
                           // collectionLayout: 'fixed three-column',
                        }
                    ],
    }).buttons().container().appendTo('#example_ext_wrapper .col-md-6:eq(0)');
  });
</script>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

</script>

<?php include ('../layout/parte2.php'); ?>


<script>
  $(function () {
    $("#example1").DataTable({
        "pageLength": 5,
          language: {
              "emptyTable": "No hay información",
              "decimal": "",
              "info": "Mostrando _START_ a _END_ de _TOTAL_ Ventas",
              "infoEmpty": "Mostrando 0 to 0 of 0 Ventas",
              "infoFiltered": "(Filtrado de _MAX_ total Ventas)",
              "infoPostFix": "",
              "thousands": ",",
              "lengthMenu": "Mostrar _MENU_ Ventas",
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
        buttons: [{
                        extend: 'collection',
                        text: 'Reportes',
                        orientation: 'landscape',
                        buttons: [{
                            text: 'Copiar',
                            extend: 'copy'
                        }, {
                            extend: 'pdf',
                        }, {
                            extend: 'csv',
                        }, {
                            extend: 'excel',
                        }, {
                            text: 'Imprimir',
                            extend: 'print'
                        }
                        ]
                    },
                        {
                            extend: 'colvis',
                            text: 'Visor de columnas'
                           // collectionLayout: 'fixed three-column',
                        }
                    ],
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>

