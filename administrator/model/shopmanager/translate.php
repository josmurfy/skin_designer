<?php
namespace Opencart\Admin\Model\Shopmanager;

class Translate extends \Opencart\System\Engine\Model {
    public function translate(string $text_field, string $targetLanguage): ?string {
        try {
            $cred = getenv('GOOGLE_APPLICATION_CREDENTIALS') ?: (DIR_STORAGE . 'credentials/translate.json');
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $cred);

            $translate = new TranslateClient();
            $result = $translate->translate($text_field, ['target' => $targetLanguage]);

            return isset($result['text']) ? addslashes($result['text']) : null;
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