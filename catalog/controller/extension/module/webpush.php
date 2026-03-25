<?php

class ControllerExtensionModuleWebpush extends Controller {

  public function inject_one_script(&$route, &$data){
    $appId = $this->config->get("module_webpush_appId");
    $bellStatus = $this->config->get("module_webpush_bellStatus") == "true" ? 'true' : 'false';
    $position = $this->config->get("module_webpush_position");
    $size = $this->config->get("module_webpush_size");
    $autoRegister = $this->config->get("module_webpush_autoRegister") == "true" ? 'true' : 'false';
    $status = $this->config->get("module_webpush_status");
    $lang = $this->language->get('code') ? $this->language->get('code') : 'en';

    if ($status) {
      $data["scripts"] []="https://cdn.onesignal.com/sdks/OneSignalSDK.js";
      $data["analytics"][] = "
      <script>
        var OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
          OneSignal.init({
            appId:'$appId' ,
            autoRegister: $autoRegister,
            notifyButton: {
              enable: $bellStatus,
              position:'$position' ,
              size:'$size'
            },
          });
          OneSignal.sendTag('lang', '$lang');
        });
      </script>";
      $data['links'][] = array(
        'href' => "/catalog/view/javascript/manifest.json",
        'rel'=>"manifest"
      );
    }
  }
}
