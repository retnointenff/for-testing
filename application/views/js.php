<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.5/bluebird.min.js"></script>
<script src="<?= base_url(); ?>assets/js/JSPrintManager.js"></script>

<script>
    var scannerDevices = null;
    var _this = this;
    let imgVal;

    //JSPrintManager WebSocket settings
    JSPM.JSPrintManager.auto_reconnect = true;
    JSPM.JSPrintManager.start();
    JSPM.JSPrintManager.WS.onStatusChanged = function() {
        if (jspmWSStatus()) {
            //get scanners
            JSPM.JSPrintManager.getScanners().then(function(scannersList) {
                scannerDevices = scannersList;
                var options = '';
                for (var i = 0; i < scannerDevices.length; i++) {
                    options += '<option>' + scannerDevices[i] + '</option>';
                }
                $('#scannerName').html(options);
            });
        }
    };

    //Check JSPM WebSocket status
    function jspmWSStatus() {
        if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open)
            return true;
        else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Closed) {
            console.warn('JSPrintManager (JSPM) is not installed or not running! Download JSPM Client App from https://neodynamic.com/downloads/jspm');
            return false;
        } else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Blocked) {
            alert('JSPM has blocked this website!');
            return false;
        }
    }

    //Do scanning...
    function doScanning() {
        if (jspmWSStatus()) {

            //create ClientScanJob
            var csj = new JSPM.ClientScanJob();
            //scanning settings
            csj.scannerName = $('#scannerName').val();
            csj.pixelMode = JSPM.PixelMode[$('#pixelMode').val()];
            csj.resolution = parseInt($('#resolution').val());
            csj.imageFormat = JSPM.ScannerImageFormatOutput[$('#imageFormat').val()];

            let _this = this;
            //get output image
            csj.onUpdate = (data, last) => {
                if (!(data instanceof Blob)) {
                    console.info(data);
                    return;
                }
                var data_type = 'image/jpg';
                if (csj.imageFormat == JSPM.ScannerImageFormatOutput.PNG) data_type = 'image/png';

                var imgBlob = new Blob([data], {
                    type: data_type
                });

                if (imgBlob.size == 0) return;

                //create html image obj from scan output
                var img = URL.createObjectURL(imgBlob, {
                    type: data_type
                });

                //scale original image to be screen size friendly
                var imgScale = {
                    width: Math.round(96.0 / csj.resolution * 100.0) + "%",
                    height: 'auto'
                };
                $('#scanOutput').css(imgScale);
                $('#scanOutput').attr("src", img);

                imgVal = imgBlob;
                $("#file_scan").val(data);
            }

            csj.onError = function(data, is_critical) {
                console.error(data);
            };

            //Send scan job to scanner!
            csj.sendToClient().then(data => console.info(data));

        }
    }

    console.log(imgVal);
    let csrf_token_name = '<?= $csrf_token_name ?>';
    let csrf_hash = '<?= $csrf_hash ?>';

    $("#submitForm").on('click', () => {
        var reader = new FileReader();
        // this function is triggered once a call to readAsDataURL returns

        reader.onload = function(event) {
            var fd = new FormData();
            fd.append($("#csrf").attr("name"), $("#csrf").val());
            fd.append('action', $("input[name=action]").val());
            // fd.append('file', imgVal);
            fd.append('data', event.target.result);
            for (var value of fd.values()) {
                console.log(value);
            }
            $.ajax({
                type: 'POST',
                url: '<?= $base_url . $page . '/uploadFileScan' ?>',
                data: fd,
                processData: false,
                contentType: false
            }).done(function(data) {
                // print the output from the upload.php script
                console.log(JSON.parse(data));
                // $('#scanOutput').attr("src", "");
                // $('#outputUpload').css("display", "block");
                // $('#outputUpload').attr("src", JSON.parse(data));
            });
        };
        // trigger the read from the reader...
        reader.readAsDataURL(imgVal);
    })
</script>