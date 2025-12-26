<?php
include_once "../templates/head.php";

date_default_timezone_set('America/Mexico_City');

echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>';
echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>';

?>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
      
        
        // Función auxiliar para actualizar una card específica
        var updateCardStats = function(poligonoId, data) {
            let cardSelector;
            
            switch(poligonoId) {
                case 1:
                    cardSelector = 'h3:contains("POLIGONO A")';
                    break;
                case 2:
                    cardSelector = 'h3:contains("POLIGONO B")';
                    break;
                case 3:
                    cardSelector = 'h3:contains("CENTRO LOGISTICO CEBU")';
                    break;
                case 4:
                    cardSelector = 'h3:contains("POLIGONO D")';
                    break;
                case 5:
                    cardSelector = 'h3:contains("POLIGONO C")';
                    break;
                case 6:
                    cardSelector = 'h3:contains("ROYAL")';
                    break;
                default:
                    return;
            }
            
            const header = document.querySelector(cardSelector);
            if (!header) return;
            
            const card = header.closest('.card');
            if (!card) return;
            
            // Actualizar Personal
            updateCell(card, 'Personal', 'en-sitio-count', data.personal.enSitio);
            updateCell(card, 'Personal', 'ingresos-count', data.personal.ingresos);
            updateCell(card, 'Personal', 'egresos-count', data.personal.egresos);
            
            // Actualizar Vehículos
            updateCell(card, 'Vehículos', 'en-sitio-count', data.vehiculos.enSitio);
            updateCell(card, 'Vehículos', 'ingresos-count', data.vehiculos.ingresos);
            updateCell(card, 'Vehículos', 'egresos-count', data.vehiculos.egresos);
            
            // Actualizar Visitantes
            updateCell(card, 'Visitantes', 'en-sitio-count', data.visitantes.enSitio);
            updateCell(card, 'Visitantes', 'ingresos-count', data.visitantes.ingresos);
            updateCell(card, 'Visitantes', 'egresos-count', data.visitantes.egresos);
            
            // Actualizar Maniobras
            updateCell(card, 'Maniobras', 'en-sitio-count', data.maniobras.enSitio);
            updateCell(card, 'Maniobras', 'ingresos-count', data.maniobras.ingresos);
            updateCell(card, 'Maniobras', 'egresos-count', data.maniobras.egresos);
            
            // Actualizar Totales
            updateCell(card, 'TOTALES', 'total-en-sitio', data.totales.enSitio, true);
            updateCell(card, 'TOTALES', 'total-ingresos', data.totales.ingresos, true);
            updateCell(card, 'TOTALES', 'total-egresos', data.totales.egresos, true);
        }
        
        var updateCell = function(card, categoria, tipo, valor, isTotal = false) {
            const rows = card.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const categoriaCell = row.querySelector('.category-cell');
                if (!categoriaCell) return;
                
                const categoriaText = categoriaCell.textContent.trim();
                if (categoriaText.includes(categoria)) {
                    let cell;
                    
                    if (isTotal) {
                        // Para totales, buscar por clase específica
                        cell = row.querySelector(`.${tipo}`);
                    } else {
                        // Para celdas normales, buscar por clase exacta
                        cell = row.querySelector(`.${tipo}`);
                    }
                    
                    if (cell) {
                        const span = cell.querySelector('span') || cell.querySelector('strong');
                        const currentValue = parseInt(span?.textContent || cell.textContent) || 0;
                        
                        if (currentValue !== valor) {
                            // Añadir efecto visual de actualización
                            cell.style.backgroundColor = '#e8f5e9';
                            setTimeout(() => {
                                cell.style.backgroundColor = '';
                            }, 1000);
                        }
                        
                        if (isTotal) {
                            cell.innerHTML = `<strong>${valor}</strong>`;
                        } else {
                            cell.innerHTML = `<span>${valor}</span>`;
                        }
                    }
                }
            });
        }
        
        // Función para actualizar el timestamp de última actualización
        var updateLastUpdateTime = function() {
            const now = moment();
            const updateTimeEl = document.getElementById('updateTime');
            if (updateTimeEl) {
                updateTimeEl.textContent = now.format('HH:mm:ss');
            }
        }
        
        // Actualizar estadísticas cada 15 segundos (puedes ajustar este tiempo)
        setInterval(updateStats, 15000);
        
        // Cargar datos iniciales desde PHP
        loadInitialData();
        
        // Actualizar inmediatamente al cargar la página
        setTimeout(updateStats, 2000);
        
        // Funcionalidad de colapsar/expandir cards
        const cardHeaders = document.querySelectorAll('.card-header');
        
        cardHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const card = this.closest('.card');
                const cardBody = card.querySelector('.card-body');
                const icon = this.querySelector('.toggle-icon');
                
                // Alternar estado
                card.classList.toggle('collapsed');
                
                if (card.classList.contains('collapsed')) {
                    cardBody.style.display = 'none';
                    icon.textContent = '+';
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    cardBody.style.display = 'block';
                    icon.textContent = '−';
                    icon.style.transform = 'rotate(0deg)';
                }
            });
        });
        
        // Inicializar todas las cards colapsadas
        document.querySelectorAll('.card').forEach(card => {
            const cardBody = card.querySelector('.card-body');
            const icon = card.querySelector('.toggle-icon');
            card.classList.add('collapsed');
            cardBody.style.display = 'none';
            icon.textContent = '+';
        });
    });
