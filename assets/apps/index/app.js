import "../../styles/index/app.scss";

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search");
    const table = document.getElementById("pessoas-table");
    const rows = table.querySelectorAll("tbody tr");

    searchInput.addEventListener("input", function () {
        const searchTerm = searchInput.value.trim().toLowerCase();

        rows.forEach(function (row) {
            const columns = row.querySelectorAll("td");
            let rowShouldBeVisible = false;

            columns.forEach(function (column) {
                const cellText = column.textContent.trim().toLowerCase();
                console.log(`Verificando se "${cellText}" inclui "${searchTerm}"`);

                if (cellText.includes(searchTerm)) {
                    rowShouldBeVisible = true;
                }
            });

            if (rowShouldBeVisible) {
                row.style.display = "";
                console.log("Exibindo linha.");
            } else {
                row.style.display = "none";
                console.log("Ocultando linha.");
            }
        });
    });
});