<?php
// Error handling
    if (!$result) {
        echo "Error: " . mysqli_error($link);
        exit(); // Stop script execution if there's an error
    }
    $i = 0;
    if (mysqli_num_rows($result) > 0) {
        echo '<div class="row">';
        while ($row = mysqli_fetch_assoc($result)) {
            $scheduled_deletion = new DateTime($row['scheduled_permanent_deletion']);
            $current_date = new DateTime();
            $difference = $current_date->diff($scheduled_deletion)->days;

            if ($i % 4 == 0 && $i != 0) {
                echo '</div><div class="row">';
            }

            echo "<div class='card' id='note_" . $row['note_id'] . "'>";
echo "<h2>" . $row['title'] . "</h2>";
echo "<div class='card-content'>" . (strlen($row['text']) > 60 ? substr($row['text'], 0, 60) . "..." : $row['text']) . "</div>";
echo "<div class='card-actions'>";
echo "<div class='tooltip-container'>";
echo "<img src='icons/mark.png' alt='Tooltip' class='tooltip-icon'>";
echo "<span class='tooltip'>" . $difference . " days until permanent deletion</span>";
echo "</div>";
echo "<div class='dropdown'>";
echo "<div class='dropdown-toggle' onclick='toggleDropdown(this)'><img src='icons/more.png' alt='Dropdown'></div>";
echo "<div class='dropdown-menu'>";
echo "<div class='dropdown-menu-item' onclick='confirmDelete(" . $row['note_id'] . ")'>Delete</div>";
echo "<div class='dropdown-menu-item' onclick='restoreNote(". $row['note_id'] . ")'>Restore</div>";
echo "<div class='dropdown-menu-item' onclick='viewNote(" . $row['note_id'] . ")'>View</div>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";


            $i++;
        }
        echo '</div>';
    } else {
        echo "No notes found.";
    } 
    mysqli_close($link);
    ?>