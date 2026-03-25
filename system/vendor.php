<?php
// aws/aws-crt-php

// aws/aws-sdk-php
$autoloader->register('Aws', DIR_STORAGE . 'vendor/aws/aws-sdk-php/src/', true);
if (is_file(DIR_STORAGE . 'vendor/aws/aws-sdk-php/src/functions.php')) {
	require_once(DIR_STORAGE . 'vendor/aws/aws-sdk-php/src/functions.php');
}

// brick/math
$autoloader->register('Brick\Math', DIR_STORAGE . 'vendor/brick/math/src/', true);

// firebase/php-jwt
$autoloader->register('Firebase\JWT', DIR_STORAGE . 'vendor/firebase/php-jwt/src/', true);

// gadoma/walmart-api-php-client
$autoloader->register('WalmartApiClient', DIR_STORAGE . 'vendor/gadoma/walmart-api-php-client/src/WalmartApiClient/', true);

// google/apiclient-services
$autoloader->register('Google\Service', DIR_STORAGE . 'vendor/google/apiclient-services/src/', true);
if (is_file(DIR_STORAGE . 'vendor/google/apiclient-services/autoload.php')) {
	require_once(DIR_STORAGE . 'vendor/google/apiclient-services/autoload.php');
}

// google/apiclient
$autoloader->register('Google', DIR_STORAGE . 'vendor/google/apiclient/src/', true);
if (is_file(DIR_STORAGE . 'vendor/google/apiclient/src/aliases.php')) {
	require_once(DIR_STORAGE . 'vendor/google/apiclient/src/aliases.php');
}

// google/auth
$autoloader->register('Google\Auth', DIR_STORAGE . 'vendor/google/auth/src/', true);

// google/cloud-core
$autoloader->register('Google\Cloud\Core', DIR_STORAGE . 'vendor/google/cloud-core/src/', true);

// google/cloud-translate
$autoloader->register('Google\Cloud\Translate', DIR_STORAGE . 'vendor/google/cloud-translate/src/', true);
$autoloader->register('GPBMetadata\Google\Cloud\Translate', DIR_STORAGE . 'vendor/google/cloud-translate/metadata/', true);

// google/cloud-vision
$autoloader->register('Google\Cloud\Vision', DIR_STORAGE . 'vendor/google/cloud-vision/src/', true);
$autoloader->register('GPBMetadata\Google\Cloud\Vision', DIR_STORAGE . 'vendor/google/cloud-vision/metadata/', true);

// google/common-protos
$autoloader->register('Google\Api', DIR_STORAGE . 'vendor/google/common-protos/src/Api/', true);
$autoloader->register('Google\Cloud', DIR_STORAGE . 'vendor/google/common-protos/src/Cloud/', true);
$autoloader->register('Google\Iam', DIR_STORAGE . 'vendor/google/common-protos/src/Iam/', true);
$autoloader->register('Google\Rpc', DIR_STORAGE . 'vendor/google/common-protos/src/Rpc/', true);
$autoloader->register('Google\Type', DIR_STORAGE . 'vendor/google/common-protos/src/Type/', true);
$autoloader->register('GPBMetadata\Google\Api', DIR_STORAGE . 'vendor/google/common-protos/metadata/Api/', true);
$autoloader->register('GPBMetadata\Google\Cloud', DIR_STORAGE . 'vendor/google/common-protos/metadata/Cloud/', true);
$autoloader->register('GPBMetadata\Google\Iam', DIR_STORAGE . 'vendor/google/common-protos/metadata/Iam/', true);
$autoloader->register('GPBMetadata\Google\Logging', DIR_STORAGE . 'vendor/google/common-protos/metadata/Logging/', true);
$autoloader->register('GPBMetadata\Google\Rpc', DIR_STORAGE . 'vendor/google/common-protos/metadata/Rpc/', true);
$autoloader->register('GPBMetadata\Google\Type', DIR_STORAGE . 'vendor/google/common-protos/metadata/Type/', true);

// google/gax
$autoloader->register('Google\ApiCore', DIR_STORAGE . 'vendor/google/gax/src/', true);
$autoloader->register('GPBMetadata\ApiCore', DIR_STORAGE . 'vendor/google/gax/metadata/ApiCore/', true);

// google/grpc-gcp
$autoloader->register('Grpc\Gcp', DIR_STORAGE . 'vendor/google/grpc-gcp/src/', true);

// google/longrunning
$autoloader->register('Google\ApiCore\LongRunning', DIR_STORAGE . 'vendor/google/longrunning/src/ApiCore/LongRunning/', true);
$autoloader->register('Google\LongRunning', DIR_STORAGE . 'vendor/google/longrunning/src/LongRunning/', true);
$autoloader->register('GPBMetadata\Google\Longrunning', DIR_STORAGE . 'vendor/google/longrunning/metadata/Longrunning/', true);

// google/protobuf
$autoloader->register('Google\Protobuf', DIR_STORAGE . 'vendor/google/protobuf/src/Google/Protobuf/', true);
$autoloader->register('GPBMetadata\Google\Protobuf', DIR_STORAGE . 'vendor/google/protobuf/src/GPBMetadata/Google/Protobuf/', true);

// grpc/grpc
$autoloader->register('Grpc', DIR_STORAGE . 'vendor/grpc/grpc/src/lib/', true);

// guzzlehttp/guzzle
$autoloader->register('GuzzleHttp', DIR_STORAGE . 'vendor/guzzlehttp/guzzle/src/', true);
if (is_file(DIR_STORAGE . 'vendor/guzzlehttp/guzzle/src/functions_include.php')) {
	require_once(DIR_STORAGE . 'vendor/guzzlehttp/guzzle/src/functions_include.php');
}

