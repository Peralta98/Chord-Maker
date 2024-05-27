<?php
include 'db.php';

// Verificar se foi fornecido um ID de música
if (isset($_GET['id_musica'])) {
    // Recuperar o ID da música
    $id_musica = $_GET['id_musica'];

    // Consulta SQL para recuperar os acordes da música especificada
    $sql = "SELECT acorde FROM acordes WHERE id_musica = ?";

    // Preparar a consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_musica);

    // Executar a consulta
    $stmt->execute();

    // Vincular o resultado da consulta
    $stmt->bind_result($acorde_json);

    // Inicializar um array para armazenar os acordes
    $acordes = array();

    // Iterar sobre os resultados e adicionar os acordes ao array
    while ($stmt->fetch()) {
        // Decodificar o acorde JSON em um array associativo
        $acorde_array = json_decode($acorde_json, true);

        // Mesclar os acordes recuperados com o array principal
        $acordes = array_merge($acordes, $acorde_array);
    }

    // Fechar a consulta
    $stmt->close();

    // Retornar os acordes como JSON
    echo json_encode($acordes);
} else {
    echo "ID da música não fornecido.";
}
