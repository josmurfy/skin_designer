<?php
// Original: shopmanager/translate.php
namespace Opencart\Admin\Model\Shopmanager;

class Translate extends \Opencart\System\Engine\Model {
    public function translate(string $text_field, string $targetLanguage): ?string {
        try {
            $this->load->model('shopmanager/ai');

            // Detect if input is already JSON
            $decoded = json_decode($text_field, true);
            $isJson = (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded)));

            if ($isJson) {
                // Input is JSON — pass directly to AI translate
                $result = $this->model_shopmanager_ai->translate($text_field, $targetLanguage);
                if (is_string($result)) {
                    return $result;
                } elseif (is_array($result)) {
                    return json_encode($result, JSON_UNESCAPED_UNICODE);
                }
            } else {
                // Input is plain text — wrap in JSON array for AI, unwrap result
                $wrapped = json_encode([$text_field], JSON_UNESCAPED_UNICODE);
                $result = $this->model_shopmanager_ai->translate($wrapped, $targetLanguage);
                if (is_string($result)) {
                    $arr = json_decode($result, true);
                    if (is_array($arr) && isset($arr[0])) {
                        return $arr[0];
                    }
                    return $result;
                } elseif (is_array($result) && isset($result[0])) {
                    return $result[0];
                }
            }
            return null;
        } catch (\Throwable $e) {
            $this->log->write('Translate error: ' . $e->getMessage());
            return null;
        }
    }

    public function bypassTranslate(string $key, string $value): ?string {
        $value = html_entity_decode($value);
        $keyBypassWords = ['weight','dimension','quantity','size','length','width','height','depth','capacity','volume','power','voltage','current','temperature'];
        foreach ($keyBypassWords as $word) {
            if (stripos($key, $word) !== false) return $value;
        }
        $valueBypassWords = ['inch','inches','foot','feet','lb','lbs','oz','cm','mm','meter','meters','kilogram','kilograms','gram','grams','ml','liter','liters','watt','watts','kw','volt','volts','amp','amps','degree','degrees','rpm','gb','mb','tb','hz','mph','kmh','unit','page','pages'];
        foreach ($valueBypassWords as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $value)) return $value;
        }
        return null;
    }
}