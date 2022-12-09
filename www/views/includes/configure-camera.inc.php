<div class="param-slide-container camera-configuration-div" camera-id="<?= $mycamera->getId() ?>">
    <div class="param-slide">
        <img src="resources/icons/error-close.svg" class="hide-camera-configuration-btn close-btn pointer lowopacity" title="Close" camera-id="<?= $mycamera->getId() ?>" />

        <h2 class="center">Configure <?= $mycamera->getName() ?> camera</h2>

        <form class="edit-camera-configuration-form" camera-id="<?= $mycamera->getId() ?>" autocomplete="off">
            <table>
                <tr>
                    <td class="td-30">Id</td>
                    <td>
                        <input type="text" value="<?= $mycamera->getId() ?>" readonly />
                    </td>
                </tr>
                <tr>
                    <td class="td-30">Name</td>
                    <td>
                        <input type="text" name="camera-name" value="<?= $mycamera->getName() ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="td-30">URL</td>
                    <td>
                        <input type="text" name="camera-url" value="<?= $mycamera->getUrl() ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="td-30">Rotate</td>
                    <td>
                        <select name="camera-rotate">
                            <option value="0" <?php echo ($mycamera->getRotate() == "0") ? 'selected' : '' ?>>0</option>
                            <option value="90" <?php echo ($mycamera->getRotate() == "90") ? 'selected' : '' ?>>90</option>
                            <option value="180" <?php echo ($mycamera->getRotate() == "180") ? 'selected' : '' ?>>180</option>
                            <option value="270" <?php echo ($mycamera->getRotate() == "270") ? 'selected' : '' ?>>270</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="td-30">Refresh (sec.)</td>
                    <td>
                        <input type="number" name="camera-refresh" value="<?= $mycamera->getRefresh() ?>" />
                    </td>
                </tr>
            </table>
            <br>
            <button type="submit" class="btn-small-green">Save</button>
        </form>
    </div>
</div>