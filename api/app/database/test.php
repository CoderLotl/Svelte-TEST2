<?php

$pdo = new PDO('sqlite:' . './api/app/database/Database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$new_id = 1310598788489936897;
$stm = $pdo->prepare("SELECT id FROM sessions WHERE id = :sessionID LIMIT 1");
$stm->bindParam(':sessionID', $new_id);
$stm->execute();

echo $stm->fetchColumn();
