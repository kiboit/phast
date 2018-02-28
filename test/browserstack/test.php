<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$tests = require_once __DIR__ . '/tests_list.php';

if (!isset ($argv[1])) {
    die ("Argument 1 must be browserstack username!\n");
}
if (!isset ($argv[2])) {
    die ("Argument 2 must be browserstack access key\n");
}

$username = $argv[1];
$access_key = $argv[2];

foreach ($tests as $caps) {
    $caps['browserstack.local'] = true;

    print_start($caps);

    try {
        $driver = RemoteWebDriver::create(
            "https://$username:$access_key@hub-cloud.browserstack.com/wd/hub",
            $caps,
            30000,
            120000
        );
        $driver->get("http://phast-browser.test/");
        $driver->wait()->until(
            WebDriverExpectedCondition::not(
                WebDriverExpectedCondition::titleIs('Phast Unit Tests')
            )
        );
        $failed = get_failed($driver);
        if (empty ($failed)) {
            print_success($caps);
        } else {
            print_failed('', $caps);
            print_errors($failed);
        }
        $driver->quit();
    } catch (TimeOutException $e) {
        print_failed("Timed out: " . $e->getMessage(), $caps);
        $driver->quit();
    } catch (Exception $e) {
        print_failed("Unknown error: " . $e->getMessage(), $caps);
        continue;
    }
}

function get_failed(RemoteWebDriver $driver) {
    return $driver->findElements(WebDriverBy::cssSelector('[id^="qunit-test-output"].fail'));
}

/**
 * @param \Facebook\WebDriver\WebDriverElement[] $tests
 */
function print_errors(array $tests) {
    foreach ($tests as $test) {
        print_line();
        $name = $test->findElements(WebDriverBy::cssSelector('.test-name'))[0]->getText();
        echo "Test: $name\n";
        print_line();
        echo "Assertions:\n";
        $assertions = $test->findElements(WebDriverBy::cssSelector('.qunit-assert-list .fail .test-message'));
        foreach ($assertions as $assertion) {
            echo "\t" . $assertion->getText() . "\n";
        }
        print_line();
    }
}

function print_start(array $caps) {
    echo "Starting " . get_test_run_info($caps) . "\n";

}

function print_success(array $caps) {
    echo "Success for " . get_test_run_info($caps) . "\n";
}

function print_failed($error, array $caps) {
    echo "Failed for " . get_test_run_info($caps) . ": $error\n";
}

function print_line() {
    echo "-----------------------------------------\n";
}

function get_test_run_info(array $caps) {
    $result = "{$caps['browser']}";
    if (isset ($caps['version'])) {
        $result .= " {$caps['version']}";
    }
    $result .= " on {$caps['os']}";
    if (isset ($caps['os_version'])) {
        $result .= " {$caps['os_version']}";
    }
    return $result;
}
