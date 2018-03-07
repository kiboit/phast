#!/usr/bin/env php
<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Exception\UnknownServerException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$tests = require_once __DIR__ . '/tests_list.php';

$caps = parse_options(array_slice($argv, 1));

if (!$caps) {
    foreach ($tests as $test) {
        echo escapeshellcmd($argv[0]), " ", generate_options($test), "\n";
    }
} else {
    $username = getenv('BROWSERSTACK_USERNAME');
    $access_key = getenv('BROWSERSTACK_ACCESS_KEY');

    if (empty($username) || empty($access_key)) {
        fwrite(STDERR, "Ensure that BROWSERSTACK_USERNAME and BROWSERSTACK_ACCESS_KEY are set.\n");
        exit(255);
    }

    $options = $caps;
    $options['browserstack.local'] = true;
    $options['real_mobile'] = !empty($caps['real_mobile']);

    while (true) {
        try {
            $driver = RemoteWebDriver::create(
                "https://$username:$access_key@hub-cloud.browserstack.com/wd/hub",
                $options,
                30000,
                120000
            );
            break;
        } catch (UnknownServerException $e) {
            if (!preg_match('/^All parallel tests are currently in use/', $e->getMessage())) {
                throw $e;
            }
            sleep(10);
        }
    }

    $status = 1;

    try {
        $driver->get("http://phast-browser.test/");
        $driver->wait()->until(
            WebDriverExpectedCondition::not(
                WebDriverExpectedCondition::titleIs('Phast Unit Tests')
            )
        );
        $failed = get_failed($driver);
        if (empty ($failed)) {
            $status = 0;
        } else {
            print_failed('', $caps);
            print_errors($failed);
        }
    } catch (TimeOutException $e) {
        print_failed("Timed out: " . $e->getMessage(), $caps);
    } catch (Exception $e) {
        print_failed("Unknown error: " . $e->getMessage(), $caps);
    } finally {
        $driver->quit();
    }

    exit($status);
}

function get_failed(RemoteWebDriver $driver) {
    return $driver->findElements(WebDriverBy::cssSelector('[id^="qunit-test-output"].fail'));
}

/**
 * @param \Facebook\WebDriver\WebDriverElement[] $tests
 */
function print_errors(array $tests) {
    foreach ($tests as $test) {
        $name = $test->findElements(WebDriverBy::cssSelector('.test-name'))[0]->getText();
        echo "    Test: $name\n";
        $assertions = $test->findElements(WebDriverBy::cssSelector('.qunit-assert-list .fail .test-message'));
        foreach ($assertions as $assertion) {
            echo "        - " . $assertion->getText() . "\n";
        }
    }
}

function print_failed($error, array $caps) {
    echo "Failed: " . generate_options($caps) . "\n";
    if ($error) {
        echo "$error\n";
    }
}

function generate_options(array $options) {
    $output = [];
    foreach ($options as $k => $v) {
        $output[] = sprintf('--%s %s', escape_argument($k), escape_argument($v));
    }
    return implode(' ', $output);
}

function escape_argument($str) {
    return preg_match('/^[a-z0-9_]+$/i', $str) ? $str : escapeshellarg($str);
}

function parse_options($args) {
    $options = [];
    for ($i = 0; $i < sizeof($args); $i++) {
        if (!preg_match('/^--(.+)$/', $args[$i], $match)) {
            fwrite(STDERR, "Unexpected argument value at position $i\n");
            exit(1);
        }
        $name = $match[1];
        if (isset($options[$name])) {
            fwrite(STDERR, "Duplicate option --$name\n");
            exit(1);
        }
        $i++;
        if (!isset($args[$i])) {
            fwrite(STDERR, "Missing value for option --$name\n");
            exit(1);
        }
        $options[$name] = $args[$i];
    }
    return $options;
}
