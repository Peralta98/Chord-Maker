<?php
include 'db.php';

$id_musica = $_GET['id_musica'] ?? null;

if (!$id_musica) {
    echo "ID da música não fornecido.";
    exit();
}

$sql = "SELECT nome FROM musicas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_musica);
$stmt->execute();
$result = $stmt->get_result();
$musica = $result->fetch_assoc();
$nomeMusica = $musica['nome'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    <title>Document</title>
</head>

<body>

    <h1 class="music-title"><?php echo htmlspecialchars($nomeMusica); ?></h1>

    <div class="grid-container"></div>

    <!-- Modal de confirmação -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to delete this bar?</p>
            <div class="modal-options">
                <button id="confirmDelete" class="modal-btn">Yes</button>
                <button id="cancelDelete" class="modal-btn">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Modal de edição -->
    <div id="editModal" class="modal">
        <div class="modal-content edit-modal-content clearfix">
            <h2>Add/Edit Chord</h2>
            <form id="editForm">
                <div class="modal-column-left">
                    <label for="noteType">Notes:</label>
                    <select id="noteType">
                        <option value="naturalNotes">Naturals</option>
                        <option value="flatNotes">Flats</option>
                        <option value="sharpNotes">Sharps</option>
                    </select>
                    <br>
                    <label for="chordType">Chord Quality:</label>
                    <select id="chordType">
                        <option value="majorChord">Major</option>
                        <option value="minorChord">Minor</option>
                    </select>
                    <br>
                    <label id="moreOptionsLabel" class="more-options-label">Mais opções</label>
                    <div id="moreOptions" class="more-options" style="display: none;">
                        <label for="seventhSelect">7th:</label>
                        <select id="seventhSelect">
                            <option value="-">-</option>
                            <option value="majorSeventh">Major</option>
                            <option value="minorSeventh">Minor</option>
                        </select>

                        <label for="ninethSelect">9th:</label>
                        <select id="ninethSelect">
                            <option value="-">-</option>
                            <option value="majorNineth">Major</option>
                            <option value="minorNineth">Minor</option>
                        </select>

                        <label for="fifthSelect">5th:</label>
                        <select id="fifthSelect">
                            <option value="-">-</option>
                            <option value="sharpFifth">Sharp</option>
                            <option value="flatFifth">Flat</option>
                        </select>
                    </div>
                </div>
                <div class="modal-column-right">
                    <label for="chordSelect">Chord List:</label>
                    <select size="7" id="chordSelect">
                        <!-- Lista de acordes preenchida dinamicamente pelo JavaScript -->
                    </select>
                </div>
                <div class="clearfix"></div>
                <br>
                <button type="button" id="insertChordBtn">Confirm</button>
                <button type="button" id="cancelEdit" class="modal-btn">Cancel</button>
            </form>
        </div>
    </div>

    <br><br>

    <button id="saveChords">Salvar Acordes</button>

    <!-- Campo hidden para armazenar o ID da música -->
    <input type="hidden" id="idMusica" value="<?php echo htmlspecialchars($id_musica); ?>">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        const idMusica = <?php echo isset($_GET['id_musica']) ? $_GET['id_musica'] : 'null'; ?>;
    </script>

    <script src="script.js"></script>



</body>

</html>