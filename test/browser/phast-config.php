<?php
return [
    'servicesUrl' => preg_replace('~^(.*/browser)?/.*~si', '$1/', $_SERVER['PHP_SELF']) . 'phast.php',
    'csp' => [
        'nonce' => defined('PHAST_CSP_NONCE') ? PHAST_CSP_NONCE : null,
        'reportOnly' => defined('PHAST_CSP_REPORT_ONLY') && PHAST_CSP_REPORT_ONLY,
        'reportUri' => '/phast-report.php',
    ],
];
