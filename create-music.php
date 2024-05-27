<?php
include 'db.php';

// Lógica para criar uma nova música
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nomeMusica'])) {
    $nomeMusica = $conn->real_escape_string($_POST['nomeMusica']);
    $sql = "INSERT INTO musicas (nome) VALUES ('$nomeMusica')";
    if ($conn->query($sql) === TRUE) {
        $musicaId = $conn->insert_id;
        header("Location: index.php?id_musica=$musicaId");
        exit();
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}

// Lógica para eliminar uma música
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteMusica'])) {
    $idMusica = $conn->real_escape_string($_POST['idMusica']);
    $sql = "DELETE FROM musicas WHERE id = '$idMusica'";
    if ($conn->query($sql) === TRUE) {
        header("Location: create-music.php");
        exit();
    } else {
        echo "Erro ao eliminar a música: " . $sql . "<br>" . $conn->error;
    }
}

// Buscar todas as músicas existentes
$sql = "SELECT id, nome FROM musicas";
$result = $conn->query($sql);

$musicas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $musicas[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles-create-music.css">
    <title>Criar Música</title>

</head>

<body>
    <div class="container">
        <h1>Criar Nova Música</h1>
        <form method="POST" action="create-music.php">
            <label for="nomeMusica">Nome da Música:</label>
            <input type="text" id="nomeMusica" name="nomeMusica" required>
            <button type="submit">Criar Partitura</button>
        </form>

        <?php if (isset($_GET['id_musica'])) : ?>
            <input type="hidden" id="idMusica" name="idMusica" value="<?php echo htmlspecialchars($_GET['id_musica']); ?>">
        <?php endif; ?>



        <div class="music-list">
            <h2>Músicas Existentes</h2>
            <?php if (count($musicas) > 0) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($musicas as $musica) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($musica['id']); ?></td>
                                <td><?php echo htmlspecialchars($musica['nome']); ?></td>
                                <td>
                                    <a href="index.php?id_musica=<?php echo htmlspecialchars($musica['id']); ?>">Editar</a>

                                    <form action="create-music.php" method="post" style="display:inline;" onsubmit="return confirm('Tens a certeza que queres eliminar esta música?');">
                                        <input type="hidden" name="idMusica" value="<?php echo htmlspecialchars($musica['id']); ?>">
                                        <button type="submit" name="deleteMusica">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No music found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>