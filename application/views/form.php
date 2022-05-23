    <section>
        <div style="text-align:center">
            <h1>Scan Docs and Images from Javascript</h1>
            <hr />
            <label>Scanner:</label>
            <select id="scannerName"></select>
            <label>Resolution (DPI):</label>
            <input type="text" id="resolution" value="200" />
            <label>Pixel Mode:</label>
            <select id="pixelMode">
                <option>Grayscale</option>
                <option selected>Color</option>
            </select>
            <label>Image Format:</label>
            <select id="imageFormat">
                <option selected>JPG</option>
                <option>PNG</option>
            </select>

            <hr />
            <div>
                <button onclick="doScanning();">Scan Now...</button>
            </div>
            <br />

            <img id="scanOutput"></img>
            <iframe id="outputUpload" style="display: none; width: 100%; height: 100%;"></iframe>
            <form id="form1" action="#" method="POST" enctype="multipart/form-data" target="_blank">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" id="csrf" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="file" id="file_scan" style="display: none;">
                <input type="button" class="btn btn-primary" value="Submit" id="submitForm">
            </form>

        </div>
    </section>