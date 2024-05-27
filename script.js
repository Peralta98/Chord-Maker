$(document).ready(function() {
    let currentEditingItem = null;
    let compassoCounter = 1; // Variable to identify each bar

    const naturalChords = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
    const flatChords = ['A♭', 'B♭', 'C♭', 'D♭', 'E♭', 'F♭', 'G♭'];
    const sharpChords = ['A♯', 'B♯', 'C♯', 'D♯', 'E♯', 'F♯', 'G♯'];

    const majorSuffix = '';
    const minorSuffix = '-';

    // Function to get chords based on note type and chord type
    function getChords(noteType, chordType) {
        let chords;
        switch (noteType) {
            case 'naturalNotes':
                chords = naturalChords;
                break;
            case 'flatNotes':
                chords = flatChords;
                break;
            case 'sharpNotes':
                chords = sharpChords;
                break;
            default:
                chords = [];
        }
        return chords.map(chord => chord + (chordType === 'majorChord' ? majorSuffix : minorSuffix));
    }

    // Function to create a grid item (compasso)
    function createGridItem(isAddButton = false) {
        const newItem = $('<div>', {
            class: 'grid-item',
            id: isAddButton ? '' : 'bar-' + compassoCounter++ // Assign unique ID if not an add button
        });

        if (!isAddButton) {
            const editIcon = $('<i>', {
                class: 'fas fa-pen-to-square edit-icon',
                style: 'display: none; left: 10px; top: 10px;'
            }).click(editIconClick);

            const deleteIcon = $('<i>', {
                class: 'fas fa-trash-alt delete-icon',
                style: 'display: none; right: 10px; top: 10px;'
            }).click(deleteIconClick);

            newItem.append(editIcon);
            newItem.append(deleteIcon);
        } else {
            newItem.addClass('add');
            newItem.append('<i class="fas fa-plus"></i>');
        }

        return newItem;
    }

    // Function to handle the edit icon click event
    function editIconClick(event) {
        event.stopPropagation();
        currentEditingItem = $(this).closest('.grid-item');
        $('#editModal').css('display', 'block');
        fillChordSelect();
    }

    // Function to handle the delete icon click event
    function deleteIconClick(event) {
        event.stopPropagation();
        const gridItem = $(this).closest('.grid-item');
        $('#confirmModal').css('display', 'block');
        $('#confirmDelete').off('click').on('click', function() {
            const compassoId = gridItem.attr('id');
            gridItem.remove();
            saveRemovedCompasso(compassoId); // Save removal to database
            $('#confirmModal').css('display', 'none');
        });
        $('#cancelDelete').off('click').on('click', function() {
            $('#confirmModal').css('display', 'none');
        });
    }

    // Function to save the removed bar to the database
    function saveRemovedCompasso(compassoId) {
        const idMusica = $('#idMusica').val();
        if (!idMusica) {
            console.error('Music ID not found. Please create a song first.');
            return;
        }

        $.ajax({
            type: "POST",
            url: "delete_compasso.php",
            data: {
                id_musica: idMusica,
                compasso_id: compassoId
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error("Error deleting bar from database:", error);
            }
        });
    }

    // Function to save chords to the database
    $('#saveChords').click(function() {
        const acordes = {};
        $('.grid-item').each(function() {
            const compassoId = $(this).attr('id');
            const chordText = $(this).text();
            if (compassoId && chordText) {
                acordes[compassoId] = chordText;
            }
        });

        const idMusica = $('#idMusica').val();
        if (!idMusica) {
            alert('Music ID not found. Please create a song first.');
            return;
        }

        $.ajax({
            type: "POST",
            url: "save_chords.php",
            data: {
                acordes: JSON.stringify(acordes),
                id_musica: idMusica
            },
            success: function(response) {
                alert(response);
            },
            error: function(xhr, status, error) {
                console.error("Error saving chords to database:", error);
            }
        });
    });

    // Check if the music ID was passed in the URL
    const idMusica = new URLSearchParams(window.location.search).get('id_musica');
    if (idMusica !== null) {
        $.ajax({
            type: "GET",
            url: "get_chords.php",
            data: { id_musica: idMusica },
            success: function(response) {
                const acordes = JSON.parse(response);
                for (const compassoId in acordes) {
                    const acorde = acordes[compassoId];
                    const compasso = $(`#${compassoId}`);
                    compasso.text(acorde);
                    addIcons(compasso);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error retrieving chords from database:", error);
            }
        });
    } else {
        console.error("Music ID not provided in the URL.");
    }

    // Function to populate the chord select dropdown
    function fillChordSelect() {
        $('#chordSelect').empty();
        const noteType = $('#noteType').val();
        const chordType = $('#chordType').val();
        const chords = getChords(noteType, chordType);
        chords.forEach(function(chord) {
            $('#chordSelect').append($('<option>', {
                value: chord,
                text: chord
            }));
        });
    }

    $('#noteType, #chordType').change(fillChordSelect);

    // Toggle more options section
    $('#moreOptionsLabel').click(function() {
        $('#moreOptions').toggle();
    });

    // Function to handle inserting a chord
    $('#insertChordBtn').click(function() {
        const selectedChord = $('#chordSelect').val();
        if (!selectedChord) {
            alert('Please select a chord before inserting.');
            return;
        }

        let chordText = selectedChord;
        if ($('#seventhSelect').val() === 'majorSeventh') {
            chordText += 'Δ';
        } else if ($('#seventhSelect').val() === 'minorSeventh') {
            chordText += '7';
        }

        if ($('#ninethSelect').val() === 'majorNineth') {
            chordText += '9';
        } else if ($('#ninethSelect').val() === 'minorNineth') {
            chordText += '(♭9)';
        }

        if ($('#fifthSelect').val() === 'sharpFifth') {
            chordText += '(♯5)';
        } else if ($('#fifthSelect').val() === 'flatFifth') {
            chordText += '(♭5)';
        }

        currentEditingItem.empty().append($('<span>', {
            class: 'chord-text',
            text: chordText
        }));

        addIcons(currentEditingItem);

        $('#editModal').css('display', 'none');
        resetForm();
    });

    // Function to add edit and delete icons to a bar
    function addIcons(compasso) {
        const editIcon = $('<i>', {
            class: 'fas fa-pen-to-square edit-icon',
            style: 'display: none; left: 10px; top: 10px;'
        }).click(editIconClick);

        const deleteIcon = $('<i>', {
            class: 'fas fa-trash-alt delete-icon',
            style: 'display: none; right: 10px; top: 10px;'
        }).click(deleteIconClick);

        compasso.append(editIcon);
        compasso.append(deleteIcon);
    }

    $('#cancelEdit').click(function() {
        $('#editModal').css('display', 'none');
    });

    // Close modals when clicking outside of them
    $(document).on('click', function(event) {
        if (!$(event.target).closest('#editModal .modal-content, #confirmModal .modal-content').length) {
            $('#editModal').css('display', 'none');
            $('#confirmModal').css('display', 'none');
        }
    });

    // Function to reset the form in the edit modal
    function resetForm() {
        $('#noteType').val('naturalNotes');
        $('#chordType').val('majorChord');
        $('#seventhSelect').val('-');
        $('#ninethSelect').val('-');
        $('#fifthSelect').val('-');
        fillChordSelect();
        $('#moreOptions, #seventhOptions, #ninethOptions, #fifthOptions').hide();
    }

    // Handle the click event to add a new bar
    $(document).on('click', '.grid-item.add', function() {
        const newItem = createGridItem();
        $('.grid-container').append(newItem);
        $(this).remove();
        appendAddButton();
    });

    // Function to append the add button to the grid
    function appendAddButton() {
        const addButton = createGridItem(true);
        $('.grid-container').append(addButton);
    }

    // Initialize the grid with 8 bars
    const initialItems = 8;
    const gridContainer = $('.grid-container');
    for (let i = 1; i <= initialItems; i++) {
        gridContainer.append(createGridItem());
    }
    appendAddButton();
    fillChordSelect();

    // Show icons on hover
    $(document).on('mouseenter', '.grid-item:not(.add)', function() {
        $(this).find('.edit-icon, .delete-icon').show();
    });

    // Hide icons when not hovering
    $(document).on('mouseleave', '.grid-item:not(.add)', function() {
        $(this).find('.edit-icon, .delete-icon').hide();
    });
});
