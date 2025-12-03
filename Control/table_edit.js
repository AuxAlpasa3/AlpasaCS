$(document).ready(function(){
    $('#data_table').Tabledit({
            deleteButton: true,
            editButton: true,   
            columns: {
              identifier: [0, 'wh_id'],                    
              editable: [[1, 'NomLargo'], [2, 'NomCorto'], [3, 'Ubicacion'], [4, 'Direccion'], [5, 'Estado']
              , [6, 'CP'], [7, 'CodigoPais'], [8, 'Pais']]
            },
            hideIdentifier: false,
            url: 'EditarCelda.php',

        });
     });