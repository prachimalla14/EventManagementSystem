document.addEventListener("DOMContentLoaded", function () {

    const input = document.getElementById("search-box");
    const resultsBox = document.getElementById("search-results");

    if (!input) return;

    // Autocomplete as user types
    input.addEventListener("keyup", function () {
        let query = this.value.trim();

        if (query.length < 2) {
            resultsBox.innerHTML = "";
            return;
        }

        // Use correct relative path to search_ajax.php
        fetch("search_ajax.php?q=" + encodeURIComponent(query))
            .then(res => res.text())
            .then(data => {
                resultsBox.innerHTML = data;
            });
    });

    // Click on a search suggestion
    resultsBox.addEventListener("click", function (e) {
        if (e.target.classList.contains("search-item")) {
            input.value = e.target.textContent;
            resultsBox.innerHTML = "";

            // Optional: submit form if needed
            if (input.form) input.form.submit();
        }
    });
});
