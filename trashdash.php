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
    <title>Home Page</title>
    <link rel="stylesheet" href="css/styl.css">
    <link rel="stylesheet" href="css/tooltip.css">
</head>
<body>
<?php include 'bars/sidebar.php'; ?>

<div class="content">
    <?php include 'bars/search.php'?>

    <h1>TRASH</h1>
    <?php
    $sql = "SELECT dn.deleted_note_id, dn.note_id, dn.deleted_at, dn.scheduled_permanent_deletion,
                n.title, n.text, n.created_at, n.updated_at
            FROM deletednotes dn
            JOIN notes n ON dn.note_id = n.note_id
            WHERE user_id = $user_id";
    $result = mysqli_query($link, $sql);

    include 'modal/trashcard.php';
    ?>
</div>
<?php include 'modal/popup.php';?>
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
    
    function confirmDelete(noteId) {
        if (confirm("Are you sure you want to delete this note?")) {
            // If user confirms deletion, proceed with the deletion
            deleteNote(noteId);
            location.reload();
        }
    }

    function deleteNote(noteId) {
        // Send AJAX request to delete the note
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // If deletion is successful, reload the page to reflect changes
                    location.reload();
                } else {
                    // If deletion fails, display an error message
                    alert("Failed to delete note: " + response.error);
                }
            }
        };
        xhr.open("POST", "delete_note.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("note_id=" + noteId);
    }

    function restoreNote(noteId) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // If restoration is successful, reload the page to reflect changes
                    location.reload();
                } else {
                    // If restoration fails, display an error message
                    alert("Failed to restore note: " + response.error);
                }
            }
        };
        xhr.open("POST", "restore_note.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("note_id=" + noteId);
    }


</script>
</body>
</html>