</script>
          <div class="card">
              <div class="container-fluid">
                    <div style="text-align: right;">
                      <div class="nowDateTime">
                        <p>
                          <span style="color: orange; font-weight: bold;" id="fecha"></span><br/>
                          <span style="font-weight: bold;" id="hora"></span>
                        </p>
                      </div>
                    </div>
                      <h1 class="h3 mb-4 text-gray-800"></h1>
                        <div class="row">
                          <div class="column">
                            <div class="card">
                              <div class="card-header">
                                <h3>POLIGONO A</h3>
                                <span class="toggle-icon">+</span>
                              </div>
                              <div class="card-body">
                                <div class="table-container">
                                  <table class="stats-table">
                                    <thead>
                                      <tr>
                                        <th class="category-header">CATEGORÍA</th>
                                        <th class="status-header">
                                          <div class="status-indicator en-sitio"></div>
                                          <div>EN SITIO</div>
                                        </th>
                                        <th class="status-header">
                                          <div class="status-indicator ingresos"></div>
                                          <div>INGRESOS</div>
                                        </th>
                                        <th class="status-header">
                                          <div class="status-indicator egresos"></div>
                                          <div>EGRESOS</div>
                                        </th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr>
                                        <td class="category-cell">
                                          <div class="category-info">
                                            <img src="../dist/img/Personal.png" alt="Personal" class="category-icon">
                                            <span>Personal</span>
                                          </div>
                                        </td>
                                        <td class="count-cell en-sitio-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalper WHERE IdUbicacion = 1 AND FolMovSal = 0";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell ingresos-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalper WHERE IdUbicacion = 1";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell egresos-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalper WHERE IdUbicacion = 1 AND FolMovSal <> 0";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                      </tr>
                                      
                                      <!-- Vehículos -->
                                      <tr>
                                        <td class="category-cell">
                                          <div class="category-info">
                                            <img src="../dist/img/Vehiculos.png" alt="Vehículos" class="category-icon">
                                            <span>Vehículos</span>
                                          </div>
                                        </td>
                                        <td class="count-cell en-sitio-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalveh WHERE IdUbicacion = 1 AND FolMovSal = 0";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell ingresos-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalveh WHERE IdUbicacion = 1";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell egresos-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalveh WHERE IdUbicacion = 1 AND FolMovSal <> 0";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                      </tr>
                                      
                                      <!-- Visitantes -->
                                      <tr>
                                        <td class="category-cell">
                                          <div class="category-info">
                                            <img src="../dist/img/Visitantes.png" alt="Visitantes" class="category-icon">
                                            <span>Visitantes</span>
                                          </div>
                                        </td>
                                        <td class="count-cell en-sitio-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalvis WHERE IdUbicacion = 1 AND FolMovSal = 0";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell ingresos-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalvis WHERE IdUbicacion = 1";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell egresos-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalvis WHERE IdUbicacion = 1 AND FolMovSal <> 0";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                      </tr>
                                      
                                      <!-- Maniobras -->
                                      <tr>
                                        <td class="category-cell">
                                          <div class="category-info">
                                            <img src="../dist/img/Maniobras.png" alt="Maniobras" class="category-icon">
                                            <span>Maniobras</span>
                                          </div>
                                        </td>
                                        <td class="count-cell en-sitio-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalman WHERE IdUbicacion = 1 AND FolMovSal = 0";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell ingresos-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalman WHERE IdUbicacion = 1";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell egresos-count">
                                          <?php 
                                            $sql = "SELECT COUNT(*) as PolA FROM regentsalman WHERE IdUbicacion = 1 AND FolMovSal <> 0";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<span>" . $row['PolA'] . "</span>";
                                            }
                                          ?>
                                        </td>
                                      </tr>
                                      
                                      <!-- Totales -->
                                      <tr class="totals-row">
                                        <td class="category-cell">
                                          <strong>TOTALES</strong>
                                        </td>
                                        <td class="count-cell total-en-sitio">
                                          <?php 
                                            // Sumar todos los "en sitio" para este polígono
                                            $sql = "SELECT 
                                              (SELECT COUNT(*) FROM regentsalper WHERE IdUbicacion = 1 AND FolMovSal = 0) +
                                              (SELECT COUNT(*) FROM regentsalveh WHERE IdUbicacion = 1 AND FolMovSal = 0) +
                                              (SELECT COUNT(*) FROM regentsalvis WHERE IdUbicacion = 1 AND FolMovSal = 0) +
                                              (SELECT COUNT(*) FROM regentsalman WHERE IdUbicacion = 1 AND FolMovSal = 0) as total";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<strong>" . $row['total'] . "</strong>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell total-ingresos">
                                          <?php 
                                            $sql = "SELECT 
                                              (SELECT COUNT(*) FROM regentsalper WHERE IdUbicacion = 1) +
                                              (SELECT COUNT(*) FROM regentsalveh WHERE IdUbicacion = 1) +
                                              (SELECT COUNT(*) FROM regentsalvis WHERE IdUbicacion = 1) +
                                              (SELECT COUNT(*) FROM regentsalman WHERE IdUbicacion = 1) as total";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<strong>" . $row['total'] . "</strong>";
                                            }
                                          ?>
                                        </td>
                                        <td class="count-cell total-egresos">
                                          <?php 
                                            $sql = "SELECT 
                                              (SELECT COUNT(*) FROM regentsalper WHERE IdUbicacion = 1 AND FolMovSal <> 0) +
                                              (SELECT COUNT(*) FROM regentsalveh WHERE IdUbicacion = 1 AND FolMovSal <> 0) +
                                              (SELECT COUNT(*) FROM regentsalvis WHERE IdUbicacion = 1 AND FolMovSal <> 0) +
                                              (SELECT COUNT(*) FROM regentsalman WHERE IdUbicacion = 1 AND FolMovSal <> 0) as total";
                                            $stmt = $Conexion->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                              echo "<strong>" . $row['total'] . "</strong>";
                                            }
                                          ?>
                                        </td>
                                      </tr>
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
                                            
  <?php include_once "../templates/footer.php"; ?>

