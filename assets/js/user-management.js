document.addEventListener("DOMContentLoaded", function () {
    const selectAll = document.getElementById("select-all");
    const checkboxes = document.querySelectorAll(".user-checkbox");
    const deleteBtn = document.getElementById("delete-users");
    const blockBtn = document.getElementById("block-users");
    const unblockBtn = document.getElementById("unblock-users");

    // Seleccionar todos los checkboxes
    selectAll.addEventListener("change", function () {
        checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
    });
    
    

    function getSelectedUsers() {
        return Array.from(document.querySelectorAll(".user-checkbox:checked")) // Checkboxes seleccionados
            .map(checkbox => checkbox.closest("tr").dataset.id) // Obtener ID de cada usuario
            .filter(id => id); // Filtrar valores vacíos
    }

    async function updateUsers(action) {
        const userIds = getSelectedUsers();
        if (userIds.length === 0) {
            alert("Select at least one user.");
            return;
        }

        const response = await fetch(`/admin/${action}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ userIds })
        });

        if (response.ok) {
            location.reload();
        } else {
            alert("Error updating users.");
        }
    }

    deleteBtn.addEventListener("click", () => updateUsers("delete"));
    blockBtn.addEventListener("click", () => updateUsers("block"));
    unblockBtn.addEventListener("click", () => updateUsers("unblock"));
});

