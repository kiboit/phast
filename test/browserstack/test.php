#!/usr/bin/env php
<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Exception\UnknownServerException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$tests = [
    [
        'os' => 'Windows',
        'os_version' => '10',
        'browser' => 'chrome',
    ],
    [
        'os' => 'OS X',
        'os_version' => 'Big Sur',
        'browser' => 'safari',
    ],
    [
        'os' => 'Windows',
        'os_version' => '10',
        'browser' => 'firefox',
    ],

    [
        'os' => 'OS X',
        'os_version' => 'Mountain Lion',
        'browser' => 'safari',
        'browser_version' => '6.2',
    ],
    [
        'os' => 'Windows',
        'os_version' => '10',
        'browser' => 'firefox',
        'browser_version' => '56',
    ],
    [
        'os' => 'Windows',
        'os_version' => '10',
        'browser' => 'chrome',
        'browser_version' => '62',
    ],
    [
        'os' => 'Windows',
        'browser' => 'edge',
        'browser_version' => '17',
    ],
    [
        'os' => 'windows',
        'browser' => 'ie',
        'browser_version' => '11',
    ],
];

$caps = parse_options(array_slice($argv, 1));

if (!$caps) {
    foreach ($tests as $test) {
        echo escapeshellcmd($argv[0]), ' ', generate_options($test), "\n";
    }
} else {
    $username = getenv('BROWSERSTACK_USERNAME');
    $access_key = getenv('BROWSERSTACK_ACCESS_KEY');

    if (empty($username) || empty($access_key)) {
        fwrite(STDERR, "Ensure that BROWSERSTACK_USERNAME and BROWSERSTACK_ACCESS_KEY are set.\n");
        exit(255);
    }

    $pipes = [];
    $proxy_proc = proc_open(
        'BrowserStackLocal --key ' . escapeshellarg($access_key) . ' ' .
            '--local-identifier ' . escapeshellarg($proxy_id = uniqid()) . ' ' .
            '>/dev/null',
        [
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR,
        ],
        $pipes
    );
    if (!$proxy_proc) {
        fwrite(STDERR, "Failed to start BrowserStackLocal!\n");
        exit(1);
    }

    $options = $caps;
    $options['browserstack.local'] = true;
    $options['browserstack.localIdentifier'] = $proxy_id;
    $options['real_mobile'] = !empty($caps['real_mobile']);

    $proxy_retry = 1;

    try {
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
                if (preg_match('/^\[browserstack.local\] is set to true but local testing/', $e->getMessage()) && $proxy_retry) {
                    $proxy_retry--;
                } elseif (!preg_match('/^All parallel tests are currently in use/', $e->getMessage())
                ) {
                    throw $e;
                }
                sleep(10);
            }
        }

        $status = 1;

        try {
            $driver->get('http://phast-browser.test/');
            $driver->wait()->until(
                WebDriverExpectedCondition::not(
                    WebDriverExpectedCondition::titleIs('Phast Unit Tests')
                )
            );
            $failed = get_failed($driver);
            if (empty($failed)) {
                $status = 0;
            } else {
                print_failed('', $caps);
                print_errors($failed);
            }
        } catch (TimeOutException $e) {
            print_failed('Timed out: ' . $e->getMessage(), $caps);
        } catch (Exception $e) {
            print_failed('Unknown error: ' . $e->getMessage(), $caps);
        } finally {
            $driver->quit();
        }
    } finally {
        proc_terminate($proxy_proc, 9);
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
            echo '        - ' . $assertion->getText() . "\n";
        }
    }
}

function print_failed($error, array $caps) {
    echo 'Failed: ' . generate_options($caps) . "\n";
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
