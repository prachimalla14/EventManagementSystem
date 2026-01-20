function loadCount(eventId) {
    fetch("registration_count.php?event_id=" + eventId)
        .then(res => res.text())
        .then(data => {
            document.getElementById("count-" + eventId).innerHTML = data;
        });
}
