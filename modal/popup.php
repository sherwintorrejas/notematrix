<div class="popup" id="add-note-popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Add Note</h2>
            <form id="add-note-form">
                <input type="text" name="title" placeholder="Title" required oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1)">
                <textarea name="text" placeholder="Enter your note here" required oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1)" ></textarea>
                <button type="submit" class="btn">SAVE</button>
            </form>
        </div>
    </div>

    <div class="popup" id="note-popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2 id="popup-title"></h2>
            <input type="text" id="popup-title-input" placeholder="Enter updated title" oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1)">
            <textarea id="popup-text" oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1)"></textarea>
            <button onclick="updateNote()" class="btn">SAVE</button>
        </div>
    </div>
    <div class="popup" id="view-popup">
        <div class="popup-content">
            <span class="close" onclick="closeViewPopup()">&times;</span>
            <h2 id="view-popup-title"></h2>
            <p id="view-popup-text"></p>
        </div>
    </div>

</div>