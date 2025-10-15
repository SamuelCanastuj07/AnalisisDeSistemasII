<?php
include ('../app/config.php');
include ('../layout/sesion.php');
include ('../layout/parte1.php');
include('../app/controllers/almacen2/listado_de_productos_view.php');
?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0">Listado de Productos - Almacén Externo</h1>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Productos Registrados (externos)</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
              </div>
              <div class="card-body" style="display: block;">
                <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th><center>No</center></th>
                        <th><center>id_producto</center></th>
                        <th><center>Nombre</center></th>
                        <th><center>Precio</center></th>
                        <th><center>Stock total</center></th>
                        <th><center>Ubicaciones</center></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $contador = 0;
                        foreach ($datos_productos as $dato_productos){
                      ?>
                      <tr>
                        <td><?php echo ++$contador; ?></td>
                        <td><?php echo htmlspecialchars($dato_productos['id_producto']); ?></td>
                        <td><?php echo htmlspecialchars($dato_productos['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($dato_productos['precio_venta']); ?></td>
                        <td><?php echo htmlspecialchars($dato_productos['stock']); ?></td>
                        <td>
                          <?php
                            $ubic = $dato_productos['ubicaciones'] ?? [];
                            $parts = [];
                            foreach ($ubic as $u) {
                              $uid = $u['id_ubicacion'] ?? ($u['id'] ?? '');
                              $ust = isset($u['stock']) ? $u['stock'] : (isset($u['stock_total']) ? $u['stock_total'] : '');
                              $parts[] = $uid . ':' . $ust;
                            }
                            echo htmlspecialchars(implode(', ', $parts));
                          ?>
                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

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
