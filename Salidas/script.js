document.getElementById('addButton').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.select-checkbox');
    const targetTableBody = document.getElementById('targetTable').querySelector('tbody');

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const row = checkbox.closest('tr');
            const name = row.cells[1].textContent;
            const age = row.cells[2].textContent;

            const newRow = targetTableBody.insertRow();
            const nameCell = newRow.insertCell(0);
            const ageCell = newRow.insertCell(1);

            nameCell.textContent = name;
            ageCell.textContent = age;

            checkbox.checked = false; // Desmarcar el checkbox después de añadir
        }
    });
});