<style>
.row {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin: 20px 0;
}

.column {
  flex: 1;
  min-width: 300px;
}

.card {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  overflow: hidden;
}

.card-header {
  background-color: #f8f9fa;
  padding: 15px 20px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #dee2e6;
  transition: background-color 0.3s;
}

.card-header:hover {
  background-color: #e9ecef;
}

.card-header h3 {
  margin: 0;
  color: #333;
  font-size: 1.2rem;
}

.toggle-icon {
  font-size: 1.5rem;
  font-weight: bold;
  color: #495057;
  transition: transform 0.3s;
}

.card-body {
  padding: 20px;
  transition: all 0.3s ease;
}

.table-container {
  overflow-x: auto;
}

.stats-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.stats-table th, .stats-table td {
  padding: 12px 8px;
  text-align: center;
  border-bottom: 1px solid #eee;
}

.stats-table thead th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #495057;
}

.category-header {
  text-align: left !important;
  font-weight: 600;
}

.status-header {
  min-width: 80px;
}

.status-indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin: 0 auto 5px;
}

.status-indicator.en-sitio {
  background-color: darkorange;
}

.status-indicator.ingresos {
  background-color: #28a745;
}

.status-indicator.egresos {
  background-color: #dc3545;
}

.category-cell {
  text-align: left !important;
}

.category-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.category-icon {
  width: 24px;
  height: 24px;
  object-fit: contain;
}

.count-cell {
  font-size: 16px;
  font-weight: 500;
}

.en-sitio-count {
  color: darkorange;
}

.ingresos-count {
  color: #28a745;
}

.egresos-count {
  color: #dc3545;
}

.totals-row {
  background-color: #f8f9fa;
  font-weight: 600;
}

.totals-row td {
  border-top: 2px solid #dee2e6;
}

/* Botón para abrir/cerrar todas */
.controls {
  text-align: center;
  margin: 20px 0;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 8px;
}

.controls button {
  padding: 8px 16px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin: 0 5px;
}

.controls button:hover {
  background-color: #0056b3;
}

/* Responsive */
@media (max-width: 1200px) {
  .column {
    min-width: 280px;
  }
}

@media (max-width: 768px) {
  .row {
    flex-direction: column;
  }
  
  .column {
    min-width: 100%;
  }
  
  .stats-table {
    font-size: 13px;
  }
  
  .stats-table th, .stats-table td {
    padding: 8px 5px;
  }
  
  .category-icon {
    width: 20px;
    height: 20px;
  }
  
  .card-header {
    padding: 12px 15px;
  }
  
  .card-body {
    padding: 15px;
  }
}
</style>

<script>
function toggleAllCards(action) {
  const cards = document.querySelectorAll('.card');
  
  cards.forEach(card => {
    const cardBody = card.querySelector('.card-body');
    const icon = card.querySelector('.toggle-icon');
    
    if (action === 'open') {
      card.classList.remove('collapsed');
      cardBody.style.display = 'block';
      icon.textContent = '−';
      icon.style.transform = 'rotate(0deg)';
    } else if (action === 'close') {
      card.classList.add('collapsed');
      cardBody.style.display = 'none';
      icon.textContent = '+';
      icon.style.transform = 'rotate(0deg)';
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const contentSection = document.querySelector('.content .container-fluid');
  const firstCard = contentSection.querySelector('.card');
  
  // Agregar controles para abrir/cerrar todas
  const controlsDiv = document.createElement('div');
  controlsDiv.className = 'controls';
  controlsDiv.innerHTML = `
    <button onclick="toggleAllCards('open')">Abrir Todas</button>
    <button onclick="toggleAllCards('close')">Cerrar Todas</button>
    <button onclick="location.reload()">🔄 Actualizar Manual</button>
  `;
  
  contentSection.insertBefore(controlsDiv, firstCard);
});
</script>