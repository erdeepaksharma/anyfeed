<?php

define('VERSION', '2.0.1.1');

error_reporting(E_ALL);
@ini_set('display_errors', 1);

// Configuration
if (is_file(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php')) {
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php');
} else {
	die("Can't load config.php, check file permissions.");
}

// CLI Initiated
define('CLI_INITIATED', true);

if (!isset($argc))
	die("This file must be run from a Cron Task or the command line.");

if ($argc > 1 && $argv[1]) {
	define('FEED_NAME', $argv[1]);
} else {
	die("Error: No Feed Name specified.");
}

if (!isset($_SERVER['SERVER_PORT'])) {
	$_SERVER['SERVER_PORT'] = 80;
}

// Startup
$indexFile = fopen(DIR_SYSTEM . '../index.php', 'r');
$indexContent = fread($indexFile, filesize(DIR_SYSTEM . '../index.php'));
fclose($indexFile);
if (strpos($indexContent, "require_once('./vqmod/vqmod.php');")) {
	// VirtualQMOD
	require_once(DIR_SYSTEM . '../vqmod/vqmod.php');
	VQMod::bootup();

	// VQMODDED Startup
	require_once(VQMod::modCheck(DIR_SYSTEM . 'startup.php'));
} else {
	require_once(DIR_SYSTEM . 'startup.php');
}

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0'");

foreach ($query->rows as $setting) {
	$config->set($setting['key'], $setting['value']);
}

// Url
$url = new Url(HTTP_SERVER, HTTPS_SERVER);
$registry->set('url', $url);

// Log
$log = new Log($config->get('config_error_filename'));
$registry->set('log', $log);

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$registry->set('response', $response);

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache);

// Session
$session = new Session();
$registry->set('session', $session);

// Language
if (strpos($indexContent, "require_once(DIR_SYSTEM . 'framework.php');")) {
	$language = new Language($config->get('language_default'));
	$language->load($config->get('language_default'));
	$registry->set('language', $language);
} else if (strpos($indexContent, "// Language Detection")) {
	$languages = array();

	$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE status = '1'");

	foreach ($query->rows as $result) {
		$languages[$result['code']] = $result;
	}

	if (isset($session->data['language']) && array_key_exists($session->data['language'], $languages)) {
		$code = $session->data['language'];
	} elseif (isset($request->cookie['language']) && array_key_exists($request->cookie['language'], $languages)) {
		$code = $request->cookie['language'];
	} else {
		$detect = '';

		if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && $request->server['HTTP_ACCEPT_LANGUAGE']) {
			$browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);

			foreach ($browser_languages as $browser_language) {
				foreach ($languages as $key => $value) {
					if ($value['status']) {
						$locale = explode(',', $value['locale']);

						if (in_array($browser_language, $locale)) {
							$detect = $key;
							break 2;
						}
					}
				}
			}
		}

		$code = $detect ? $detect : $config->get('config_language');
	}

	if (!isset($session->data['language']) || $session->data['language'] != $code) {
		$session->data['language'] = $code;
	}

	if (!isset($request->cookie['language']) || $request->cookie['language'] != $code) {
		setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
	}

	$config->set('config_language_id', $languages[$code]['language_id']);
	$config->set('config_language', $languages[$code]['code']);

	// Language
	$language = new Language($languages[$code]['directory']);
	$language->load($languages[$code]['directory']);
	$registry->set('language', $language);
} else {
	$languages = array();

	$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE status = '1'");

	foreach ($query->rows as $result) {
		$languages[$result['code']] = $result;
	}

	$code = $config->get('config_language');
	$config->set('config_language_id', $languages[$code]['language_id']);
	$config->set('config_language', $languages[$code]['code']);
	$language = new Language($languages[$code]['directory']);
	$language->load('default');
	$registry->set('language', $language);
}

// Currency and Tax
if (file_exists(DIR_SYSTEM . 'library/cart/currency.php')) {
	$registry->set('currency', new Cart\Currency($registry));
	$registry->set('tax', new Cart\Tax($registry));
} else {
	$registry->set('currency', new Currency($registry));
	$registry->set('tax', new Tax($registry));
}



// Event
$event = new Event($registry);
$registry->set('event', $event);

$query = $db->query("SELECT * FROM " . DB_PREFIX . "event");

foreach ($query->rows as $result) {
	$event->register($result['trigger'], $result['action']);
}

// Document
$document = new Document();
$registry->set('document', $document);

// Front Controller
$controller = new Front($registry);

//Create lock file to prevent multiple runs
$lock_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . "cron_lock";
$run = false;
if(!file_exists($lock_file)) {
	$ourFileHandle = fopen($lock_file, 'w') or die("can't open file");
	fclose($ourFileHandle);
	$run = true;
} else {
	//Remove a lock file over one hour old
	$file_age = time() - filemtime($lock_file);
	if($file_age >= 3600) {
		$run = true;
	} else {
		die("cron_lock file present. Feed Generation is already running. (The Cron Lock file is automatically cleared after 1 hour or you can remove cron_lock in your admin folder)\n");
	}
}
// Generate Feed
if($run){
	//Route of action
	echo "Starting Feed Generation..\n";
	$action = new Action('feed/any_feed_pro/index');
	//Run cron
	$controller->dispatch($action, new Action('error/not_found'));
	$response->output();

	echo "Cron Feed Finished..\n";
	//Delete lock file
	unlink($lock_file);
}

?>
