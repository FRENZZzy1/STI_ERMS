<!-- UPDATE MODAL -->
<div id="updateModal" class="modal">
    <form class="modal-form" method="POST" onsubmit="return confirmUpdate();">
        <h3>Update Equipment</h3>
        <input type="hidden" name="equipment_id" id="modal_equipment_id">

        <div class="form-group">
            <label>Barcode</label>
            <input type="text" name="barcode" id="modal_barcode" required>
        </div>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" id="modal_name" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="modal_description" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label>Lab ID</label>
            <input type="text" name="lab_id" id="modal_lab_id" required>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" id="modal_status" required>
                <option value="">-- Select Status --</option>
                <option value="Good">Good</option>
                <option value="Damaged">Damaged</option>
                <option value="Under Maintenance">Under Maintenance</option>
            </select>
        </div>

        <div class="modal-buttons">
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        </div>
    </form>
</div>

<!-- ADD MODAL -->
<div id="addModal" class="modal">
    <form class="modal-form" method="POST" onsubmit="return confirmAdd();">
        <h3>Add Equipment</h3>

        <div class="form-group">
            <label>QR CODE</label>
            <input type="text" name="barcode" id="add_barcode" required>
        </div>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" id="add_name" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="add_description" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label>Lab ID</label>
            <input type="text" name="lab_id" id="add_lab_id" value="<?= htmlspecialchars($lab) ?>" readonly required>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" id="add_status" required>
                <option value="">-- Select Status --</option>
                <option value="Good">Good</option>
                <option value="Archived">Archived</option>
                <option value="Under Maintenance">Not Functional</option>
            </select>
        </div>

        <div class="modal-buttons">
            <button type="submit" class="btn btn-primary">Add</button>
            <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
        </div>
    </form>
</div>

<!-- OVERLAYS -->
<div id="overlay" class="overlay" onclick="closeModal()"></div>
<div id="overlayAdd" class="overlay" onclick="closeAddModal()"></div>
