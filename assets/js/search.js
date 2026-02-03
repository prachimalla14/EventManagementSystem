// Live search functionality
document.addEventListener("DOMContentLoaded", function () {

    const input = document.getElementById("search-box");
    const resultsBox = document.getElementById("search-results");
    if (!input) return;

    // Fetch results on typing
    input.addEventListener("keyup", function () {
        let query = this.value.trim();

        if (query.length < 2) {
            resultsBox.innerHTML = "";
            return;
        }

        fetch("search_ajax.php?q=" + encodeURIComponent(query))
            .then(res => res.text())
            .then(data => {
                resultsBox.innerHTML = data;
            });
    });

    // Fill input and submit form when item clicked
    resultsBox.addEventListener("click", function (e) {
        if (e.target.classList.contains("search-item")) {
            input.value = e.target.textContent;
            resultsBox.innerHTML = "";

            if (input.form) input.form.submit();
        }
    });
});
