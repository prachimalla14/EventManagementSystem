<?php
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function participantCount($pdo, $event_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM participants WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'];
}
?>
