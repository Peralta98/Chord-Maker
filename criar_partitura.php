<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifique se o nome da música está definido e se não está vazio
    if (isset($_POST['nomeMusica']) && !empty($_POST['nomeMusica'])) {
        $nomeMusica = $_POST['nomeMusica'];

        $stmt = $conn->prepare("INSERT INTO musicas (nome) VALUES (?)");
        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("s", $nomeMusica);
        $stmt->execute();

        if ($stmt->error) {
            die('execute() failed: ' . htmlspecialchars($stmt->error));
        }

        $id_musica = $stmt->insert_id;
        $stmt->close();

        header("Location: index.php?id_musica=" . $id_musica);
        exit();
    }

    // Lógica para salvar acordes na base de dados
    if (isset($_POST['acordes']) && isset($_POST['id_musica'])) {
        $id_musica = $_POST['id_musica'];
        $acordes = json_decode($_POST['acordes'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            die('JSON decode error: ' . json_last_error_msg());
        }

        // Limpar acordes antigos para esta música
        if (!$conn->query("DELETE FROM acordes WHERE id_musica = $id_musica")) {
            die('Error deleting old chords: ' . htmlspecialchars($conn->error));
        }

        // Inserir novos acordes
        $stmt = $conn->prepare("INSERT INTO acordes (id_musica, compasso, acorde) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("iis", $id_musica, $compasso, $acorde);

        foreach ($acordes as $compasso => $acorde) {
            $stmt->execute();
            if ($stmt->error) {
                die('execute() failed: ' . htmlspecialchars($stmt->error));
            }
        }

        $stmt->close();

        echo "Acordes guardados na base de dados com sucesso.";
    }
}
