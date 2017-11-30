<?php

add_handler_pages("page", "assistant", "assistant");

function getTicketsFooter($user)
{
    global $db;
    
    $sql = "SELECT ticket_id FROM support_user_tickets WHERE user = :user LIMIT 4";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user', $user);
    $stmt->execute();

    $row = $stmt->fetchAll();
    
    return $row;
}
?>