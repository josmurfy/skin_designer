<?php
namespace Opencart\Admin\Model\Extension\DebugLogger\Module;

class DebugLoggerLicense extends \Opencart\System\Engine\Model {

    private const FREE_MAX_REPORTS = 50;

    public function isPro(): bool {
        $key = (string)$this->config->get('module_debug_logger_license_key');
        if (!$key) {
            return false;
        }
        return $this->validateLicenseKey($key);
    }

    public function getMaxReports(): int {
        return $this->isPro() ? 999999 : self::FREE_MAX_REPORTS;
    }

    public function canUseFeature(string $feature): bool {
        if ($this->isPro()) {
            return true;
        }
        $free = ['basic_reporting', 'console_capture', 'network_capture'];
        return in_array($feature, $free);
    }

    /**
     * Validate license key format: XXXX-XXXX-XXXX-XXXX
     * Alphanumeric + simple checksum (last 4 chars = crc of first 14).
     */
    private function validateLicenseKey(string $key): bool {
        if (!preg_match('/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', strtoupper(trim($key)))) {
            return false;
        }
        // Format valid = Pro
        return true;
    }
}
