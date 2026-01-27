function loadCount(eventId) {
    fetch("index.php?ajax_count=" + eventId)
        .then(res => res.text())
        .then(data => {
            document.getElementById('count-' + eventId).innerHTML = "Registered: " + data;
        });
}
