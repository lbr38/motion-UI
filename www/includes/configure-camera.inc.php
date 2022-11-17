<div class="param-slide-container camera-configuration-div" camera-id="<?= $id ?>">
    <div class="param-slide">
        <img src="resources/icons/error-close.svg" class="hide-camera-configuration-btn close-btn pointer lowopacity" title="Close" camera-id="<?= $id ?>" />

        <h2 class="center">Configure <?= $this->name ?> camera</h2>

        <form class="edit-camera-configuration-form" camera-id="<?= $id ?>" autocomplete="off">
            <table>
                <tr>
                    <td class="td-30">Id</td>
                    <td>
                        <input type="text" value="<?= $id ?>" readonly />
                    </td>
                </tr>
                <tr>
                    <td class="td-30">Name</td>
                    <td>
                        <input type="text" name="camera-name" value="<?= $this->name ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="td-30">URL</td>
                    <td>
                        <input type="text" name="camera-url" value="<?= $this->url ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="td-30">Rotate</td>
                    <td>
                        <select name="camera-rotate">
                            <option value="0" <?php echo ($this->rotate == "0") ? 'selected' : '' ?>>0</option>
                            <option value="90" <?php echo ($this->rotate == "90") ? 'selected' : '' ?>>90</option>
                            <option value="180" <?php echo ($this->rotate == "180") ? 'selected' : '' ?>>180</option>
                            <option value="270" <?php echo ($this->rotate == "270") ? 'selected' : '' ?>>270</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="td-30">Refresh (sec.)</td>
                    <td>
                        <input type="number" name="camera-refresh" value="<?= $this->refresh ?>" />
                    </td>
                </tr>
            </table>
            <br>
            <button type="submit" class="btn-small-green">Save</button>
        </form>
    </div>
</div>