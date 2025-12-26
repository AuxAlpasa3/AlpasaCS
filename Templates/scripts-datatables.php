<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script>
    $(function() {
        $("#dataTable").DataTable({
            "responsive": true,
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
                "infoEmpty": "Mostrando 0 to 0 of 0 Registros",
                "infoFiltered": "(Filtrado de _MAX_ total registros)",
                "zeroRecords": "No se encontraron registros",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente",
                }
            },      
            "searching": true,
            "lengthChange": true,
            "autoWidth": false
        }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
    });
    
    $(function() {
        $("#tablaSinB").DataTable({
            "responsive": true,
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
                "infoEmpty": "Mostrando 0 to 0 of 0 Registros",
                "infoFiltered": "(Filtrado de _MAX_ total registros)",
                "zeroRecords": "No se encontraron registros",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente",
                }
            },      
            "searching": true,
            "lengthChange": true,
            "autoWidth": false
        }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
    });

    $(function() {
         $("#tablaconB").DataTable({
            "responsive": true,
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
                "infoEmpty": "Mostrando 0 to 0 of 0 Registros",
                "infoFiltered": "(Filtrado de _MAX_ total registros)",
                "zeroRecords": "No se encontraron registros",
                "search": "Buscar",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente",
                }
            },
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Excel',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    className: 'btn btn-danger',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: 'Imprimir',
                    className: 'btn btn-info',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            "searching": true,
            "lengthChange": true,
            "autoWidth": false
        });
    });
</script>