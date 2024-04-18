<?php
include_once 'connection/config.php';
include 'bars/time.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archive</title>
    <link rel="stylesheet" href="css/styl.css">
</head>
<body>
<?php include 'bars/sidebar.php'; ?>

<div class="content">
    <?php include 'bars/search.php'?>

    <h1>Archive</h1>
    <?php
    // Modified SQL query to fetch archived notes of the logged-in user
    $sql = "SELECT archive.archive_id, archive.note_id, notes.user_id, notes.title, notes.text, notes.updated_at 
    FROM archive
    INNER JOIN notes ON archive.note_id = notes.note_id
    WHERE user_id = $user_id
    AND NOT EXISTS (
        SELECT 1
        FROM deletednotes
        WHERE deletednotes.note_id = archive.note_id
    )"; 


    $result = mysqli_query($link, $sql);

    include 'modal/archivecard.php';
    ?>

    <div class="popup" id="note-popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2 id="popup-title"></h2>
            <input type="text" id="popup-title-input" placeholder="Enter updated title">
            <textarea id="popup-text"></textarea>
            <button onclick="updateNote()">Save</button>
        </div>
    </div>

    <div class="popup" id="view-popup">
        <div class="popup-content">
            <span class="close" onclick="closeViewPopup()">&times;</span>
            <h2 id="view-popup-title"></h2>
            <div id="view-popup-text"></div>
        </div>
    </div>

    <script>
        function toggleDropdown(element) {
            var dropdownMenu = element.nextElementSibling;
            dropdownMenu.classList.toggle('active');
        }

        // Close dropdowns when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown-toggle img')) {
                var dropdowns = document.getElementsByClassName("dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('active')) {
                        openDropdown.classList.remove('active');
                    }
                }
            }
        }

        function unarchiveNote(noteId) {
            // AJAX request to unarchive note
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "unarchive_note.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Reload page after unarchiving note
                    window.location.reload();
                }
            };
            xhr.send("note_id=" + noteId);
        }

        function viewNote(noteId) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    showViewPopup(data.title, data.text);
                }
            };
            xhr.open("POST", "view_note.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("note_id=" + noteId);
        }

        function showViewPopup(title, text) {
            document.getElementById("view-popup-title").textContent = title;
            document.getElementById("view-popup-text").textContent = text;
            document.getElementById("view-popup").style.display = "block";
        }

        function closeViewPopup() {
            document.getElementById("view-popup").style.display = "none";
        }

        function showPopup(id, title, text) {
            // Fetch the data from the server using AJAX
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    // Populate the popup fields with fetched data
                    document.getElementById("popup-title").textContent = "Edit Note";
                    document.getElementById("popup-title-input").value = data.title !== undefined ? data.title : '';
                    document.getElementById("popup-text").value = data.text !== undefined ? data.text : '';
                    document.getElementById("popup-text").setAttribute('data-id', id);
                    document.getElementById("note-popup").style.display = "block";
                }
            };
            xhr.open("POST", "fetch_note.php", true); // Create a new PHP file to fetch note data
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("note_id=" + id);
        }

        function closePopup() {
            document.getElementById("note-popup").style.display = "none";
        }

            // Function to update the note
        function updateNote() {
             var id = document.getElementById("popup-text").getAttribute('data-id');
             var text = document.getElementById("popup-text").value;
             var title = document.getElementById("popup-title-input").value;

             var xhr = new XMLHttpRequest();
             xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        closePopup();
                        location.reload(); // Reload the page after updating the note
                    }
                };
             xhr.open("POST", "update_note.php", true);
             xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
             xhr.send("id=" + id + "&title=" + title + "&text=" + text);
        }
        function trashNote(noteId) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            closePopup();
            location.reload();
        }
    };
    xhr.open("POST", "trash_note.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("note_id=" + noteId);
}
</script>
</body>
</html>


