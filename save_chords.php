<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['acordes']) && isset($_POST['id_musica'])) {
        $id_musica = $_POST['id_musica'];
        $acordes = json_decode($_POST['acordes'], true);

        // Adiciona estas linhas para verificar se os dados estão corretos
        echo "ID da Música: " . htmlspecialchars($id_musica) . "\n";
        echo "Acordes a serem enviados: " . htmlspecialchars(print_r($acordes, true)) . "\n";
        echo "VAR DUMP:";
        echo var_dump($acordes);

        if (empty($acordes)) {
            echo "Nenhum acorde recebido.";
            exit();
        }

        $conn->query("DELETE FROM acordes WHERE id_musica = $id_musica");

        // Preparar a consulta para inserção dos dados
        $stmt = $conn->prepare("INSERT INTO acordes (id_musica, acorde) VALUES (?, ?)");
        $stmt->bind_param("is", $id_musica, $acorde_json);

        foreach ($acordes as $compasso => $acorde) {
            // Transformar o acorde em JSON antes de inserir na base de dados
            $acorde_json = json_encode(array($compasso => $acorde));

            // Executar a consulta
            $stmt->execute();
        }

        // Fechar a consulta
        $stmt->close();

        echo "Acordes guardados na base de dados com sucesso.";
    } else {
        echo "Dados incompletos.";
    }
}
