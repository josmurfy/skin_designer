<?php
/**
 * Migration des controllers Shopmanager vers OpenCart 4.x avec logs/erreurs visibles.
 * Compatible CLI et Web (pas d'usage de STDOUT/STDERR constants).
 */

$dir = '/home/n7f9655/public_html/phoenixliquidation/administrator/controller/shopmanager';
$logFile = __DIR__ . '/migrate_shopmanager_oc4.error.log';

// Flux de sortie compatibles (CLI → stdout/stderr, Web → output)
$OUT = fopen((PHP_SAPI === 'cli') ? 'php://stdout' : 'php://output', 'w');
$ERR = fopen((PHP_SAPI === 'cli') ? 'php://stderr' : 'php://output', 'w');

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', $logFile);

// Helpers d'affichage
function info($msg) { global $OUT; fwrite($OUT, "[INFO] $msg\n"); }
function warn($msg) { global $OUT; fwrite($OUT, "[WARN] $msg\n"); }
function err($msg)  { global $ERR; fwrite($ERR, "[ERROR] $msg\n"); error_log("[ERROR] $msg"); }

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) return false;
    err("PHP Error: $message in $file:$line");
    return true;
});

set_exception_handler(function ($ex) {
    err("EXCEPTION: " . $ex->getMessage() . " in " . $ex->getFile() . ":" . $ex->getLine());
});

function canShellExec() {
    if (!function_exists('shell_exec')) return false;
    $disabled = ini_get('disable_functions');
    return stripos($disabled, 'shell_exec') === false;
}

function runLint($file) {
    if (!canShellExec()) return "Lint ignoré (shell_exec indisponible).";
    $cmd = 'php -l ' . escapeshellarg($file) . ' 2>&1';
    $out = shell_exec($cmd);
    return $out === null ? 'Impossible d’exécuter php -l' : trim($out);
}

if (!is_dir($dir)) {
    err("Répertoire introuvable: $dir");
    exit(1);
}

$files = glob($dir . '/*.php');
if (!$files) {
    warn("Aucun fichier .php trouvé dans: $dir");
    exit(0);
}

$skipPatterns = [
    '/\bcopy\.php$/i',
];

function shouldSkip($file, $patterns) {
    foreach ($patterns as $p) if (preg_match($p, $file)) return true;
    return false;
}

function toPascalCaseFromFilename($file) {
    $name = preg_replace('/\.php$/', '', basename($file));
    $name = preg_replace('/[^A-Za-z0-9_]+/', '_', $name);
    return str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($name))));
}

$actionNames = '(index|add|edit|delete|save|list|autocomplete|install|uninstall|validate|export|import|refresh|load|getList|getForm|download|upload)';

