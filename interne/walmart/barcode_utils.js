(function(d, t) {
    var s = d.createElement(t);
        s.type = 'text/javascript';
        s.async = true;
        s.src = 'quagga.min.js'; 
    var r = d.getElementsByTagName(t)[0];
        r.parentNode.insertBefore(s, r);
}(document, 'script'));


var lastCode = "";
const QuaggaStateEnum = {"stop":1, "check":2, "init":3, "start":4};
var quaggaState = QuaggaStateEnum.stop;
function stop_barcodescan()
{
    Quagga.stop();
    quaggaState = QuaggaStateEnum.stop;
    $("#barcodeScan").hide();
    lastCode = "";
}

var quaggaDebug = false;
function quaggaLog(message)
{
    if(quaggaDebug)
    {
        console.log("Quagga: " + message);
    }
}
function quaggaError(err)
{
    let mess = "";
    if(err.name != undefined && err.message != undefined)
    {
        mess = err.name + ": " + err.message
    }
    else
    {
        mess = err;
    }
    
    dhtmlx.message({
        type: "error",
        text: mess
    });
}

function initQuagga(success)
{
    quaggaState = QuaggaStateEnum.init;
	Quagga.init({
		inputStream : {
		  name : "Live",
		  type : "LiveStream",
		  target: document.querySelector('#barcodeVid'),
		  constraints: {
            deviceId: getCameraDevideID(),
            facingMode: "environment",
            audio: false,
            focusMode: "manual",
            focusDistance: 0.15
		  }
		},
        locator: {
            patchSize: "medium",
            halfSample: true
        },
        locate: true,
		decoder : {
		  readers : ["upc_reader", "ean_reader"]
		}
	  }, function(err) {
		  if (err) {
              quaggaState = QuaggaStateEnum.stop;
              quaggaError(err);
			  return;
		  }
          setTimeout(function(){
              lastCode = "";
              $("#barcodeScan").show();
              Quagga.start();
              quaggaState = QuaggaStateEnum.start;
          
              Quagga.onDetected(function(data){
                  if(lastCode != data.codeResult.code)
                  {
                      lastCode = data.codeResult.code;
                      stop_barcodescan();
                      if(success != undefined)
                      {
                        success(data.codeResult.code);
                      }
                  }
              });
              
              var track = Quagga.CameraAccess.getActiveTrack();
              if (track && typeof track.getCapabilities === 'function') {
                  var capabilities = track.getCapabilities();
                  quaggaLog(JSON.stringify(capabilities));
                  //TODO: Figure out LG camera focus issue
                  if(capabilities.focusMode && capabilities.focusMode.indexOf("continuous") >= 0)
                  {
                    track.applyConstraints({focusMode: "continuous"})
                    .catch(e => quaggaError(e));
                  }
                  else if(capabilities.focusMode && capabilities.focusMode.indexOf("manual") >= 0 && capabilities.focusDistance)
                  {
                    track.applyConstraints({
                        focusMode: "manual",
                        focusDistance: (capabilities.focusDistance.min<0.15?0.15:capabilities.focusDistance.min)})
                    .catch(e => quaggaError(e));
                    /*
                    if(capabilities.zoom)
                    {
                    track.applyConstraints({
                      advanced: [{zoom: 3}]
                    })
                    .catch(e => quaggaError(e));
                    }
                    */
                  }
                  if(capabilities.torch)
                  {
                    camForm.uncheckItem("torch");
                    camForm.showItem("torch");
                  }
                  else
                  {
                      camForm.hideItem("torch");
                  }
              }
              
          }, 100);
	  });
}

function setCameraDevideID(camera)
{
    localStorage["cameraDevideID"] = camera;
}
function getCameraDevideID()
{
    return localStorage.getItem("cameraDevideID");
}
var camForm;
function scan_barcode(success)
{
    if(quaggaState != QuaggaStateEnum.stop)
    {
        quaggaLog("state is not stop, not starting again");
        return;
    }
    
    quaggaState = QuaggaStateEnum.check;
    if($("#barcodeScan").length == 0)
    {
        $("body").append('\
            <div id="barcodeScan" hidden="true">\
            <span id="barcodeVid" onclick="stop_barcodescan()"></span>\
            </div>');

        if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) 
        {
            navigator.mediaDevices.enumerateDevices()
            .then(function(devices) {
                quaggaLog(JSON.stringify(devices));
                let cam_opt = new Array();
                let selected = false;
                let savedDevice = getCameraDevideID();
                devices.forEach(function(device) {
                    //quaggaLog( JSON.stringify(device) );
                    if( device.kind == "videoinput"){
                        
                      if(!getCameraDevideID())
                          setCameraDevideID(device.deviceId);
                      let dev = {value: device.deviceId, selected: false, text: device.label};
                      if(!selected && (device.deviceId == savedDevice || (!savedDevice && device.label.match(/back/) != null)))
                      {
                        selected = true;
                        dev.selected = true;
                        setCameraDevideID(device.deviceId);
                      }
                      if(dev.text.length == 0)
                          dev.text = "Camera " + (cam_opt.length + 1);
                      cam_opt.push(dev);
                    }
                });
                if(cam_opt.length > 0)
                {
                    var formData = [
                        {
                            type: "settings",
                            position: "label-left",
                            labelWidth: 45
                        },
                        {type: "select", label: "Camera", name: "cam", options:cam_opt},
                        {type: "checkbox", label: "Torch", name: "torch", checked: false},
                        {type: "button", name:	"b_close_scan", value: 	"Close"}
                    ];
                    $("#barcodeScan").append("<div id='cam_form'></div>");
                    camForm = new dhtmlXForm("cam_form", formData);
                    camForm.attachEvent("onChange", function(name, value, is_checked){
                        if(name == "cam")
                        {
                          setCameraDevideID(value);
                          Quagga.stop();
                          quaggaLog('changed');
                          initQuagga(success);
                        }
                        else if(name == "torch")
                        {
                            var track = Quagga.CameraAccess.getActiveTrack();
                            if(track)
                                track.applyConstraints({advanced: [{torch: is_checked}]})
                                    .catch(e => quaggaError(e));
                        }
                    });
                    
                    camForm.attachEvent("onButtonClick", function(name) {
                        if(name == "b_close_scan")
                        {
                            stop_barcodescan();
                        }
                    });
                }
                
                if(!getCameraDevideID())
                {
                    quaggaState = QuaggaStateEnum.stop;
                    quaggaError("No video sources found");
                }
                else
                {
                    quaggaLog('enumerateDevices.then()');
                    initQuagga(success);
                }
            })
            .catch(function(err) {
                quaggaState = QuaggaStateEnum.stop;
                quaggaError(err);
            });
        }
        else
        {
            quaggaState = QuaggaStateEnum.stop;
            quaggaError("Could not enumerateDevices");
        }
    }
    else
    {
        quaggaLog('scan_barcode loaded devices');
        if(getCameraDevideID())
        {
            initQuagga(success);
        }
        else
        {
            quaggaState = QuaggaStateEnum.stop;
            quaggaError("No video sources found");
        }
    }
}