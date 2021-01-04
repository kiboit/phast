# Phast

__Phast__ is a unique automated page optimization suite for PHP by [Kibo
IT](https://www.kiboit.com).

Phast applies advanced optimization techniques to any PHP-based site without
changing any code. Optimizations are applied in such a way that no changes to
your site are necessary. Sites optimized by Phast easily score 90+ in Google
PageSpeed Insights, and usually reach 100/100 with small adjustments.

A free integration for WordPress is available as
[PhastPress](https://wordpress.org/plugins/phastpress/)
([GitHub](https://github.com/kiboit/phastpress)).

Van der Let & Partners have contributed an [OctoberCMS plugin for
Phast](https://octobercms.com/plugin/vdlp-phast).

__[Contact Kibo IT](https://www.kiboit.com/contact)__
([email](mailto:info@kiboit.com)): We'd love your feedback. We can help you try out
Phast and provide custom services for website optimization.


## Getting started

Install Phast into your project using Composer:

~~~
composer install kiboit/phast:dev-master
~~~

Create `http://your.site/phast.php` to serve optimized versions of resources:

~~~php
<?php
require 'vendor/autoload.php';
\Kibo\Phast\PhastServices::serve();
~~~

Load Phast on all of your pages:

~~~php
<?php
// At the top of your index.php
require 'vendor/autoload.php';
\Kibo\Phast\PhastDocumentFilters::deploy();
~~~

Test your site!


## Features

* Image optimization: Images are recompressed, optimised (via
  [pngquant](https://pngquant.org/) and
  [jpegtran](https://en.wikipedia.org/wiki/Libjpeg#jpegtran)) and converted to
  WebP, when supported by the browser.

* CSS optimization: We break down included stylesheets and remove all
  class-based selectors that cannot apply to the current document. The optimised
  CSS is inlined. After the page has loaded, the original CSS is included, so
  any classes used in JavaScript will be available.

* CSS inlining: Small stylesheets (including Google Fonts) get inlined.

* Delay IFrames: IFrames are loaded after the page finishes, to prevent stealing
  bandwidth and resources from the main page load.

* Scripts rearrangement: We move all &lt;script&gt; tags to the bottom of the
  page, so the important stuff gets loaded first.

* Scripts deferring: Scripts are loaded asynchronously with full compatibility.
  To make sure that legacy scripts work while being loaded asynchronously, we
  use a custom script loader that loads the scripts in order, and triggers
  DOMContentLoaded when they have finished.

* Scripts proxy: External scripts such as Google Analytics are loaded through a
  proxy script. This allows us to extend the cache duration.


## Browser compatibility

Phast is tested to work on all browser versions equal or higher than the following:

* Firefox 56
* Chrome 62
* Edge (any version)
* Safari 6.2
* IE 11


## Thanks, BrowserStack!

[![BrowserStack](https://peschar.net/files/browserstack.png)](https://www.browserstack.com)

[BrowserStack](https://www.browserstack.com) generously provides us with free
access to their browser testing platform, so we can make sure Phast works on all
supported browsers.