foreach ($files as $file) {
    $base = basename($file);
    if (shouldSkip($file, $skipPatterns)) {
        info("Skip (copie): $base");
        continue;
    }

    $original = @file_get_contents($file);
    if ($original === false) {
        err("Lecture impossible: $base");
        continue;
    }

    // Lint avant modifs
    $lintBefore = runLint($file);
    if (stripos($lintBefore, 'No syntax errors detected') === false) {
        warn("Lint avant modifs ($base): $lintBefore");
    }

    $updated = str_replace(["\r\n", "\r"], "\n", $original);
    if (trim($updated) === '') {
        warn("Fichier vide: $base");
        continue;
    }

    // Retirer un namespace existant pour repartir propre
    $updated = preg_replace('#^\s*<\?php\s*namespace\s+[^;]+;\s*#i', "<?php\n", $updated);

    $classNameFromFile = toPascalCaseFromFilename($file);
    $replacedLegacyHeader = false;

    // Remplacer "class ControllerShopmanagerX extends Controller"
    $updated = preg_replace_callback(
        '#class\s+ControllerShopmanager([A-Za-z0-9_]+)\s+extends\s+Controller#i',
        function ($m) {
            $short = $m[1];
            return "namespace Opencart\\Admin\\Controller\\Shopmanager;\n\nclass $short extends \\Opencart\\System\\Engine\\Controller";
        },
        $updated,
        -1,
        $countLegacyHeader
    );
    if ($countLegacyHeader > 0) $replacedLegacyHeader = true;

    // Injecter namespace si manquant
    if (!$replacedLegacyHeader && !preg_match('#^\s*namespace\s+Opencart\\\Admin\\\Controller\\\Shopmanager;#mi', $updated)) {
        if (preg_match('#class\s+[A-Za-z0-9_]+\s+extends\s+\\\\?Opencart\\\\System\\\\Engine\\\\Controller#i', $updated)) {
            $updated = preg_replace('#^\s*<\?php#', "<?php\nnamespace Opencart\\Admin\\Controller\\Shopmanager;\n", $updated, 1);
        } else {
            if (preg_match('#class\s+ControllerShopmanager([A-Za-z0-9_]+)\s+extends\s+Controller#i', $updated, $m)) {
                $short = $m[1];
                $updated = preg_replace(
                    '#class\s+ControllerShopmanager([A-Za-z0-9_]+)\s+extends\s+Controller#i',
                    "namespace Opencart\\Admin\\Controller\\Shopmanager;\n\nclass $short extends \\Opencart\\System\\Engine\\Controller",
                    $updated,
                    1
                );
                $replacedLegacyHeader = true;
            } else {
                if (preg_match('#^\s*<\?php#', $updated)) {
                    $updated = preg_replace('#^\s*<\?php#', "<?php\nnamespace Opencart\\Admin\\Controller\\Shopmanager;\n", $updated, 1);
                }
            }
        }
    }

    // Uniformiser "extends Controller" -> OC4 Controller
    if (!preg_match('#class\s+[A-Za-z0-9_]+\s+extends\s+\\\\?Opencart\\\\System\\\\Engine\\\\Controller#i', $updated)
        && preg_match('#class\s+([A-Za-z0-9_]+)\s+extends\s+Controller#i', $updated, $m)) {
        $short = $m[1];
        if (!preg_match('#^\s*namespace\s+Opencart\\\Admin\\\Controller\\\Shopmanager;#mi', $updated)) {
            $updated = preg_replace('#^\s*<\?php#', "<?php\nnamespace Opencart\\Admin\\Controller\\Shopmanager;\n", $updated, 1);
        }
        $updated = preg_replace(
            '#class\s+([A-Za-z0-9_]+)\s+extends\s+Controller#i',
            "class $short extends \\Opencart\\System\\Engine\\Controller",
            $updated,
            1
        );
    }

    // Typage ": void" sur actions courantes
    $updated = preg_replace(
        '#public\s+function\s+(?!__)' . $actionNames . '\s*\(([^)]*)\)\s*(?!:)\s*\{#i',
        'public function $1($2): void {',
        $updated
    );

    // token -> user_token
    $updated = str_replace(["['token']", '["token"]'], ["['user_token']", '["user_token"]'], $updated);
    $updated = preg_replace('#([?&])token=#i', '$1user_token=', $updated);

    if ($updated === $original) {
        info("Aucun changement: $base");
        continue;
    }

    // Sauvegarde + écriture
    $bak = $file . '.bak';
    if (!@copy($file, $bak)) {
        err("Backup échoué: $base");
        continue;
    }
    if (@file_put_contents($file, $updated) === false) {
        err("Écriture échouée: $base (restauration .bak)");
        @copy($bak, $file);
        continue;
    }

    // Lint après écriture
    $lintAfter = runLint($file);
    if (stripos($lintAfter, 'No syntax errors detected') === false) {
        err("Syntax error après migration ($base): $lintAfter");
        @copy($bak, $file);
        info("Restauration .bak effectuée: $base");
        continue;
    }

    info("Migré: $base");
}

info("Terminé. Journal d’erreurs: $logFile");