// guzzlehttp/promises
$autoloader->register('GuzzleHttp\Promise', DIR_STORAGE . 'vendor/guzzlehttp/promises/src/', true);

// guzzlehttp/psr7
$autoloader->register('GuzzleHttp\Psr7', DIR_STORAGE . 'vendor/guzzlehttp/psr7/src/', true);

// matthiasmullie/minify
$autoloader->register('MatthiasMullie\Minify', DIR_STORAGE . 'vendor/matthiasmullie/minify/src/', true);

// matthiasmullie/path-converter
$autoloader->register('MatthiasMullie\PathConverter', DIR_STORAGE . 'vendor/matthiasmullie/path-converter/src/', true);

// monolog/monolog
$autoloader->register('Monolog', DIR_STORAGE . 'vendor/monolog/monolog/src/Monolog/', true);

// mtdowling/jmespath.php
$autoloader->register('JmesPath', DIR_STORAGE . 'vendor/mtdowling/jmespath.php/src/', true);
if (is_file(DIR_STORAGE . 'vendor/mtdowling/jmespath.php/src/JmesPath.php')) {
	require_once(DIR_STORAGE . 'vendor/mtdowling/jmespath.php/src/JmesPath.php');
}

// paragonie/constant_time_encoding
$autoloader->register('ParagonIE\ConstantTime', DIR_STORAGE . 'vendor/paragonie/constant_time_encoding/src/', true);

// paragonie/random_compat

// phpseclib/phpseclib
$autoloader->register('phpseclib3', DIR_STORAGE . 'vendor/phpseclib/phpseclib/phpseclib/', true);
if (is_file(DIR_STORAGE . 'vendor/phpseclib/phpseclib/phpseclib/bootstrap.php')) {
	require_once(DIR_STORAGE . 'vendor/phpseclib/phpseclib/phpseclib/bootstrap.php');
}

// psr/cache
$autoloader->register('Psr\Cache', DIR_STORAGE . 'vendor/psr/cache/src/', true);

// psr/http-client
$autoloader->register('Psr\Http\Client', DIR_STORAGE . 'vendor/psr/http-client/src/', true);

// psr/http-factory
$autoloader->register('Psr\Http\Message', DIR_STORAGE . 'vendor/psr/http-factory/src/', true);

// psr/http-message
$autoloader->register('Psr\Http\Message', DIR_STORAGE . 'vendor/psr/http-message/src/', true);

// psr/log
$autoloader->register('Psr\Log', DIR_STORAGE . 'vendor/psr/log/src/', true);

// ralouphie/getallheaders
if (is_file(DIR_STORAGE . 'vendor/ralouphie/getallheaders/src/getallheaders.php')) {
	require_once(DIR_STORAGE . 'vendor/ralouphie/getallheaders/src/getallheaders.php');
}

// ramsey/collection
$autoloader->register('Ramsey\Collection', DIR_STORAGE . 'vendor/ramsey/collection/src/', true);

// ramsey/uuid
$autoloader->register('Ramsey\Uuid', DIR_STORAGE . 'vendor/ramsey/uuid/src/', true);
if (is_file(DIR_STORAGE . 'vendor/ramsey/uuid/src/functions.php')) {
	require_once(DIR_STORAGE . 'vendor/ramsey/uuid/src/functions.php');
}

// rize/uri-template
$autoloader->register('Rize', DIR_STORAGE . 'vendor/rize/uri-template/src/Rize/', true);

// symfony/deprecation-contracts
if (is_file(DIR_STORAGE . 'vendor/symfony/deprecation-contracts/function.php')) {
	require_once(DIR_STORAGE . 'vendor/symfony/deprecation-contracts/function.php');
}

// symfony/filesystem
$autoloader->register('Symfony\Component\Filesystem', DIR_STORAGE . 'vendor/symfony/filesystem//', true);

// symfony/finder
$autoloader->register('Symfony\Component\Finder', DIR_STORAGE . 'vendor/symfony/finder//', true);

// symfony/polyfill-ctype
$autoloader->register('Symfony\Polyfill\Ctype', DIR_STORAGE . 'vendor/symfony/polyfill-ctype//', true);
if (is_file(DIR_STORAGE . 'vendor/symfony/polyfill-ctype/bootstrap.php')) {
	require_once(DIR_STORAGE . 'vendor/symfony/polyfill-ctype/bootstrap.php');
}

// symfony/polyfill-mbstring
$autoloader->register('Symfony\Polyfill\Mbstring', DIR_STORAGE . 'vendor/symfony/polyfill-mbstring//', true);
if (is_file(DIR_STORAGE . 'vendor/symfony/polyfill-mbstring/bootstrap.php')) {
	require_once(DIR_STORAGE . 'vendor/symfony/polyfill-mbstring/bootstrap.php');
}

// twig/twig
$autoloader->register('Twig', DIR_STORAGE . 'vendor/twig/twig/src/', true);
if (is_file(DIR_STORAGE . 'vendor/twig/twig/src/Resources/core.php')) {
	require_once(DIR_STORAGE . 'vendor/twig/twig/src/Resources/core.php');
}
if (is_file(DIR_STORAGE . 'vendor/twig/twig/src/Resources/debug.php')) {
	require_once(DIR_STORAGE . 'vendor/twig/twig/src/Resources/debug.php');
}
if (is_file(DIR_STORAGE . 'vendor/twig/twig/src/Resources/escaper.php')) {
	require_once(DIR_STORAGE . 'vendor/twig/twig/src/Resources/escaper.php');
}
if (is_file(DIR_STORAGE . 'vendor/twig/twig/src/Resources/string_loader.php')) {
	require_once(DIR_STORAGE . 'vendor/twig/twig/src/Resources/string_loader.php');
}