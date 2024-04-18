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
</head>
<body>
<?php include 'bars/sidebar.php'; ?>

<div class="content">
    <?php include 'bars/search.php'?>

    <h1>Dashboard</h1>
    <?php
    $sql = "SELECT * FROM notes 
            WHERE user_id = $user_id 
            AND note_id NOT IN (SELECT note_id FROM archive) 
            AND note_id NOT IN (SELECT note_id FROM deletednotes)";
    $result = mysqli_query($link, $sql);

    include 'modal/card.php';
    ?>

    <div class="add-note-icon">
        <img src="icons/add.png" alt="Add Note" id="add-note-button" onclick="showAddNotePopup()">
    </div>

    <?php include 'modal/popup.php';?>
<script>
    function showPopup(id, title, text) {
        document.getElementById("popup-title").textContent = title;
        document.getElementById("popup-text").value = text;
        document.getElementById("popup-title-input").value = title;
        document.getElementById("popup-text").setAttribute('data-id', id);
        document.getElementById("note-popup").style.display = "block";
    }

    function showAddNotePopup() {
        document.getElementById("add-note-popup").style.display = "block";
    }

    function closePopup() {
        document.getElementById("add-note-popup").style.display = "none";
        document.getElementById("note-popup").style.display = "none";
    }

    var activeDropdown = null;

function closeDropdowns() {
    var dropdowns = document.querySelectorAll(".dropdown-menu.active");
    dropdowns.forEach(function(dropdown) {
        dropdown.classList.remove('active');
    });
}

function toggleDropdown(element) {
    var dropdownMenu = element.nextElementSibling;
    if (dropdownMenu !== activeDropdown) {
        closeDropdowns();
        dropdownMenu.classList.add('active');
        activeDropdown = dropdownMenu;
    } else {
        dropdownMenu.classList.remove('active');
        activeDropdown = null;
    }
}

// Close dropdowns when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.dropdown-toggle img')) {
        closeDropdowns();
        activeDropdown = null;
    }
}

    document.getElementById("add-note-form").addEventListener("submit", function(event) {
        event.preventDefault();
        var form = this;
        var formData = new FormData(form);
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                closePopup();
                location.reload();
            }
        };
        xhr.open("POST", "add_note.php", true);
        xhr.send(formData);
    });

    function updateNote() {
        var id = document.getElementById("popup-text").getAttribute('data-id');
        var text = document.getElementById("popup-text").value;
        var title = document.getElementById("popup-title-input").value;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                closePopup();
                location.reload();
            }
        };
        xhr.open("POST", "update_note.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("id=" + id + "&title=" + title + "&text=" + text);
    }

    function archiveNote(noteId) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                closePopup();
                location.reload();
            }
        };
        xhr.open("POST", "archive_note.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
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


