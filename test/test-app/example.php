<?php
define('PHAST_CONFIG_FILE', __DIR__ . '/test-config.php');
require_once __DIR__ . '/../../src/html-filters.php';
?>
<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="ltr" lang="nl" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="ltr" lang="nl" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="ltr" class="ltr" lang="nl">
<!--<![endif]-->
<head>

                  

			<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','//connect.facebook.net/en_US/fbevents.js');  fbq('init', '1350949178350380');fbq('track', 'PageView', {value: '0.00', currency: 'EUR'});</script>			
<meta charset="UTF-8" />
<style>
#header-main .logo a,
#mobile-header .logo-store a {
    background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGFhZ18xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDcyMi42IDE1MS43IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA3MjIuNiAxNTEuNzsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCgkuc3Qwe2ZpbGw6IzQxNDE0Mjt9DQoJLnN0MXtmaWxsOiNDNTFBMTk7fQ0KPC9zdHlsZT4NCjxnPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xOTguMiwxMTIuMmgwLjdsMC0wLjJjMC43LTMtMC4yLTQuMS0yLjgtNC4xYy0wLjksMC0yLjgsMC4yLTMuMSwxLjRsLTAuMywxLjFoLTQuOGwwLjUtMi4yDQoJCWMxLTMuOSw2LjUtNC4zLDguNy00LjNjNi45LDAsOC4xLDMuNiw2LjksOC4zbC0xLjksNy44Yy0wLjEsMC42LDAuMSwwLjksMC43LDAuOWgxLjZsLTEuMSw0LjRoLTQuN2MtMS45LDAtMi40LTEuMS0yLjEtMi4zDQoJCWMwLjEtMC41LDAuMy0wLjksMC4zLTAuOWgtMC4xYzAsMC0yLjUsMy43LTcuMiwzLjdjLTMuNywwLTYuNS0yLjMtNS41LTYuNUMxODUuNywxMTIuNywxOTQuOSwxMTIuMiwxOTguMiwxMTIuMnogTTE5MS45LDEyMS43DQoJCWMyLjgsMCw1LjUtMi45LDYuMS01LjVsMC4xLTAuNWgtMC45Yy0yLjcsMC03LjEsMC40LTcuOCwzLjNDMTg5LjEsMTIwLjQsMTg5LjgsMTIxLjcsMTkxLjksMTIxLjd6Ii8+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTIxNCwxMDkuOGMwLjItMC42LTAuMS0wLjktMC43LTAuOWgtMS42bDEuMS00LjRoNC44YzEuOCwwLDIuNSwwLjksMi4yLDIuM2wtMC4yLDAuNg0KCQljLTAuMSwwLjQtMC4zLDAuOS0wLjMsMC45aDAuMWMxLjMtMS44LDQuMi00LjMsOC40LTQuM2M0LjYsMCw2LjcsMi40LDUuMyw3LjlsLTIsOC4xYy0wLjEsMC42LDAuMSwwLjksMC43LDAuOWgxLjZsLTEuMSw0LjRoLTQuOQ0KCQljLTIsMC0yLjYtMC44LTIuMS0yLjhsMi40LTkuNmMwLjYtMi41LDAuNC00LjItMi4yLTQuMmMtMi44LDAtNS4zLDEuOC02LjYsNC4yYy0wLjUsMC45LTAuOSwxLjgtMS4xLDIuOGwtMi40LDkuNmgtNS4xTDIxNCwxMDkuOHoNCgkJIi8+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTI0My4zLDEwOC42aC0yLjdsMS00LjFoMi44bDEuNC01LjdoNS4xbC0xLjQsNS43aDQuN2wtMSw0LjFoLTQuN2wtMi4xLDguNGMtMC45LDMuNCwxLjcsMy45LDMuMSwzLjkNCgkJYzAuNiwwLDEtMC4xLDEtMC4xbC0xLjEsNC41YzAsMC0wLjcsMC4xLTEuNiwwLjFjLTIuOSwwLTguNC0wLjktNi43LTcuOEwyNDMuMywxMDguNnoiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMjcwLjMsMTEyLjJoMC43bDAtMC4yYzAuNy0zLTAuMi00LjEtMi44LTQuMWMtMC45LDAtMi44LDAuMi0zLjEsMS40bC0wLjMsMS4xaC00LjhsMC41LTIuMg0KCQljMS0zLjksNi41LTQuMyw4LjctNC4zYzYuOSwwLDguMSwzLjYsNi45LDguM2wtMS45LDcuOGMtMC4xLDAuNiwwLjEsMC45LDAuNywwLjloMS42bC0xLjEsNC40aC00LjdjLTEuOSwwLTIuNC0xLjEtMi4xLTIuMw0KCQljMC4xLTAuNSwwLjMtMC45LDAuMy0wLjloLTAuMWMwLDAtMi41LDMuNy03LjIsMy43Yy0zLjcsMC02LjUtMi4zLTUuNS02LjVDMjU3LjgsMTEyLjcsMjY2LjksMTEyLjIsMjcwLjMsMTEyLjJ6IE0yNjMuOSwxMjEuNw0KCQljMi44LDAsNS41LTIuOSw2LjEtNS41bDAuMS0wLjVoLTAuOWMtMi43LDAtNy4xLDAuNC03LjgsMy4zQzI2MS4xLDEyMC40LDI2MS44LDEyMS43LDI2My45LDEyMS43eiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0yODMuOSwxMTguN2MwLDAsMS45LDMuMSw1LjQsMy4xYzEuNiwwLDMtMC43LDMuMy0yYzAuNy0yLjktMTAuMy0yLjktOC42LTkuNmMxLTQuMiw1LjMtNi4xLDkuNi02LjENCgkJYzIuOCwwLDcuMSwwLjksNi4yLDQuM2wtMC41LDIuMmgtNC42bDAuMy0xYzAuMi0xLTEuMS0xLjUtMi4yLTEuNWMtMS44LDAtMy4zLDAuNy0zLjYsMS45Yy0wLjgsMy4zLDEwLjQsMi42LDguNyw5LjYNCgkJYy0xLDMuOS01LjEsNi40LTkuNiw2LjRjLTUuNywwLTcuNy0zLjctNy43LTMuN0wyODMuOSwxMTguN3oiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMzA4LjIsMTA4LjZoLTIuN2wxLTQuMWgyLjhsMS40LTUuN2g1LjFsLTEuNCw1LjdoNC43bC0xLDQuMWgtNC43bC0yLjEsOC40Yy0wLjksMy40LDEuNywzLjksMy4xLDMuOQ0KCQljMC42LDAsMS0wLjEsMS0wLjFsLTEuMSw0LjVjMCwwLTAuNywwLjEtMS42LDAuMWMtMi45LDAtOC40LTAuOS02LjctNy44TDMwOC4yLDEwOC42eiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0zMjUuNywxMDkuOGMwLjItMC42LTAuMS0wLjktMC43LTAuOWgtMS42bDEuMS00LjRoNC45YzEuOSwwLDIuNSwwLjksMiwyLjhsLTMuMiwxMi44DQoJCWMtMC4xLDAuNiwwLjEsMC45LDAuNywwLjloMS42bC0xLjEsNC40aC00LjljLTEuOSwwLTIuNS0wLjgtMi0yLjhMMzI1LjcsMTA5Ljh6IE0zMjkuNCw5Ni4zaDQuNWwtMS4yLDQuOGgtNC41TDMyOS40LDk2LjN6Ii8+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTMzOC4zLDExOC43YzAsMCwxLjksMy4xLDUuNCwzLjFjMS42LDAsMy0wLjcsMy4zLTJjMC43LTIuOS0xMC4zLTIuOS04LjYtOS42YzEtNC4yLDUuMy02LjEsOS42LTYuMQ0KCQljMi44LDAsNy4xLDAuOSw2LjIsNC4zbC0wLjUsMi4yaC00LjZsMC4zLTFjMC4yLTEtMS4xLTEuNS0yLjItMS41Yy0xLjgsMC0zLjMsMC43LTMuNiwxLjljLTAuOCwzLjMsMTAuNCwyLjYsOC43LDkuNg0KCQljLTEsMy45LTUuMSw2LjQtOS42LDYuNGMtNS43LDAtNy43LTMuNy03LjctMy43TDMzOC4zLDExOC43eiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0zNzIuOSwxMDRjMi43LDAsNy4zLDEuMSw2LjQsNC43bC0wLjYsMi40aC00LjZsMC4zLTEuMWMwLjMtMS4xLTEuMy0xLjYtMi42LTEuNmMtMy42LDAtNi44LDIuNy03LjgsNi41DQoJCWMtMS4xLDQuMiwxLjUsNi40LDQuOSw2LjRjMy42LDAsNi45LTIuOCw2LjktMi44bDEuMywzLjZjMCwwLTQsMy44LTkuNywzLjhjLTYuOSwwLTEwLjEtNC45LTguNS0xMC45DQoJCUMzNjAuMywxMDksMzY1LjgsMTA0LDM3Mi45LDEwNHoiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMzg4LjYsMTAxLjZjMC4yLTAuNi0wLjEtMC45LTAuNy0wLjloLTEuNmwxLjEtNC40aDQuOWMxLjksMCwyLjYsMC45LDIuMSwyLjdsLTEuOCw3LjMNCgkJYy0wLjMsMS0wLjUsMS44LTAuNSwxLjhoMC4xYzEuNS0yLDQuNi00LjEsOC4yLTQuMWM0LjcsMCw2LjcsMi40LDUuMyw3LjlsLTIsOC4xYy0wLjEsMC42LDAuMSwwLjksMC43LDAuOWgxLjZsLTEuMSw0LjRoLTQuOQ0KCQljLTEuOSwwLTIuNS0wLjgtMi0yLjhsMi40LTkuNmMwLjYtMi41LDAuNC00LjItMi4yLTQuMmMtMi43LDAtNS4zLDEuOC02LjcsNC4zYy0wLjQsMC44LTAuOCwxLjctMSwyLjdsLTIuNCw5LjZoLTUuMkwzODguNiwxMDEuNnoNCgkJIi8+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTQyNy43LDEwOS42Yy0wLjEtMC41LTAuNC0wLjctMS0wLjdoLTAuNGwxLjEtNC40aDNjMS43LDAsMi40LDAuNSwyLjYsMmwxLjIsMTAuOWMwLjEsMS4yLDAsMywwLDNoMC4xDQoJCWMwLDAsMC44LTEuOCwxLjUtM2w2LjYtMTAuOWMwLjktMS40LDEuOS0yLDMuNS0yaDIuOGwtMS4xLDQuNGgtMC41Yy0wLjYsMC0xLDAuMi0xLjMsMC43bC05LjksMTUuN2gtNi4yTDQyNy43LDEwOS42eiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00NjQuMiwxMDRjNi40LDAsMTAuMyw0LjUsOC43LDEwLjljLTEuNiw2LjQtNy44LDEwLjktMTQuMSwxMC45Yy02LjMsMC0xMC4zLTQuNS04LjctMTAuOQ0KCQlDNDUxLjcsMTA4LjUsNDU3LjgsMTA0LDQ2NC4yLDEwNHogTTQ1OS44LDEyMS4zYzMuNCwwLDYuOS0yLjYsNy44LTYuNWMxLTMuOC0xLjItNi41LTQuNi02LjVjLTMuMywwLTYuOCwyLjctNy44LDYuNQ0KCQlDNDU0LjMsMTE4LjcsNDU2LjUsMTIxLjMsNDU5LjgsMTIxLjN6Ii8+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTQ5Mi4zLDEwNGM2LjQsMCwxMC4zLDQuNSw4LjcsMTAuOWMtMS42LDYuNC03LjgsMTAuOS0xNC4xLDEwLjljLTYuMywwLTEwLjMtNC41LTguNy0xMC45DQoJCUM0NzkuNywxMDguNSw0ODUuOSwxMDQsNDkyLjMsMTA0eiBNNDg3LjksMTIxLjNjMy40LDAsNi45LTIuNiw3LjgtNi41YzEtMy44LTEuMi02LjUtNC42LTYuNWMtMy4zLDAtNi44LDIuNy03LjgsNi41DQoJCUM0ODIuNCwxMTguNyw0ODQuNiwxMjEuMyw0ODcuOSwxMjEuM3oiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNTA5LjYsMTA5LjhjMC4yLTAuNi0wLjEtMC45LTAuNy0wLjloLTEuNmwxLjEtNC40aDQuN2MxLjgsMCwyLjYsMC44LDIuMSwyLjVsLTAuMywxLjINCgkJYy0wLjIsMC44LTAuNCwxLjMtMC40LDEuM2gwLjFjMS43LTMsNC44LTUuMyw3LjktNS4zYzAuNCwwLDAuOSwwLjEsMC45LDAuMWwtMS4zLDUuMWMwLDAtMC41LTAuMS0xLjItMC4xYy0yLjIsMC01LDEuMy02LjgsNC40DQoJCWMtMC41LDEtMSwyLjItMS4zLDMuNGwtMi4xLDguMmgtNS4xTDUwOS42LDEwOS44eiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik01MzQuNCwxMjkuM2MxLjUsMCw0LjMtMC40LDUuMS0zLjhsMy45LTE1LjdjMC4yLTAuNi0wLjEtMC45LTAuNy0wLjloLTEuNmwxLjEtNC40aDQuOWMyLDAsMi42LDAuOSwyLjEsMi44DQoJCWwtNC43LDE4LjdjLTEuNyw3LTcuOCw3LjgtMTAuNiw3LjhjLTAuOSwwLTEuNS0wLjEtMS41LTAuMWwxLjEtNC40QzUzMy41LDEyOS4yLDUzMy44LDEyOS4zLDUzNC40LDEyOS4zeiBNNTQ3LDk2LjNoNC41bC0xLjIsNC44DQoJCWgtNC41TDU0Nyw5Ni4zeiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik01NjcuNiwxMDRjNiwwLDguMSw0LjQsNi44LDkuOWMtMC4yLDAuNi0wLjYsMS45LTAuNiwxLjloLTE0LjRjLTAuNSwzLjcsMS43LDUuNiw0LjgsNS42DQoJCWMzLjMsMCw2LjQtMi4zLDYuNC0yLjNsMS4zLDMuNmMwLDAtNCwzLjEtOS4yLDMuMWMtNi45LDAtOS45LTUtOC41LTEwLjlDNTU1LjksMTA4LjQsNTYxLjMsMTA0LDU2Ny42LDEwNHogTTU2OS40LDExMi4yDQoJCWMwLjUtMi41LTAuNy00LjItMi45LTQuMmMtMi43LDAtNC45LDEuNi02LjIsNC4ySDU2OS40eiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik01OTUuMiwxMDkuNmMtMC4xLTAuNS0wLjQtMC43LTEtMC43aC0wLjRsMS4xLTQuNGgzYzEuNywwLDIuNCwwLjUsMi42LDJsMS4yLDEwLjljMC4xLDEuMiwwLDMsMCwzaDAuMQ0KCQljMCwwLDAuOC0xLjgsMS41LTNsNi42LTEwLjljMC45LTEuNCwxLjktMiwzLjUtMmgyLjhsLTEuMSw0LjRoLTAuNWMtMC42LDAtMSwwLjItMS4zLDAuN2wtOS45LDE1LjdoLTYuMkw1OTUuMiwxMDkuNnoiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNjIzLjMsMTAxLjZjMC4yLTAuNi0wLjEtMC45LTAuNy0wLjlINjIxbDEuMS00LjRoNC45YzEuOSwwLDIuNiwwLjksMi4xLDIuOGwtNS4yLDIwLjkNCgkJYy0wLjEsMC42LDAuMSwwLjksMC43LDAuOWgxLjZsLTEuMSw0LjRoLTQuOWMtMiwwLTIuNi0wLjgtMi4xLTIuOEw2MjMuMywxMDEuNnoiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNjQ2LjcsMTA0YzYuNCwwLDEwLjMsNC41LDguNywxMC45Yy0xLjYsNi40LTcuOCwxMC45LTE0LjEsMTAuOWMtNi4zLDAtMTAuMy00LjUtOC43LTEwLjkNCgkJQzYzNC4xLDEwOC41LDY0MC4zLDEwNCw2NDYuNywxMDR6IE02NDIuMywxMjEuM2MzLjQsMCw2LjktMi42LDcuOC02LjVjMS0zLjgtMS4yLTYuNS00LjYtNi41Yy0zLjMsMC02LjgsMi43LTcuOCw2LjUNCgkJQzYzNi44LDExOC43LDYzOSwxMjEuMyw2NDIuMywxMjEuM3oiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNjczLjksMTA0YzYsMCw4LjEsNC40LDYuOCw5LjljLTAuMiwwLjYtMC42LDEuOS0wLjYsMS45aC0xNC40Yy0wLjUsMy43LDEuNyw1LjYsNC45LDUuNg0KCQljMy4zLDAsNi40LTIuMyw2LjQtMi4zbDEuMywzLjZjMCwwLTQsMy4xLTkuMiwzLjFjLTYuOSwwLTkuOS01LTguNS0xMC45QzY2Mi4yLDEwOC40LDY2Ny43LDEwNCw2NzMuOSwxMDR6IE02NzUuOCwxMTIuMg0KCQljMC41LTIuNS0wLjctNC4yLTIuOS00LjJjLTIuNywwLTQuOSwxLjYtNi4yLDQuMkg2NzUuOHoiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNjg5LjQsMTA5LjhjMC4yLTAuNi0wLjEtMC45LTAuNy0wLjloLTEuNmwxLjEtNC40aDQuN2MxLjgsMCwyLjYsMC44LDIuMSwyLjVsLTAuMywxLjINCgkJYy0wLjIsMC44LTAuNCwxLjMtMC40LDEuM2gwLjFjMS43LTMsNC44LTUuMyw3LjktNS4zYzAuNCwwLDAuOSwwLjEsMC45LDAuMWwtMS4zLDUuMWMwLDAtMC41LTAuMS0xLjItMC4xYy0yLjIsMC01LjEsMS4zLTYuOCw0LjQNCgkJYy0wLjUsMS0xLDIuMi0xLjMsMy40bC0yLjEsOC4yaC01LjFMNjg5LjQsMTA5Ljh6Ii8+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTcwNy4xLDEyMC40aDVsLTEuMiw0LjhoLTVMNzA3LjEsMTIwLjR6IE03MTIuOSw5Ni4zaDUuNGwtNS41LDIwLjZoLTQuNUw3MTIuOSw5Ni4zeiIvPg0KPC9nPg0KPHBhdGggY2xhc3M9InN0MSIgZD0iTTEyOS42LDEzNUg3Mi44Yy0xNi4yLDAtMzEuOC00LjEtNDMuMy0xNS42QzE4LDEwOCwxMiw5Ni42LDEyLDgwLjJzNi4zLTMxLjUsMTcuOC00Mw0KCWM4LjMtOC4zLDE4LjYtMTMuOSwyOS44LTE2LjRMNTYuOSw5LjFDMjQuMywxNi40LDAsNDUuNSwwLDgwLjJDMCwxMjAuNCwzMi42LDE0Nyw3Mi44LDE0N2w0LjMsMGg0OS41TDEyOS42LDEzNXoiLz4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xMzUuNSwxMTEuNGwtNjMuNywwYy0yMCwwLTM1LjUtMTIuNy0zNS41LTMyLjFjMC0xNy4zLDEyLjQtMzEuNywyOC44LTM0LjZsLTIuNy0xMS44DQoJQzQwLjYsMzcsMjQuMiw1Ni4yLDI0LjIsNzkuM2MwLDI2LjEsMjEsNDQuMSw0Ny42LDQ0LjFsNjAuNy0wLjFMMTM1LjUsMTExLjR6Ii8+DQo8cGF0aCBjbGFzcz0ic3QxIiBkPSJNMTQxLjQsODcuNUg3MC41djBjLTUuMi0wLjEtOS43LTQuNS05LjctOS44YzAtNS4zLDQuMy05LjcsOS42LTkuN2MwLDAtMi44LTExLjktMi44LTExLjkNCgljLTEwLjcsMS40LTE5LDEwLjUtMTksMjEuNmMwLDEyLDkuOCwyMS43LDIxLjgsMjEuN2wxLjksMGg2NkwxNDEuNCw4Ny41eiIvPg0KPGc+DQoJPHBhdGggY2xhc3M9InN0MSIgZD0iTTE1MC4yLDY4LjFoLTE0LjZsLTguMi0zMS44Yy0wLjMtMS4xLTAuOC0zLjUtMS42LTdjLTAuNy0zLjYtMS4yLTUuOS0xLjMtNy4yYy0wLjIsMS41LTAuNiwzLjktMS4yLDcuMg0KCQljLTAuNywzLjMtMS4yLDUuNy0xLjUsNy4xbC04LjIsMzEuOEg5OUw4My42LDcuNWgxMi42bDcuOCwzMy4xYzEuNCw2LjEsMi4zLDExLjQsMi45LDE1LjljMC4yLTEuNiwwLjUtNCwxLjEtNy4zDQoJCWMwLjYtMy4zLDEuMi01LjksMS43LTcuN2w4LjgtMzRoMTIuMWw4LjgsMzRjMC40LDEuNSwwLjksMy44LDEuNSw3YzAuNiwzLjEsMSw1LjgsMS4zLDhjMC4zLTIuMiwwLjctNC44LDEuMy04LjENCgkJYzAuNi0zLjIsMS4yLTUuOCwxLjctNy44TDE1Myw3LjVoMTIuNkwxNTAuMiw2OC4xeiIvPg0KCTxwYXRoIGNsYXNzPSJzdDEiIGQ9Ik0xOTMuMyw2OC45Yy03LjUsMC0xMy4zLTIuMS0xNy41LTYuMmMtNC4yLTQuMS02LjMtMTAtNi4zLTE3LjVjMC03LjgsMS45LTEzLjgsNS44LTE4DQoJCWMzLjktNC4yLDkuMy02LjQsMTYuMS02LjRjNi42LDAsMTEuNiwxLjksMTUuMyw1LjZjMy42LDMuNyw1LjUsOC45LDUuNSwxNS41djYuMWgtMjkuOWMwLjEsMy42LDEuMiw2LjQsMy4yLDguNGMyLDIsNC44LDMsOC40LDMNCgkJYzIuOCwwLDUuNC0wLjMsNy45LTAuOWMyLjUtMC42LDUuMS0xLjUsNy44LTIuOHY5LjhjLTIuMiwxLjEtNC42LDEuOS03LjEsMi41QzIwMCw2OC43LDE5Ni45LDY4LjksMTkzLjMsNjguOXogTTE5MS41LDI5LjkNCgkJYy0yLjcsMC00LjgsMC44LTYuMywyLjVjLTEuNSwxLjctMi40LDQuMS0yLjYsNy4yaDE3LjdjLTAuMS0zLjEtMC45LTUuNS0yLjQtNy4yQzE5Ni4zLDMwLjcsMTk0LjIsMjkuOSwxOTEuNSwyOS45eiIvPg0KCTxwYXRoIGNsYXNzPSJzdDEiIGQ9Ik0yNDguMiwyMC45YzUuNSwwLDkuOCwyLjEsMTIuOSw2LjRjMy4xLDQuMyw0LjYsMTAuMSw0LjYsMTcuNmMwLDcuNy0xLjYsMTMuNi00LjgsMTcuOA0KCQljLTMuMiw0LjItNy41LDYuMy0xMyw2LjNjLTUuNCwwLTkuNy0yLTEyLjgtNS45aC0wLjlsLTIuMSw1LjFoLTkuN1YzLjZoMTIuNnYxNWMwLDEuOS0wLjIsNS0wLjUsOS4yaDAuNQ0KCQlDMjM4LjEsMjMuMiwyNDIuNCwyMC45LDI0OC4yLDIwLjl6IE0yNDQuMiwzMWMtMy4xLDAtNS40LDEtNi44LDIuOWMtMS40LDEuOS0yLjIsNS4xLTIuMiw5LjV2MS40YzAsNSwwLjcsOC41LDIuMiwxMC43DQoJCWMxLjUsMi4yLDMuOCwzLjIsNywzLjJjMi42LDAsNC43LTEuMiw2LjItMy42YzEuNS0yLjQsMi4zLTUuOSwyLjMtMTAuNGMwLTQuNi0wLjgtOC0yLjMtMTAuM0MyNDksMzIuMSwyNDYuOSwzMSwyNDQuMiwzMXoiLz4NCgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNMjg4LjMsNDIuOWw1LjUtN2wxMy0xNC4xaDE0LjNsLTE4LjQsMjAuMWwxOS41LDI2LjJoLTE0LjZsLTEzLjMtMTguOGwtNS40LDQuNHYxNC40aC0xMi42VjMuNmgxMi42djI4LjgNCgkJbC0wLjcsMTAuNUgyODguM3oiLz4NCgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNMzU4LjMsNjguMWwtMi40LTYuM2gtMC4zYy0yLjEsMi43LTQuMyw0LjUtNi42LDUuNmMtMi4zLDEtNS4yLDEuNi04LjgsMS42Yy00LjUsMC04LTEuMy0xMC41LTMuOA0KCQljLTIuNi0yLjUtMy44LTYuMi0zLjgtMTAuOWMwLTQuOSwxLjctOC41LDUuMi0xMC45YzMuNC0yLjMsOC42LTMuNiwxNS42LTMuOWw4LTAuMnYtMmMwLTQuNy0yLjQtNy03LjItN2MtMy43LDAtOC4xLDEuMS0xMy4xLDMuNA0KCQlsLTQuMi04LjVjNS4zLTIuOCwxMS4yLTQuMiwxNy43LTQuMmM2LjIsMCwxMSwxLjQsMTQuMyw0LjFzNSw2LjgsNSwxMi40djMwLjlIMzU4LjN6IE0zNTQuNSw0Ni42bC00LjksMC4yYy0zLjcsMC4xLTYuNCwwLjgtOC4yLDINCgkJYy0xLjgsMS4yLTIuNywzLjEtMi43LDUuNmMwLDMuNiwyLDUuMyw2LjEsNS4zYzIuOSwwLDUuMy0wLjgsNy0yLjVjMS44LTEuNywyLjYtMy45LDIuNi02LjdWNDYuNnoiLz4NCgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNNDA2LDIwLjljMS43LDAsMy4xLDAuMSw0LjMsMC40bC0xLDExLjljLTEtMC4zLTIuMy0wLjQtMy43LTAuNGMtNCwwLTcuMiwxLTkuNCwzLjFjLTIuMywyLjEtMy40LDUtMy40LDguNw0KCQl2MjMuNmgtMTIuNlYyMS43aDkuNmwxLjksNy44aDAuNmMxLjQtMi42LDMuNC00LjcsNS44LTYuM0M0MDAuNSwyMS43LDQwMy4xLDIwLjksNDA2LDIwLjl6Ii8+DQoJPHBhdGggY2xhc3M9InN0MSIgZD0iTTQ0NC4xLDY4LjljLTUuNCwwLTkuNy0yLTEyLjgtNS45aC0wLjdjMC40LDMuOSwwLjcsNi4xLDAuNyw2Ljd2MTguOGgtMTIuNlYyMS43SDQyOWwxLjgsNmgwLjYNCgkJYzMtNC42LDcuMy02LjksMTMuMS02LjljNS41LDAsOS44LDIuMSwxMi45LDYuM2MzLjEsNC4yLDQuNiwxMC4xLDQuNiwxNy42YzAsNC45LTAuNyw5LjItMi4yLDEyLjljLTEuNSwzLjYtMy41LDYuNC02LjIsOC4zDQoJCUM0NTAuOSw2OCw0NDcuOCw2OC45LDQ0NC4xLDY4Ljl6IE00NDAuNCwzMWMtMy4xLDAtNS40LDEtNi44LDIuOWMtMS40LDEuOS0yLjIsNS4xLTIuMiw5LjV2MS40YzAsNSwwLjcsOC41LDIuMiwxMC43DQoJCWMxLjUsMi4yLDMuOCwzLjIsNywzLjJjNS43LDAsOC41LTQuNyw4LjUtMTRjMC00LjYtMC43LTgtMi4xLTEwLjNDNDQ1LjYsMzIuMSw0NDMuNCwzMSw0NDAuNCwzMXoiLz4NCgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNNDkzLjQsNjguOWMtNy41LDAtMTMuMy0yLjEtMTcuNS02LjJjLTQuMi00LjEtNi4zLTEwLTYuMy0xNy41YzAtNy44LDEuOS0xMy44LDUuOC0xOA0KCQljMy45LTQuMiw5LjMtNi40LDE2LjEtNi40YzYuNiwwLDExLjYsMS45LDE1LjMsNS42YzMuNiwzLjcsNS41LDguOSw1LjUsMTUuNXY2LjFoLTI5LjljMC4xLDMuNiwxLjIsNi40LDMuMiw4LjRjMiwyLDQuOCwzLDguNCwzDQoJCWMyLjgsMCw1LjQtMC4zLDcuOS0wLjljMi41LTAuNiw1LjEtMS41LDcuOC0yLjh2OS44Yy0yLjIsMS4xLTQuNiwxLjktNy4xLDIuNUM1MDAuMSw2OC43LDQ5Nyw2OC45LDQ5My40LDY4Ljl6IE00OTEuNiwyOS45DQoJCWMtMi43LDAtNC44LDAuOC02LjMsMi41Yy0xLjUsMS43LTIuNCw0LjEtMi42LDcuMmgxNy43Yy0wLjEtMy4xLTAuOS01LjUtMi40LTcuMkM0OTYuNCwzMC43LDQ5NC4zLDI5LjksNDkxLjYsMjkuOXoiLz4NCgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNNTQyLjEsNTguOWMyLjIsMCw0LjktMC41LDgtMS41djkuNGMtMy4yLDEuNC03LDIuMS0xMS42LDIuMWMtNS4xLDAtOC43LTEuMy0xMS0zLjgNCgkJYy0yLjMtMi42LTMuNS02LjQtMy41LTExLjVWMzEuMmgtNi4xdi01LjNsNy00LjJsMy42LTkuOGg4LjF2OS45aDEzdjkuNWgtMTN2MjIuM2MwLDEuOCwwLjUsMy4xLDEuNSw0DQoJCUM1MzkuMSw1OC40LDU0MC40LDU4LjksNTQyLjEsNTguOXoiLz4NCgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNNTU3LjYsNjIuMmMwLTIuMywwLjYtNC4xLDEuOS01LjNjMS4yLTEuMiwzLjEtMS44LDUuNC0xLjhjMi4zLDAsNC4xLDAuNiw1LjMsMS44YzEuMywxLjIsMS45LDMsMS45LDUuMg0KCQljMCwyLjItMC42LDMuOS0xLjksNS4yYy0xLjMsMS4zLTMsMS45LTUuMywxLjljLTIuMywwLTQuMS0wLjYtNS40LTEuOEM1NTguMyw2Ni4xLDU1Ny42LDY0LjQsNTU3LjYsNjIuMnoiLz4NCgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNNjI2LjQsNjguMWgtMTIuNlY0MWMwLTMuMy0wLjYtNS45LTEuOC03LjVjLTEuMi0xLjctMy4xLTIuNS01LjctMi41Yy0zLjUsMC02LjEsMS4yLTcuNywzLjUNCgkJYy0xLjYsMi40LTIuNCw2LjMtMi40LDExLjh2MjEuOGgtMTIuNlYyMS43aDkuN2wxLjcsNS45aDAuN2MxLjQtMi4yLDMuNC0zLjksNS44LTUuMWMyLjUtMS4xLDUuMy0xLjcsOC40LTEuNw0KCQljNS40LDAsOS41LDEuNSwxMi4zLDQuNGMyLjgsMi45LDQuMiw3LjEsNC4yLDEyLjZWNjguMXoiLz4NCgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNNjUyLjEsNjguMWgtMTIuNlYzLjZoMTIuNlY2OC4xeiIvPg0KPC9nPg0KPGc+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTE2OS4zLDkyLjFoLTMuNWwxLjEtNC42aDIwLjhjMi40LDAsMy4yLDEsMi42LDMuNGwtMS4yLDQuOGgtNC45bDAuNi0yLjRjMC4yLTAuOC0wLjEtMS4yLTAuOS0xLjJoLTkuNA0KCQlsLTMuMSwxMi40aDEyLjlsLTEuMSw0LjNoLTEyLjlsLTQuMSwxNi40aC01LjNMMTY5LjMsOTIuMXoiLz4NCjwvZz4NCjwvc3ZnPg0K);
 }
</style>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>De voordeligste webshop in vloerkleden - Webkarpet.nl</title>
<base href="https://www.webkarpet.nl/" />
<meta name="description" content="Op zoek naar goedkope vloerkleden? ✓ Ruime collectie ✓ Binnen 1 werkdag in huis ✓ Bekend van TV ✓ Eigen productielijn ✓ Ruim 40% goedkoper" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link href="https://www.webkarpet.nl/image/catalog/Logo/favicon.png?=1482679240" rel="icon" />
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Belgrano" rel="stylesheet">
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/bootstrap.css?=1488881421" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/stylesheet.css?=1491317456" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/font.css?=1419861967" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/local/advice.css?=1505131268" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/local/custom.css?=1517991281" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/local/unic.css?=1514877058" rel="stylesheet" />
<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css?=1419861714" rel="stylesheet" />
<link href="catalog/view/javascript/jquery/magnific/magnific-popup.css?=1419861745" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/pavreassurance.css?=1420234239" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/pavproducttabs.css?=1419861988" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/pavverticalmenu/style.css?=1419861998" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/lastviewed.css?=1479815482" rel="stylesheet" />
<link href="catalog/view/theme/lexus_superstore_first/stylesheet/sliderlayer/css/typo.css?=1506501791" rel="stylesheet" />


<!-- Google Tag Manager -->
<script>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-T94B93');
</script>
<!-- End Google Tag Manager -->

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js?=1419861744"></script>
<script type="text/javascript" src="catalog/view/javascript/bootstrap/js/bootstrap.min.js?=1419861712"></script>
<script type="text/javascript" src="catalog/view/javascript/common.js?=1419861712"></script>
<script type="text/javascript" src="catalog/view/javascript/smoothscroll.js?=1517586335"></script>
<script type="text/javascript" src="catalog/view/theme/lexus_superstore_first/javascript/common.js?=1516885837"></script>
<script type="text/javascript" src="catalog/view/javascript/layerslider/jquery.themepunch.plugins.min.js?=1419861748"></script>
<script type="text/javascript" src="catalog/view/javascript/layerslider/jquery.themepunch.revolution.min.js?=1419861751"></script>
<script type="text/javascript" src="catalog/view/javascript/quiet-datetimepicker.js?=1456911222"></script>
    
<script type="text/javascript" src="/catalog/view/theme/lexus_superstore_first/javascript/custom.js?=1517576197"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-33067576-1', 'auto');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>

    


    
        <!-- Hotjar Tracking Code for https://webkarpet.nl -->
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:299257,hjsv:5};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');
</script>
    

			<link rel="stylesheet" href="catalog/view/javascript/jquery.cluetip.css?=1419938255" type="text/css" />
			<script src="catalog/view/javascript/jquery.cluetip.js?=1419938255" type="text/javascript"></script>
			
			<script type="text/javascript">
				$(document).ready(function() {
				$('a.title').cluetip({splitTitle: '|'});
				  $('ol.rounded a:eq(0)').cluetip({splitTitle: '|', dropShadow: false, cluetipClass: 'rounded', showtitle: false});
				  $('ol.rounded a:eq(1)').cluetip({cluetipClass: 'rounded', dropShadow: false, showtitle: false, positionBy: 'mouse'});
				  $('ol.rounded a:eq(2)').cluetip({cluetipClass: 'rounded', dropShadow: false, showtitle: false, positionBy: 'bottomTop', topOffset: 70});
				  $('ol.rounded a:eq(3)').cluetip({cluetipClass: 'rounded', dropShadow: false, sticky: true, ajaxCache: false, arrows: true});
				  $('ol.rounded a:eq(4)').cluetip({cluetipClass: 'rounded', dropShadow: false});  
				});
			</script>
			

                  

                  

                  
</head>
<body class="common-home page-home layout-">

                  

                  

                   

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T94B93"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    
<section  class="row-offcanvas row-offcanvas-left">

  <div id="page">


<!-- header -->
<div id="webkarpet-header">

<nav id="topbar" class="hidden-xs hidden-sm">
  <div class="container">
  	<div class="inner">
  		<div class="row hidden-sm hidden-xs">
  			<div class="col-lg-12 col-md-12 col-sm-12">
				<ul class="topbar-usp">
					<li><span class="shipping"></span>Gratis verzending boven de €150,-</li>
					<li><span class="time"></span>Voor 14:00 uur besteld, morgen in huis</li>
					<li><span class="check"></span>Voordeligste van Nederland</li>
				</ul>
			</div>
	   </div>
	</div> <!-- end inner -->
	<!--<div class="show-mobile hidden-lg hidden-md">
		<div class="quick-cart pull-left">
			<div class="quickaccess-toggle">
				<a class="shoppingcart" href="https://www.webkarpet.nl/index.php?route=checkout/cart"><span class="fa fa-shopping-cart"></span><span class="hide">Winkelwagen</span></a>
			</div>
		</div>
		<div class="quick-user pull-left">
			<div class="quickaccess-toggle">
				<i class="fa fa-user"></i>
			</div>
			<div class="inner-toggle">
				<ul class="links pull-left">
											<li><a href="https://www.webkarpet.nl/register"><span class="fa fa-pencil"></span>Registreren</a></li>
	    				<li><a href="https://www.webkarpet.nl/login"><span class="fa fa-unlock"></span>Inloggen</a></li>
    								</ul>
			</div>
		</div>
		<div class="quick-access pull-left">
			<div class="quickaccess-toggle">
				<i class="fa fa-list"></i>
			</div>
			<div class="inner-toggle">
				<ul class="links pull-left">
					<li><a class="wishlist" href="https://www.webkarpet.nl/wishlist" id="wishlist-total"><i class="fa fa-heart"></i>Verlanglijst (0)</a></li>
					<li><a class="account" href="https://www.webkarpet.nl/account"><span class="fa fa-user"></span>Mijn Account</a></li>
					<li><a class="last checkout" href="https://www.webkarpet.nl/index.php?route=checkout/checkout"><span class="fa fa-file"></span>Afrekenen</a></li>
				</ul>
			</div>
		</div>
	</div> -->
  </div>
</nav>

    <div id="mobile-header" class="hidden-lg hidden-md">
        <div class="row">
            <div class="container">
                <div class="col-xs-8">
                    <div id="logo-theme" class="logo-store">
                        <a href="https://www.webkarpet.nl/">
                            Webkarpet                        </a>
                    </div>
                </div>
                <div class="col-xs-2">
                    <div id="mobile-account" class="clearfix pull-right">
                        <button type="button" data-toggle="dropdown" data-loading-text="Laden..." class="dropdown-toggle">
                          <div class="mobile-account">
                             <img src="/catalog/view/theme/lexus_superstore_first/image/webkarpet-account.svg" width="45" height="40" alt="cart">
                          </div>
                        </button>
                        <ul class="dropdown-menu">
                                                                    <li><a href="https://www.webkarpet.nl/register"><span class="fa fa-pencil"></span>Registreren</a></li>
                                    <li><a href="https://www.webkarpet.nl/login"><span class="fa fa-unlock"></span>Inloggen</a></li>
                                                                    <li><a class="account" href="https://www.webkarpet.nl/account"><span class="fa fa-user"></span>Mijn Account</a></li>
                                    <li><a class="last checkout" href="https://www.webkarpet.nl/index.php?route=checkout/checkout"><span class="fa fa-file"></span>Afrekenen</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-2 shopping-cart inner">
                    <div id="cart" class="clearfix pull-right">
    <button type="button" data-toggle="dropdown" data-loading-text="Laden..." class="dropdown-toggle">
      <div class="webkarpet-cart">
         <img src="/catalog/view/theme/lexus_superstore_first/image/webkarpet-cart.svg" width="50" height="46" alt="cart">
          <div class="cart-number"> 0</div>
      </div>
    </button>
    <ul class="dropdown-menu">
            <li>
        <p class="text-center">U heeft nog geen producten in uw winkelwagen.</p>
      </li>
          </ul>
</div>
                </div>
            </div>
        </div>
    </div>
    
<header id="header-main">
	<div class="row">
		<div class="container">
			<div class="col-lg-5 col-md-4 hidden-sm hidden-xs logo inner">
								<div id="logo" class="logo-store">
					<a href="https://www.webkarpet.nl/">
						Webkarpet					</a>
				</div>
							</div>
			<div id="search" class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							 	<div class="input-group">
					<div id="search-auto" class="search-auto search-wrappe">
						<div class="search_block">
	<form method="GET" action="index.php">
		<div class="filter_type category_filter pull-left">
		<span class="fa fa-caret-down"></span>
		<select name="category_id">
			<option value="0">Alle categorieën</option>
				        	        <option value="302">Accessoires</option>
	        	        	        	        	        <option value="290">Bekend van TV</option>
	        	        	        	        <option value="142">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kunstgras op maat</option>
	        	        	        	        	        <option value="100">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Robs Grote Tuinverbouwing</option>
	        	        	        	        	        	        <option value="289">Exclusieve vloerkleden</option>
	        	        	        	        <option value="247">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleden Xilento</option>
	        	        	        	        <option value="249">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;170 x 230 cm</option>
	        	        	        	        <option value="248">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;200 x 300 cm</option>
	        	        	        	        	        	        <option value="285">Karpetten</option>
	        	        	        	        <option value="252">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleden 140x200 cm</option>
	        	        	        	        <option value="273">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Memphis</option>
	        	        	        	        <option value="271">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nashville</option>
	        	        	        	        	        <option value="286">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleden 160x230 cm</option>
	        	        	        	        <option value="235">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Adelaide</option>
	        	        	        	        <option value="229">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Canberra</option>
	        	        	        	        <option value="233">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gold Coast Stripe</option>
	        	        	        	        <option value="156">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Melbourne</option>
	        	        	        	        	        <option value="256">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleden 160x240 cm</option>
	        	        	        	        <option value="230">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Brisbane</option>
	        	        	        	        	        <option value="253">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleden 170x230 cm</option>
	        	        	        	        <option value="275">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Alaska Intense</option>
	        	        	        	        <option value="167">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casablanca Natura</option>
	        	        	        	        <option value="114">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casanova Valentino</option>
	        	        	        	        <option value="258">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Colorado</option>
	        	        	        	        <option value="131">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Florida</option>
	        	        	        	        <option value="311">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Four Seasons</option>
	        	        	        	        <option value="63">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hollywood</option>
	        	        	        	        <option value="261">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Memphis</option>
	        	        	        	        <option value="267">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nashville</option>
	        	        	        	        <option value="66">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;San Francisco</option>
	        	        	        	        <option value="96">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Boston (wol) </option>
	        	        	        	        	        <option value="257">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleden 200x290 cm</option>
	        	        	        	        <option value="236">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Adelaide</option>
	        	        	        	        <option value="232">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Brisbane</option>
	        	        	        	        <option value="231">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Canberra</option>
	        	        	        	        <option value="234">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gold Coast Stripe</option>
	        	        	        	        <option value="157">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Melbourne</option>
	        	        	        	        	        <option value="254">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleden 200x300 cm</option>
	        	        	        	        <option value="276">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Alaska Intense </option>
	        	        	        	        <option value="168">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casablanca Natura</option>
	        	        	        	        <option value="115">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casanova Valentino</option>
	        	        	        	        <option value="259">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Colorado</option>
	        	        	        	        <option value="132">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Florida</option>
	        	        	        	        <option value="312">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Four Seasons</option>
	        	        	        	        <option value="65">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hollywood</option>
	        	        	        	        <option value="262">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Memphis</option>
	        	        	        	        <option value="268">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nashville</option>
	        	        	        	        <option value="67">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;San Francisco</option>
	        	        	        	        <option value="97">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Boston (wol) </option>
	        	        	        	        	        <option value="287">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleden 300x400 cm</option>
	        	        	        	        <option value="134">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casanova Valentino</option>
	        	        	        	        <option value="137">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Florida</option>
	        	        	        	        <option value="138">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hollywood</option>
	        	        	        	        <option value="274">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Memphis</option>
	        	        	        	        <option value="272">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nashville</option>
	        	        	        	        <option value="135">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New York</option>
	        	        	        	        <option value="139">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;San Francisco</option>
	        	        	        	        	        	        <option value="171">Kleuren vloerkleden</option>
	        	        	        	        <option value="223">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Antraciet vloerkleed</option>
	        	        	        	        	        <option value="226">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Beige vloerkleed </option>
	        	        	        	        	        <option value="221">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bruin vloerkleed</option>
	        	        	        	        	        <option value="222">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grijs vloerkleed</option>
	        	        	        	        	        <option value="172">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Oranje vloerkleed</option>
	        	        	        	        	        <option value="173">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paars vloerkleed</option>
	        	        	        	        	        <option value="174">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rood vloerkleed</option>
	        	        	        	        	        <option value="175">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Roze vloerkleed</option>
	        	        	        	        	        <option value="220">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taupe vloerkleed</option>
	        	        	        	        	        <option value="180">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleed blauw</option>
	        	        	        	        	        <option value="181">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleed geel</option>
	        	        	        	        	        <option value="182">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleed groen</option>
	        	        	        	        	        <option value="183">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleed turquoise</option>
	        	        	        	        	        <option value="184">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleed zwart wit</option>
	        	        	        	        	        <option value="185">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Wit vloerkleed</option>
	        	        	        	        	        <option value="225">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zilver vloerkleed</option>
	        	        	        	        	        <option value="186">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zwart vloerkleed</option>
	        	        	        	        	        	        <option value="149">Natuur vloerkleed op maat </option>
	        	        	        	        <option value="151">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kokos Vloerkleed</option>
	        	        	        	        	        <option value="150">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Seagrass Vloerkleed</option>
	        	        	        	        	        <option value="152">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sisal vloerkleed</option>
	        	        	        	        	        	        <option value="144">Patchwork vloerkleed op maat</option>
	        	        	        	        	        <option value="288">Speciale vloerkleden</option>
	        	        	        	        <option value="145">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Natuur vloerkleden</option>
	        	        	        	        <option value="147">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kokos Vloerkleed</option>
	        	        	        	        <option value="148">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Seagrass Vloerkleed</option>
	        	        	        	        <option value="146">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sisal Vloerkleed</option>
	        	        	        	        	        <option value="143">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Patchwork vloerkleden</option>
	        	        	        	        	        <option value="255">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vintage vloerkleed</option>
	        	        	        	        	        <option value="215">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Wollen vloerkleed</option>
	        	        	        	        	        	        <option value="251">Vloerkleden Outlet</option>
	        	        	        	        	        <option value="119">Vloerkleed aanbiedingen</option>
	        	        	        	        	        <option value="228">Wollen vloerkleed op maat</option>
	        	        	        	        <option value="282">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Boston (wol)</option>
	        	        	        	        	        <option value="280">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dallas (wol)</option>
	        	        	        	        	        <option value="283">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nevada (wol)</option>
	        	        	        	        	        <option value="300">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Oakland (wol)</option>
	        	        	        	        	        <option value="301">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Phoenix (wol)</option>
	        	        	        	        	        	        <option value="72">Karpet van het jaar</option>
	        	        	        	        <option value="73">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Karpet 140 x 200 cm</option>
	        	        	        	        	        <option value="74">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Karpet 170 x 230 cm</option>
	        	        	        	        	        <option value="75">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Karpet 200 x 300 cm</option>
	        	        	        	        	        <option value="136">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Karpet 300 x 400 cm</option>
	        	        	        	        	        	        <option value="58">Goedkope vloerkleden</option>
	        	        	        	        <option value="59">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleed 170 x 230 cm</option>
	        	        	        	        	        <option value="60">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vloerkleed 200 x 300 cm</option>
	        	        	        	        	        	        <option value="129">Ronde vloerkleden</option>
	        	        	        	        <option value="210">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Alaska Intense</option>
	        	        	        	        	        <option value="241">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Arizona</option>
	        	        	        	        	        <option value="250">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Boston (wol stripe) </option>
	        	        	        	        	        <option value="238">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Brisbane</option>
	        	        	        	        	        <option value="244">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Carlton New</option>
	        	        	        	        	        <option value="203">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casablanca Natura</option>
	        	        	        	        	        <option value="204">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casanova Valentino</option>
	        	        	        	        	        <option value="239">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chicago New</option>
	        	        	        	        	        <option value="242">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Colorado</option>
	        	        	        	        	        <option value="217">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dallas (wol)</option>
	        	        	        	        	        <option value="307">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detroit </option>
	        	        	        	        	        <option value="211">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Florida</option>
	        	        	        	        	        <option value="309">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Four Seasons</option>
	        	        	        	        	        <option value="212">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hollywood</option>
	        	        	        	        	        <option value="213">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Houston</option>
	        	        	        	        	        <option value="295">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kempinski</option>
	        	        	        	        	        <option value="206">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Las Vegas</option>
	        	        	        	        	        <option value="193">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Melbourne</option>
	        	        	        	        	        <option value="264">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Memphis</option>
	        	        	        	        	        <option value="207">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Miami</option>
	        	        	        	        	        <option value="266">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nashville</option>
	        	        	        	        	        <option value="293">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nevada (wol)</option>
	        	        	        	        	        <option value="298">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Oakland (wol)</option>
	        	        	        	        	        <option value="214">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Patchwork</option>
	        	        	        	        	        <option value="299">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Phoenix (wol)</option>
	        	        	        	        	        <option value="306">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Portland</option>
	        	        	        	        	        <option value="303">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ronde natuurvloerkleden </option>
	        	        	        	        	        <option value="292">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Royal</option>
	        	        	        	        	        <option value="209">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;San Fransisco</option>
	        	        	        	        	        <option value="245">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Xilento Dream</option>
	        	        	        	        	        <option value="284">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Xilento Soft</option>
	        	        	        	        	        	        <option value="81">Vloerkleed op maat</option>
	        	        	        	        <option value="166">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Alaska Intense</option>
	        	        	        	        	        <option value="240">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Arizona </option>
	        	        	        	        	        <option value="98">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Boston (wol stripe)</option>
	        	        	        	        	        <option value="237">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Brisbane</option>
	        	        	        	        	        <option value="187">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Carlton New</option>
	        	        	        	        	        <option value="169">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casablanca Natura</option>
	        	        	        	        	        <option value="112">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casanova Valentino</option>
	        	        	        	        	        <option value="85">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chicago New</option>
	        	        	        	        	        <option value="243">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Colorado</option>
	        	        	        	        	        <option value="216">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dallas (wol)</option>
	        	        	        	        	        <option value="305">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detroit</option>
	        	        	        	        	        <option value="130">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Florida</option>
	        	        	        	        	        <option value="310">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Four Seasons</option>
	        	        	        	        	        <option value="83">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hollywood</option>
	        	        	        	        	        <option value="82">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Houston</option>
	        	        	        	        	        <option value="294">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kempinski</option>
	        	        	        	        	        <option value="87">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Las Vegas</option>
	        	        	        	        	        <option value="162">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Melbourne </option>
	        	        	        	        	        <option value="263">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Memphis</option>
	        	        	        	        	        <option value="260">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Miami </option>
	        	        	        	        	        <option value="265">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nashville</option>
	        	        	        	        	        <option value="279">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nevada (wol)</option>
	        	        	        	        	        <option value="296">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Oakland (wol)</option>
	        	        	        	        	        <option value="308">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Patchwork </option>
	        	        	        	        	        <option value="297">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Phoenix (wol)</option>
	        	        	        	        	        <option value="304">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Portland</option>
	        	        	        	        	        <option value="291">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Royal</option>
	        	        	        	        	        <option value="84">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;San Francisco</option>
	        	        	        	        	        <option value="246">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Xilento Dream</option>
	        	        	        	        	        <option value="269">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Xilento Soft</option>
	        	        	        	        	        	        <option value="116">Maandaanbieding op maat</option>
	        	        	        	        	        <option value="111">Patchwork Custom Made</option>
	        	        	        	        	        <option value="170">Casablanca Natura</option>
	        	        	        		</select>
	</div>
		<div id="search0" class="search pull-left">
	    <input type="text" value="" autocomplete="off" placeholder="Zoek een product..." name="search" class="input-search form-control">
	    <button type="submit" class="button button-search" name="submit_search"><i class="fa fa-search"></i></button>
	</div>
	<input type="hidden" name="sub_category" value="1" id="sub_category"/>
	<input type="hidden" name="route" value="product/search"/>
	<input type="hidden" name="sub_category" value="true" id="sub_category"/>
	<input type="hidden" name="description" value="true" id="description"/>
	</form>
	<div class="clear clr"></div>
</div>
<script type="text/javascript">

/* Autocomplete */
(function($) {
	function Autocomplete1(element, options) {
		this.element = element;
		this.options = options;
		this.timer = null;
		this.items = new Array();

		$(element).attr('autocomplete', 'off');
		$(element).on('focus', $.proxy(this.focus, this));
		$(element).on('blur', $.proxy(this.blur, this));
		$(element).on('keydown', $.proxy(this.keydown, this));

		$(element).after('<ul class="dropdown-menu autosearch"></ul>');
		$(element).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));
	}

	Autocomplete1.prototype = {
		focus: function() {
			this.request();
		},
		blur: function() {
			setTimeout(function(object) {
				object.hide();
			}, 200, this);
		},
		click: function(event) {
			event.preventDefault();
			value = $(event.target).parent().attr("href");
			if (value) {
				window.location = value.replace(/&amp;/gi,'&');
			}
		},
		keydown: function(event) {
			switch(event.keyCode) {
				case 27: // escape
					this.hide();
					break;
				default:
					this.request();
					break;
			}
		},
		show: function() {
			var pos = $(this.element).position();

			$(this.element).siblings('ul.dropdown-menu').css({
				top: pos.top + $(this.element).outerHeight(),
				left: pos.left
			});

			$(this.element).siblings('ul.dropdown-menu').show();
		},
		hide: function() {
			$(this.element).siblings('ul.dropdown-menu').hide();
		},
		request: function() {
			clearTimeout(this.timer);

			this.timer = setTimeout(function(object) {
				object.options.source($(object.element).val(), $.proxy(object.response, object));
			}, 200, this);
		},
		response: function(json) {
			console.log(json);
			html = '';

			if (json.length) {
				for (i = 0; i < json.length; i++) {
					this.items[json[i]['value']] = json[i];
				}

				for (i = 0; i < json.length; i++) {
					if (!json[i]['category']) {
						html += '<li class="media" data-value="' + json[i]['value'] + '">';
						if(json[i]['simage']) {
							html += '	<a class="media-left" href="' + json[i]['link'] + '"><img  src="' + json[i]['image'] + '"></a>';	
						}
						html += '<div class="media-body">';
						html += '	<a  href="' + json[i]['link'] + '"><span>' + json[i]['label'] + '</span></a>';
						if(json[i]['sprice']){
							html += '	<div>';
							if (!json[i]['special']) {
								html += json[i]['price'];
							} else {
								html += '<span class="price-old">' + json[i]['price'] + '</span><span class="price-new">' + json[i]['special'] + '</span>';
							}
							if (json[i]['tax']) {
								html += '<br />';
								html += '<span class="price-tax">Excl. BTW:' + json[i]['tax'] + '</span>';
							}
							html += '	</div> </div>';
						}
						html += '</li>';
					}
				}
				//html += '<li><a href="index.php?route=product/search&search='+g.term+'&category_id='+category_id+'&sub_category=true&description=true" onclick="window.location=this.href">'+text_view_all+'</a></li>';

				// Get all the ones with a categories
				var category = new Array();
				for (i = 0; i < json.length; i++) {
					if (json[i]['category']) {
						if (!category[json[i]['category']]) {
							category[json[i]['category']] = new Array();
							category[json[i]['category']]['name'] = json[i]['category'];
							category[json[i]['category']]['item'] = new Array();
						}
						category[json[i]['category']]['item'].push(json[i]);
					}
				}
				for (i in category) {
					html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';
					for (j = 0; j < category[i]['item'].length; j++) {
						html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
					}
				}
			}
			if (html) {
				this.show();
			} else {
				this.hide();
			}
			$(this.element).siblings('ul.dropdown-menu').html(html);
		}
	};

	$.fn.autocomplete1 = function(option) {
		return this.each(function() {
			var data = $(this).data('autocomplete');
			if (!data) {
				data = new Autocomplete1(this, option);
				$(this).data('autocomplete', data);
			}
		});
	}
})(window.jQuery);
$(document).ready(function() {
	var selector = '#search0';
	var total = 0;
	var show_image = true;
	var show_price = true;
	var search_sub_category = true;
	var search_description = true;
	var width = 102;
	var height = 102;

	$(selector).find('input[name=\'search\']').autocomplete1({
		delay: 500,
		source: function(request, response) {
			var category_id = $(".category_filter select[name=\"category_id\"]").first().val();
			if(typeof(category_id) == 'undefined')
				category_id = 0;
			var limit = 5;
			var search_sub_category = search_sub_category?'&sub_category=true':'';
			var search_description = search_description?'&description=true':'';
			$.ajax({
				url: 'index.php?route=module/pavautosearch/autocomplete&filter_category_id='+category_id+'&width='+width+'&height='+height+'&limit='+limit+search_sub_category+search_description+'&filter_name='+encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {		
					response($.map(json, function(item) {
						if($('.pavautosearch_result')){
							$('.pavautosearch_result').first().html("");
						}
						total = 0;
						if(item.total){
							total = item.total;
						}
						return {
							price:   item.price,
							speical: item.special,
							tax:     item.tax,
							label:   item.name,
							image:   item.image,
							link:    item.link,
							value:   item.product_id,
							sprice:  show_price,
							simage:  show_image,
						}
					}));
				}
			});
		},
	}); // End Autocomplete 

});// End document.ready

</script>					</div>
				</div>
							</div>
			<div class="col-lg-1 col-md-2 shopping-cart inner hidden-xs hidden-sm">
				<div id="cart" class="clearfix pull-right">
    <button type="button" data-toggle="dropdown" data-loading-text="Laden..." class="dropdown-toggle">
      <div class="webkarpet-cart">
         <img src="/catalog/view/theme/lexus_superstore_first/image/webkarpet-cart.svg" width="50" height="46" alt="cart">
          <div class="cart-number"> 0</div>
      </div>
    </button>
    <ul class="dropdown-menu">
            <li>
        <p class="text-center">U heeft nog geen producten in uw winkelwagen.</p>
      </li>
          </ul>
</div>
			</div>
		</div>
	</div>
</header>
<!-- menu -->

<div id="pav-mainnav">
	<div class="container">
		<div class="mainnav-wrap">
			<button data-toggle="offcanvas" class="btn btn-theme-default canvas-menu hidden-lg hidden-md" type="button"><span class="fa fa-bars"></span> Menu</button>
			

			<div class="pav-megamenu hidden-sm hidden-xs">
	<div class="navbar navbar-default">
		<div id="mainmenutop" class="megamenu" role="navigation">
			<div class="navbar-header">
			<button type="button" class="navbar-toggle hidden-lg hidden-md collapsed" data-toggle="collapse" data-target="#bs-megamenu">
		        <span class="fa fa-bars"></span>
		     </button>
			<div id="bs-megamenu" class="collapse navbar-collapse navbar-ex1-collapse hidden-sm hidden-xs">
				<ul class="nav navbar-nav megamenu"><li class="home" >
					<a href="https://webkarpet.nl"><span class="menu-title">Home</span></a></li><li class="parent dropdown  aligned-fullwidth" >
					<a class="dropdown-toggle" data-toggle="dropdown" href=""><span class="menu-title">Producten</span><b class="caret"></b></a><div class="dropdown-menu"><div class="menu-content"><div class="productmenu">        <div class="row">                <div class="col-md-3">                        <div class="inner">                                <h3><a href="/vloerkleed-op-maat">Vloerkleed op maat</a></h3>                <ul>                    <li><a href="/vloerkleed-op-maat">Vloerkleed op maat</a></li>                    <li><a href="/maandaanbieding-op-maat">Maandaanbieding op maat</a></li>                    <li><a href="/patchwork-vloerkleed-op-maat">Patchwork vloerkleed op maat</a></li>                    <li><a href="/natuur-vloerkleed-op-maat">Natuur vloerkleed op maat</a></li>                    <li><a href="/wollen-vloerkleed-op-maat">Wollen vloerkleed op maat</a></li>                </ul>                                <h3><a href="/rond-vloerkleed">Ronde vloerkleden</a></h3>                <ul>                    <li><a href="/rond-vloerkleed">Ronde vloerkleden</a></li><li><a href="/rond-vloerkleed/rond-natuur-vloerkleed">Ronde natuurvloerkleden</a></li>                </ul>                                </div>        </div>        <div class="col-md-3">            <div class="inner">                <h3><a href="/karpetten">Karpetten</a></h3>                    <ul>                        <li><a href="/karpetten/vloerkleden-140x200">Vloerkleden 140 x 200 cm</a></li>                        <li><a href="/karpetten/vloerkleden-160x230">Vloerkleden 160 x 230 cm</a></li>                        <li><a href="/karpetten/vloerkleden-160x240">Vloerkleden 160 x 240 cm</a></li>                        <li><a href="/karpetten/vloerkleden-170x230">Vloerkleden 170 x 230 cm</a></li>                        <li><a href="/karpetten/vloerkleden-200x290">Vloerkleden 200 x 290 cm</a></li>                        <li><a href="/karpetten/vloerkleden-200x300">Vloerkleden 200 x 300 cm</a></li>                        <li><a href="/karpetten/vloerkleden-300x400">Vloerkleden 300 x 400 cm</a></li>                    </ul>            </div>             </div>        <div class="col-md-3">                                    <div class="inner">                                                <h3><a href="/goedkope-vloerkleden">Goedkope vloerkleden</a></h3>                <ul>                    <li><a href="/vloerkleed-aanbieding">Vloerkleed aanbieding</a></li>                    <li><a href="/goedkope-vloerkleden">Goedkope vloerkleden</a></li>                    <li><a href="/vloerkleden-outlet">Vloerkleden outlet</a></li>                    <li><a href="/karpet-van-het-jaar">Karpet van het jaar: Houston</a></li>                </ul>                <h3><a href="/bekend-van-tv">Bekend van TV</a></h3>                <ul>                    <li><a href="/bekend-van-tv/Robs-Grote-Tuinverbouwing">Gezien bij Rob's Grote Tuinverbouwing</a></li>                    <li><a href="/bekend-van-tv/kunstgras">Kunstgras / Buitenkarpetten</a></li>                </ul>            </div>        </div>        <div class="col-md-3">                                    <div class="inner">                                                            <h3><a href="/speciale-vloerkleden">Speciale vloerkleden</a></h3>                <ul>                    <li><a href="/speciale-vloerkleden/patchwork-vloerkleden">Patchwork vloerkleden</a></li>                    <li><a href="/speciale-vloerkleden/vintage-vloerkleed">Vintage vloerkleden</a></li>                    <li><a href="/speciale-vloerkleden/natuur-vloerkleden/sisal-vloerkleed">Sisal vloerkleed</a></li>                    <li><a href="/speciale-vloerkleden/natuur-vloerkleden/kokos-vloerkleed">Kokos vloerkleed</a></li>                    <li><a href="/speciale-vloerkleden/natuur-vloerkleden/seagrass-vloerkleed">Zeegras vloerkleed</a></li>                    <li><a href="/speciale-vloerkleden/natuur-vloerkleden/wollen-vloerkleed">Wollen vloerkleed</a></li>                </ul>                <h3><a href="/exclusieve-vloerkleden">Exclusieve vloerkleden</a></h3>                <ul>                    <li><a href="/exclusieve-vloerkleden/xilento">Xilento vloerkleden</a></li>                    <li><a href="/vloerkleden-ambiant">Ambiant vloerkleden</a></li>                </ul>            </div>        </div>    </div></div></div></div></li><li class="" >
					<a href="https://www.webkarpet.nl/inspiratie"><span class="menu-title">Inspiratie</span></a></li><li class="" >
					<a href="https://www.webkarpet.nl/randafwerking"><span class="menu-title">Randafwerkingen</span></a></li><li class="" >
					<a href="/index.php?route=module/advice"><span class="menu-title">Vloerkleed advies</span></a></li><li class="" >
					<a href="https://www.webkarpet.nl/over-webkarpet"><span class="menu-title">Over Webkarpet</span></a></li><li class="" >
					<a href="https://www.webkarpet.nl/klantenservice"><span class="menu-title">Klantenservice</span></a></li></ul>			 </div>
			 </div>
		</div>
	</div>
</div>				

					</div>
	</div>
</div>
</div>
<script>
$('.navbar-nav.megamenu').append('<li class="samplesNavLink"><a href="/?route=module/sample"></a></li>');
$('.mainnav-wrap').append('<div class="samplesNavLink hidden-md hidden-lg"><a href="/?route=module/sample"></a></div>');
	
function updateSamplesLink(count) {
    var $link = $('.samplesNavLink');
    $link.toggle(count >= 0);
    $link.find('a').text('Stalendoosje (' + count + ')');
}

updateSamplesLink(0);
</script>

<!-- /header -->



<!-- sys-notification -->
<div id="sys-notification">
  <div class="container">
    <div id="notification"></div>
  </div>
</div>
<!-- /sys-notification -->

<div class="webkarpet-overlay" style="display:none;"></div>





<section class="showcase " id="pavo-showcase">
	<div class="container">
				<div class="row">	
		<div class="col-lg-3 col-md-3  "><div id="pav-verticalmenu" class="box pav-verticalmenu highlighted hidden-xs hidden-sm">
	<div class="box-heading">
		<span>Producten</span>
	</div>
	<div class="box-content">
		<div class="navbar navbar-default">
			<div id="verticalmenu" class="verticalmenu" role="navigation">
				<div class="navbar-header">
					<a href="javascript:;" data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
				        <span class="fa fa-bars"></span>
				     </a>
					<div class="collapse navbar-collapse navbar-ex1-collapse">
					<ul class="nav navbar-nav verticalmenu"><li class="parent dropdown " >
					<a class="dropdown-toggle" data-toggle="dropdown" href="https://www.webkarpet.nl/vloerkleed-op-maat"><span class="menu-title">Vloerkleed op maat</span><b class="caret"></b></a><div class="dropdown-menu"><div class="menu-content"><h3>Vloerkleed op maat</h3><ul><li><a href="/vloerkleed-op-maat">Vloerkleed op maat</a></li><li><a href="/maandaanbieding-op-maat">Maandaanbieding op maat</a></li><li><a href="/patchwork-vloerkleed-op-maat">Patchwork vloerkleed op maat</a></li><li><a href="/natuur-vloerkleed-op-maat">Natuur vloerkleed op maat</a></li><li><a href="/wollen-vloerkleed-op-maat">Wollen vloerkleed op maat</a></li></ul></div></div></li><li class="parent dropdown " >
					<a class="dropdown-toggle" data-toggle="dropdown" href="https://www.webkarpet.nl/rond-vloerkleed"><span class="menu-title">Ronde vloerkleden</span><b class="caret"></b></a><div class="dropdown-menu"><div class="menu-content"><h3>Ronde vloerkleden</h3><ul><li><a href="https://www.webkarpet.nl/rond-vloerkleed">Rond vloerkleed</a></li><li><a href="https://www.webkarpet.nl/rond-vloerkleed/rond-natuur-vloerkleed">Ronde natuurvloerkleden</a></li></ul></div></div></li><li class="parent dropdown " >
					<a class="dropdown-toggle" data-toggle="dropdown" href="https://www.webkarpet.nl/karpetten"><span class="menu-title">Karpetten</span><b class="caret"></b></a><div class="dropdown-menu"><div class="menu-content"><h3><a href="/karpetten">Karpetten</a></h3>                    <ul>                        <li><a href="/karpetten/vloerkleden-140x200">Vloerkleden 140 x 200 cm</a></li>                        <li><a href="/karpetten/vloerkleden-160x230">Vloerkleden 160 x 230 cm</a></li>                        <li><a href="/karpetten/vloerkleden-160x240">Vloerkleden 160 x 240 cm</a></li>                        <li><a href="/karpetten/vloerkleden-170x230">Vloerkleden 170 x 230 cm</a></li>                        <li><a href="/karpetten/vloerkleden-200x290">Vloerkleden 200 x 290 cm</a></li>                        <li><a href="/karpetten/vloerkleden-200x300">Vloerkleden 200 x 300 cm</a></li>                        <li><a href="/karpetten/vloerkleden-300x400">Vloerkleden 300 x 400 cm</a></li>                    </ul>            </div></div></li><li class="parent dropdown " >
					<a class="dropdown-toggle" data-toggle="dropdown" href="https://www.webkarpet.nl/goedkope-vloerkleden"><span class="menu-title">Goedkope vloerkleden</span><b class="caret"></b></a><div class="dropdown-menu"><div class="menu-content"><h3>Goedkope vloerkleden</h3><p>Productgroepen:</p><ul><li><a href="/vloerkleed-aanbieding">Vloerkleed aanbieding</a></li><li><a href="/goedkope-vloerkleden">Goedkope vloerkleden</a></li><li><a href="/vloerkleden-outlet">Vloerkleden outlet</a></li><li><a href="/karpet-van-het-jaar">Karpet van het jaar: Houston</a></li></ul></div></div></li><li class="parent dropdown " >
					<a class="dropdown-toggle" data-toggle="dropdown" href="https://www.webkarpet.nl/Robs-Grote-Tuinverbouwing"><span class="menu-title">Bekend van TV</span><b class="caret"></b></a><div class="dropdown-menu"><div class="menu-content"><h3>Rob's Grote Tuinverbouwing</h3>
<p>Wekelijks zijn wij te zien op SBS6 en SBS9 in het tv-programma Rob's Grote Tuinverbouwing. Bekijk hier de producten die in de uitzendingen zijn geweest.</p>
<ul>
<li><a href="/Robs-Grote-Tuinverbouwing">Rob's Grote Tuinverbouwing</a></li>
</ul></div></div></li><li class="parent dropdown " >
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="menu-title">Speciale vloerkleden</span><b class="caret"></b></a><div class="dropdown-menu"><div class="menu-content"><h3>Speciale vloerkleden</h3><p>Ruime collectie vloerkleden in allerlei maatvoeringen.</p><ul><ul>                    <li><a href="/rond-vloerkleed">Ronde vloerkleden</a></li>                    <li><a href="/patchwork-vloerkleden">Patchwork vloerkleden</a></li>                    <li><a href="/sisal-vloerkleed">Sisal vloerkleed</a></li>                    <li><a href="/kokos-vloerkleed">Kokos vloerkleed</a></li>                    <li><a href="/seagrass-vloerkleed">Zeegras vloerkleed</a></li>                    <li><a href="/wollen-vloerkleed">Wollen vloerkleed</a></li>                </ul></ul></div></div></li><li class="parent dropdown " >
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="menu-title">Exclusieve vloerkleden</span><b class="caret"></b></a><div class="dropdown-menu"><div class="menu-content"><h3>Exclusieve vloerkleden</h3><p>Vloerkleden met een exclusieve uitstraling of van een exclusief merk.</p><ul>                    <li><a href="/xilento">Xilento vloerkleden</a></li>                    <li><a href="/vloerkleden-ambiant">Ambiant vloerkleden</a></li>                </ul></div></div></li><li class="parent dropdown " >
					<a class="dropdown-toggle" data-toggle="dropdown" href="https://www.webkarpet.nl/accessoires"><span class="menu-title">Accessoires</span><b class="caret"></b></a><div class="dropdown-menu"><div class="menu-content"><h3>Accessoires</h3><ul><li><a href="https://www.webkarpet.nl/james-starterset">James Startersset</a></li></ul></div></div></li></ul>					</div>
				</div>
			</div>
		</div>
	</div>
</div></div>
			
					
		<div class="col-lg-9 col-md-9  "><div class="layerslider-wrapper" style="max-width:873px;">
			<div class="bannercontainer banner-boxed" style="padding: 0;margin: 0;">
					<div id="sliderlayer72319442" class="rev_slider boxedbanner" style="width:100%;height:457px; " >
						
						 
						<ul style="margin:0;padding:0;list-style-type:none;">
														
								<li  data-link="/vloerkleed-op-maat"  data-masterspeed="300"  data-transition="slideleft" data-slotamount="7" data-thumb="https://www.webkarpet.nl/image/catalog/slider/vloerkleed-op-maat.jpg" style="margin:0;">

																					
											<img src="https://www.webkarpet.nl/image/catalog/slider/vloerkleed-op-maat.jpg"  alt=""/>
																				
												
							</li>			
										 
						 
													
								<li  data-link="/karpetten"  data-masterspeed="300"  data-transition="slideleft" data-slotamount="7" data-thumb="https://www.webkarpet.nl/image/catalog/slider/karpetten.jpg" style="margin:0;">

																					
											<img src="https://www.webkarpet.nl/image/catalog/slider/karpetten.jpg"  alt=""/>
																				
												
							</li>			
										 
						 
													
								<li  data-link="https://www.webkarpet.nl/bekend-van-tv/Robs-Grote-Tuinverbouwing"  data-masterspeed="300"  data-transition="slideleft" data-slotamount="7" data-thumb="https://www.webkarpet.nl/image/catalog/slider/bekend-van-tv.jpg" style="margin:0;">

																					
											<img src="https://www.webkarpet.nl/image/catalog/slider/bekend-van-tv.jpg"  alt=""/>
																				
												
							</li>			
										 
						 
										 	
										 
						 
							 
						</ul>
											</div>
				</div>

 
 </div>
 

			<!--
			##############################
			 - ACTIVATE THE BANNER HERE -
			##############################
			-->
			<script type="text/javascript">

				var tpj=jQuery;
				 

			

				if (tpj.fn.cssOriginal!=undefined)
					tpj.fn.css = tpj.fn.cssOriginal;

					tpj('#sliderlayer72319442').revolution(
						{
							delay:9000,
							startheight:457,
							startwidth:873,


							hideThumbs:200,

							thumbWidth:100,						
							thumbHeight:50,
							thumbAmount:5,

							navigationType:"bullet",				
							navigationArrows:"verticalcentered",				
														navigationStyle:"round",			 
							 					
							navOffsetHorizontal:0,
							navOffsetVertical:20, 	

							touchenabled:"on",			
							onHoverStop:"on",						
							shuffle:"off",	
							stopAtSlide:-1,						
							stopAfterLoops:-1,						

							hideCaptionAtLimit:0,				
							hideAllCaptionAtLilmit:0,				
							hideSliderAtLimit:0,			
							fullWidth:"off",
							shadow:0	 
							 				 


						});



				

			</script>
</div>
		</div>	
			
	</div>
</section>




 
 

<div class="container">
  <div class="row"> 
  
   <section id="sidebar-main" class="col-md-12">
   		<div id="content">
   			<div class="content-top"><div class="box">
	<div class="box-heading"></div>
	<div class="box-content">
		<div class="row">    <div class="col-md-6">        <div class="box-rounded">            <div class="inner-left">                <h2><span>Webkarpet introduceert</span> Xilento</h2>                <p>Een exclusief eigen merk</p>                <a href="/xilento" class="btn btn-red">Bekijk de Xilento collectie</a>            </div>            <div class="image right">                <img src="/image/catalog/dev/home/xilento.jpg" alt="Xilento vloerkleden">            </div>        </div>    </div>    <div class="col-md-6">        <div class="box-rounded">            <div class="inner-right">                <h2>Vloerkleed <span>op maat</span></h2>                <p>Binnen 2 werkdagen in huis!</p>                <a href="/vloerkleed-op-maat" class="btn btn-red">Bekijk de collectie</a>            </div>            <div class="image left">                <img src="/image/catalog/dev/home/vloerkleed-op-maat.jpg" alt="vloerkleed op maat">            </div>        </div>    </div></div><div class="row">    <div class="col-md-6">        <div class="box-rounded">            <div class="inner-left">                <h2>Vloerkleed <span>advies</span></h2>                <p>Nieuwe service van Webkarpet</p>                <a href="/index.php?route=module/advice" class="btn btn-red">Lees verder</a>            </div>            <div class="image right">                <img src="/image/catalog/dev/home/advies.jpg" alt="advies vloerkleed">            </div>        </div>    </div>    <div class="col-md-6">        <div class="box-rounded">            <div class="inner-right">                <h2><span>Ronde</span> vloerkleden</h2>                <p>In alle doorsnedes</p>                <a href="/rond-vloerkleed" class="btn btn-red">Bekijk de ronde vloerkleden</a>            </div>            <div class="image left">                <img src="/image/catalog/dev/home/ronde-vloerkleden.jpg" alt="Ronde vloerkleden">            </div>        </div>    </div></div>	</div>
</div>
<div class="box box-normal border products-featured border-bottom">
<div class="box-heading">
	<h1>Fantastische vloerkleden <span>voor jou geselecteerd...</span></h1>
</div>  
<div class="box-content">
    <div class="products-block">
			
        <div class="row product-items">
             
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 product-cols">
                <div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-houston-zilver"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Zilver-Nieuw-270x203.jpg" alt="Vloerkleed Houston Zilver " title="Vloerkleed Houston Zilver " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Zilver-Nieuw.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Houston Zilver ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-houston-zilver">Vloerkleed Houston Zilver </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Houston Zilver .....</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			20,00</span>
					 
					<meta content="20,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('398');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('398');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('398');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





   	
            </div>		
            			
		 
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 product-cols">
                <div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-xilento-dream-kiezel"><img src="https://www.webkarpet.nl/image/cache/catalog/Xilento/xilento-kiezel-sfeer-270x203.jpg" alt="Vloerkleed Xilento Dream Kiezel" title="Vloerkleed Xilento Dream Kiezel" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Xilento/xilento-kiezel-sfeer.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Xilento Dream Kiezel">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-xilento-dream-kiezel">Vloerkleed Xilento Dream Kiezel</a></h3>
			 
				<div class="description" itemprop="description">Wordt verwacht februari 2018, u kunt ons altijd contacten. .....</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			65,00</span>
					 
					<meta content="65,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1373');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1373');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1373');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





   	
            </div>		
            			
		 
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 product-cols">
                <div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-houston-naturel-taupe-vernieuwde-kleur"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Naturel-270x203.jpg" alt="Vloerkleed Houston Naturel Taupe | Vernieuwde kleur " title="Vloerkleed Houston Naturel Taupe | Vernieuwde kleur " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Naturel.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Houston Naturel Taupe | Vernieuwde kleur ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-houston-naturel-taupe-vernieuwde-kleur">Vloerkleed Houston Naturel Taupe | Vernieuwde kleur </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Houston Naturel Taupe | Vernieuwde kleur .....</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			20,00</span>
					 
					<meta content="20,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('392');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('392');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('392');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





   	
            </div>		
            			
		 
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 product-cols">
                <div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-casanova-valentino-titaan-grijs-nieuw"><img src="https://www.webkarpet.nl/image/cache/catalog/valentino casanova /Valencia-casanova-Titaan-270x203.jpg" alt="Vloerkleed Casanova Valentino Titaan Grijs | NIEUW in de collectie " title="Vloerkleed Casanova Valentino Titaan Grijs | NIEUW in de collectie " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/valentino casanova /Valencia-casanova-Titaan.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Casanova Valentino Titaan Grijs | NIEUW in de collectie ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-casanova-valentino-titaan-grijs-nieuw">Vloerkleed Casanova Valentino Titaan Grijs | NIEUW in de collectie </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Casanova Valentino Titaan Grijs | NIEUW in de collectie .....</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			37,50</span>
					 
					<meta content="37,50" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('640');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('640');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('640');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





   	
            </div>		
                    </div>

				
			
        <div class="row product-items">
             
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 product-cols">
                <div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-xilento-dream-platina-grijs"><img src="https://www.webkarpet.nl/image/cache/catalog/Xilento/xilento-platina-grijs-270x203.jpg" alt="Rond Vloerkleed Xilento Dream Platina Grijs" title="Rond Vloerkleed Xilento Dream Platina Grijs" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Xilento/xilento-platina-grijs.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Xilento Dream Platina Grijs">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-xilento-dream-platina-grijs">Rond Vloerkleed Xilento Dream Platina Grijs</a></h3>
			 
				<div class="description" itemprop="description">Rond Vloerkleed Xilento Dream Platina Grijs.....</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			127,40</span>
					 
					<meta content="127,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1412');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1412');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1412');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





   	
            </div>		
            			
		 
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 product-cols">
                <div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-houston-Bruin-Grijs"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Bruin-Grijs-Nieuwe-kleur-270x203.jpg" alt="Rond Vloerkleed Houston Bruin Grijs" title="Rond Vloerkleed Houston Bruin Grijs" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Bruin-Grijs-Nieuwe-kleur.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Houston Bruin Grijs">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-houston-Bruin-Grijs">Rond Vloerkleed Houston Bruin Grijs</a></h3>
			 
				<div class="description" itemprop="description">Rond Vloerkleed Houston Bruin Grijs.....</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			47,43</span>
					 
					<meta content="47,43" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('787');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('787');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('787');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





   	
            </div>		
            			
		 
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 product-cols">
                <div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-memphis-snow-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/MEPPHIS/PISA-73-270x203.jpg" alt="Vloerkleed Memphis Snow | 170 x 230 cm" title="Vloerkleed Memphis Snow | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/MEPPHIS/PISA-73.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Memphis Snow | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-memphis-snow-170-x-230-cm">Vloerkleed Memphis Snow | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Memphis Snow | 170 x 230 cm.....</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			69,95</span>
					 
					<meta content="69,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1487');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1487');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1487');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





   	
            </div>		
            			
		 
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 product-cols">
                <div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-nashville-steel-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Nashville/NASHVILLE-77-270x203.jpg" alt="Vloerkleed Nashville Steel | 200 x 300 cm" title="Vloerkleed Nashville Steel | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Nashville/NASHVILLE-77.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Nashville Steel | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-nashville-steel-200-x-300-cm">Vloerkleed Nashville Steel | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Nashville Steel | 200 x 300 cm.....</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			149,95</span>
					 
					<meta content="149,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1504');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1504');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1504');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





   	
            </div>		
                    </div>

				
			
    </div>
</div>	  
</div>
		<div class="box-module-pavreassurances ">
				<div class="row box-outer">
																			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 column">
								<div class="reassurances-center">
									<span class="icon-name fa fa fa-truck"></span>
									<div class="description">
										<h4>Snelle levering</h4>
										<p>Bestel snel: Binnen 1 werkdag in huis!</p>										<!-- Button trigger modal -->
										<button type="button" class="arrow" data-toggle="modal" data-target="#myModal1"><i class="fa fa-long-arrow-right"></i></button>
										<div class="mask" style="display:none;">
											<p><br></p>										</div>
									</div>
								</div>
							</div>
							<!-- Modal -->
							<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							  <div class="modal-dialog">
							    <div class="modal-content">
							      <div class="modal-header">							        
							        <span class="icon-name fa fa fa-truck"></span>
							        <div class="description">
								        <h4>Snelle levering</h4>
								        <p>Bestel snel: Binnen 1 werkdag in huis!</p>								    </div>
							      </div>
							      <div class="modal-body">
							       		<p><br></p>							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							      </div>
							    </div>
							  </div>
							</div>
																				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 column">
								<div class="reassurances-center">
									<span class="icon-name fa fa-trophy"></span>
									<div class="description">
										<h4>De nummer 1</h4>
										<p>De grootste webshop in vloerkleden</p>										<!-- Button trigger modal -->
										<button type="button" class="arrow" data-toggle="modal" data-target="#myModal2"><i class="fa fa-long-arrow-right"></i></button>
										<div class="mask" style="display:none;">
											<p><br></p>										</div>
									</div>
								</div>
							</div>
							<!-- Modal -->
							<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							  <div class="modal-dialog">
							    <div class="modal-content">
							      <div class="modal-header">							        
							        <span class="icon-name fa fa-trophy"></span>
							        <div class="description">
								        <h4>De nummer 1</h4>
								        <p>De grootste webshop in vloerkleden</p>								    </div>
							      </div>
							      <div class="modal-body">
							       		<p><br></p>							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							      </div>
							    </div>
							  </div>
							</div>
																				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 column">
								<div class="reassurances-center">
									<span class="icon-name fa fa-shopping-cart"></span>
									<div class="description">
										<h4>Online betalen</h4>
										<p>iDeal, PayPal, Creditcard, Overboeking</p>										<!-- Button trigger modal -->
										<button type="button" class="arrow" data-toggle="modal" data-target="#myModal3"><i class="fa fa-long-arrow-right"></i></button>
										<div class="mask" style="display:none;">
											<p><br></p>										</div>
									</div>
								</div>
							</div>
							<!-- Modal -->
							<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							  <div class="modal-dialog">
							    <div class="modal-content">
							      <div class="modal-header">							        
							        <span class="icon-name fa fa-shopping-cart"></span>
							        <div class="description">
								        <h4>Online betalen</h4>
								        <p>iDeal, PayPal, Creditcard, Overboeking</p>								    </div>
							      </div>
							      <div class="modal-body">
							       		<p><br></p>							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							      </div>
							    </div>
							  </div>
							</div>
																				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 column">
								<div class="reassurances-center">
									<span class="icon-name fa fa fa-comments-o"></span>
									<div class="description">
										<h4>Klantenservice</h4>
										<p>Bel of mail voor een passend advies</p>										<!-- Button trigger modal -->
										<button type="button" class="arrow" data-toggle="modal" data-target="#myModal4"><i class="fa fa-long-arrow-right"></i></button>
										<div class="mask" style="display:none;">
											<p><br></p>										</div>
									</div>
								</div>
							</div>
							<!-- Modal -->
							<div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							  <div class="modal-dialog">
							    <div class="modal-content">
							      <div class="modal-header">							        
							        <span class="icon-name fa fa fa-comments-o"></span>
							        <div class="description">
								        <h4>Klantenservice</h4>
								        <p>Bel of mail voor een passend advies</p>								    </div>
							      </div>
							      <div class="modal-body">
							       		<p><br></p>							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							      </div>
							    </div>
							  </div>
							</div>
																	</div>
		</div>
	<div class="box producttabs">
  <div class="tab-nav">
	<ul class="nav nav-tabs" id="producttabs4">
					 <li><a href="#tab-latest4" data-toggle="tab">Nieuw</a></li>
					 <li><a href="#tab-bestseller4" data-toggle="tab">Best verkocht</a></li>
					 <li><a href="#tab-special4" data-toggle="tab">Afgeprijsd</a></li>
					 <li><a href="#tab-mostviewed4" data-toggle="tab">Meest bekeken</a></li>
			</ul>
  </div>


	<div class="tab-content">
					<div class="tab-pane box-products  tabcarousel4 slide" id="tab-latest4">

								<div class="carousel-controls">
					<a class="carousel-control left fa fa-angle-left" href="#tab-latest4"   data-slide="prev"></a>
					<a class="carousel-control right fa fa-angle-right" href="#tab-latest4"  data-slide="next"></a>
				</div>
								<div class="carousel-inner ">
				 				  						<div class="item active">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
				<!-- zoom image-->
	    	    <a href="" class="btn btn-theme-default product-zoom" title="test product 3">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/test-product-3">test product 3</a></h3>
			 
				<div class="description" itemprop="description">...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			0,00</span>
					 
					<meta content="0,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1848');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1848');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1848');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-silver"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-silver-270x203.jpg" alt="Rond Vloerkleed Four Seasons Silver" title="Rond Vloerkleed Four Seasons Silver" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-silver.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Silver">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-silver">Rond Vloerkleed Four Seasons Silver</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Silver...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1845');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1845');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1845');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-cognac"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-cognac-270x203.jpg" alt="Rond Vloerkleed Four Seasons Cognac" title="Rond Vloerkleed Four Seasons Cognac" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-cognac.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Cognac">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-cognac">Rond Vloerkleed Four Seasons Cognac</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Cognac...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1844');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1844');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1844');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ocean"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-ocean-270x203.jpg" alt="Rond Vloerkleed Four Seasons Ocean" title="Rond Vloerkleed Four Seasons Ocean" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-ocean.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Ocean">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ocean">Rond Vloerkleed Four Seasons Ocean</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Ocean...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1843');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1843');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1843');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-olive"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-olive-270x203.jpg" alt="Rond Vloerkleed Four Seasons Olive" title="Rond Vloerkleed Four Seasons Olive" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-olive.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Olive">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-olive">Rond Vloerkleed Four Seasons Olive</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Olive...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1842');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1842');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1842');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-platinum"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-platinum-2--270x203.jpg" alt="Rond Vloerkleed Four Seasons Platinum" title="Rond Vloerkleed Four Seasons Platinum" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-platinum-2-.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Platinum">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-platinum">Rond Vloerkleed Four Seasons Platinum</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Platinum...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1841');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1841');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1841');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-bronze"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-brons-270x203.jpg" alt="Rond Vloerkleed Four Seasons Bronze" title="Rond Vloerkleed Four Seasons Bronze" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-brons.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Bronze">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-bronze">Rond Vloerkleed Four Seasons Bronze</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Bronze...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1840');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1840');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1840');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ivory"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-ivory-270x203.jpg" alt="Rond Vloerkleed Four Seasons Ivory" title="Rond Vloerkleed Four Seasons Ivory" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-ivory.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Ivory">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ivory">Rond Vloerkleed Four Seasons Ivory</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Ivory...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1839');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1839');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1839');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ash-1837"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-ash-270x203.jpg" alt="Rond Vloerkleed Four Seasons Ash" title="Rond Vloerkleed Four Seasons Ash" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-ash.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Ash">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ash-1837">Rond Vloerkleed Four Seasons Ash</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Ash...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1837');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1837');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1837');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-silver-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-silver-270x203.jpg" alt="Vloerkleed Four Seasons Silver | 200 x 300 cm" title="Vloerkleed Four Seasons Silver | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-silver.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Silver | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-silver-200-x-300-cm">Vloerkleed Four Seasons Silver | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Silver&nbsp;| 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			585,00</span>
					 
					<meta content="585,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1836');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1836');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1836');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-silver-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-silver-270x203.jpg" alt="Vloerkleed Four Seasons Silver | 170 x 230 cm" title="Vloerkleed Four Seasons Silver | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-silver.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Silver | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-silver-170-x-230-cm">Vloerkleed Four Seasons Silver | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Silver&nbsp;| 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			375,00</span>
					 
					<meta content="375,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1835');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1835');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1835');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-platinum-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-platinum-2--270x203.jpg" alt="Vloerkleed Four Seasons Platinum | 200 x 300 cm" title="Vloerkleed Four Seasons Platinum | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-platinum-2-.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Platinum | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-platinum-200-x-300-cm">Vloerkleed Four Seasons Platinum | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Platinum&nbsp;| 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			585,00</span>
					 
					<meta content="585,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1834');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1834');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1834');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-platinum-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-platinum-2--270x203.jpg" alt="Vloerkleed Four Seasons Platinum | 170 x 230 cm" title="Vloerkleed Four Seasons Platinum | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-platinum-2-.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Platinum | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-platinum-170-x-230-cm">Vloerkleed Four Seasons Platinum | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Platinum&nbsp;| 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			375,00</span>
					 
					<meta content="375,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1833');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1833');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1833');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-olive-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-olive-270x203.jpg" alt="Vloerkleed Four Seasons Olive | 200 x 300 cm" title="Vloerkleed Four Seasons Olive | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-olive.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Olive | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-olive-200-x-300-cm">Vloerkleed Four Seasons Olive | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Olive&nbsp;| 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			585,00</span>
					 
					<meta content="585,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1832');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1832');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1832');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-olive-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-olive-270x203.jpg" alt="Vloerkleed Four Seasons Olive | 170 x 230 cm" title="Vloerkleed Four Seasons Olive | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-olive.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Olive | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-olive-170-x-230-cm">Vloerkleed Four Seasons Olive | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Olive&nbsp;| 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			375,00</span>
					 
					<meta content="375,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1831');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1831');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1831');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-ocean-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-ocean-270x203.jpg" alt="Vloerkleed Four Seasons Ocean | 200 x 300 cm" title="Vloerkleed Four Seasons Ocean | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-ocean.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Ocean | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-ocean-200-x-300-cm">Vloerkleed Four Seasons Ocean | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Ocean&nbsp;| 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			585,00</span>
					 
					<meta content="585,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1830');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1830');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1830');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  				</div>
			</div>
					<div class="tab-pane box-products  tabcarousel4 slide" id="tab-bestseller4">

								<div class="carousel-controls">
					<a class="carousel-control left fa fa-angle-left" href="#tab-bestseller4"   data-slide="prev"></a>
					<a class="carousel-control right fa fa-angle-right" href="#tab-bestseller4"  data-slide="next"></a>
				</div>
								<div class="carousel-inner ">
				 				  						<div class="item active">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/houston-zilver-karpet-170-230"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Zilver-Nieuw-270x203.jpg" alt="Karpet Houston Zilver | 170 x 230 cm" title="Karpet Houston Zilver | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Zilver-Nieuw.jpg" class="btn btn-theme-default product-zoom" title="Karpet Houston Zilver | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/houston-zilver-karpet-170-230">Karpet Houston Zilver | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Karpet Houston Zilver | 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			49,95</span>
					 
					<meta content="49,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('213');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('213');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('213');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a039"><img src="https://www.webkarpet.nl/image/cache/catalog/Vloerkleden - Miami/Barcelona-grijs-270x203.jpg" alt="Vloerkleed Miami Grijs | 170 x 230 cm" title="Vloerkleed Miami Grijs | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Vloerkleden - Miami/Barcelona-grijs.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Miami Grijs | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a039">Vloerkleed Miami Grijs | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Miami Grijs | 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			49,95</span>
					 
					<meta content="49,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('88');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('88');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('88');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/houston-bruin-grijs-karpet-170-230"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Bruin-Grijs-Nieuwe-kleur-270x203.jpg" alt="Karpet Houston Bruin Grijs | 170 x 230 cm" title="Karpet Houston Bruin Grijs | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Bruin-Grijs-Nieuwe-kleur.jpg" class="btn btn-theme-default product-zoom" title="Karpet Houston Bruin Grijs | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/houston-bruin-grijs-karpet-170-230">Karpet Houston Bruin Grijs | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Karpet Houston Bruin Grijs | 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			49,95</span>
					 
					<meta content="49,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('210');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('210');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('210');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/houston-zilver-karpet-200-300"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Zilver-Nieuw-270x203.jpg" alt="Karpet Houston Zilver | 200 x 300 cm" title="Karpet Houston Zilver | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Zilver-Nieuw.jpg" class="btn btn-theme-default product-zoom" title="Karpet Houston Zilver | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/houston-zilver-karpet-200-300">Karpet Houston Zilver | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Karpet Houston Zilver | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			79,95</span>
					 
					<meta content="79,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('224');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('224');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('224');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a007"><img src="https://www.webkarpet.nl/image/cache/catalog/op maat aanbieding /sevilla-groen-2-270x203.jpg" alt="Vloerkleed Sevilla Groen | 170 x 230 cm  " title="Vloerkleed Sevilla Groen | 170 x 230 cm  " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/op maat aanbieding /sevilla-groen-2.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Sevilla Groen | 170 x 230 cm  ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a007">Vloerkleed Sevilla Groen | 170 x 230 cm  </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Sevilla Groen | 170 x 230 cm  ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			69,95</span>
					<span class="price-old">			100,00</span> 
					 
					<meta content="69,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('56');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('56');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('56');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv200300g035"><img src="https://www.webkarpet.nl/image/cache/catalog/barcelona /Barcelona-Zwart-270x203.jpg" alt="Vloerkleed Miami Zwart  | 200 x 300 cm" title="Vloerkleed Miami Zwart  | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/barcelona /Barcelona-Zwart.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Miami Zwart  | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv200300g035">Vloerkleed Miami Zwart  | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Miami Zwart  | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			79,95</span>
					 
					<meta content="79,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('149');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('149');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('149');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/houston-turquoise-karpet-170-200"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /turqaise-houston-1-270x203.jpg" alt="Karpet Houston Turquoise | 170 x 230 cm " title="Karpet Houston Turquoise | 170 x 230 cm " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /turqaise-houston-1.jpg" class="btn btn-theme-default product-zoom" title="Karpet Houston Turquoise | 170 x 230 cm ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/houston-turquoise-karpet-170-200">Karpet Houston Turquoise | 170 x 230 cm </a></h3>
			 
				<div class="description" itemprop="description">Karpet Houston Turquoise | 170 x 230 cm ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			49,95</span>
					 
					<meta content="49,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('216');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('216');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('216');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/houston-antraciet-karpet-200-300"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Nieuw-Antraciet-270x203.jpg" alt="Karpet Houston Antraciet | 200 x 300 cm" title="Karpet Houston Antraciet | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Nieuw-Antraciet.jpg" class="btn btn-theme-default product-zoom" title="Karpet Houston Antraciet | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/houston-antraciet-karpet-200-300">Karpet Houston Antraciet | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Karpet Houston Antraciet | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			79,95</span>
					 
					<meta content="79,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('226');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('226');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('226');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a031"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Taupe-Houston-Nieuw-270x203.jpg" alt="Vloerkleed Taupe | 170 x 230 cm Nieuw" title="Vloerkleed Taupe | 170 x 230 cm Nieuw" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Taupe-Houston-Nieuw.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Taupe | 170 x 230 cm Nieuw">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a031">Vloerkleed Taupe | 170 x 230 cm Nieuw</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Taupe | 170 x 230 cm Nieuw...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			49,95</span>
					 
					<meta content="49,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('80');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('80');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('80');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/houston-parelmoer-karpet-170-230"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Parelmoer-Taupe-nieuw-270x203.jpg" alt="Karpet Houston Parelmoer Taupe | 170 x 230 cm" title="Karpet Houston Parelmoer Taupe | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Parelmoer-Taupe-nieuw.jpg" class="btn btn-theme-default product-zoom" title="Karpet Houston Parelmoer Taupe | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/houston-parelmoer-karpet-170-230">Karpet Houston Parelmoer Taupe | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Karpet Houston Parelmoer Taupe | 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			49,95</span>
					 
					<meta content="49,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('207');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('207');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('207');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/houston-naturel-karpet-200-300"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Naturel-270x203.jpg" alt="Karpet Houston Naturel Taupe | 200 x 300 cm" title="Karpet Houston Naturel Taupe | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Naturel.jpg" class="btn btn-theme-default product-zoom" title="Karpet Houston Naturel Taupe | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/houston-naturel-karpet-200-300">Karpet Houston Naturel Taupe | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Karpet Houston Naturel Taupe | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			79,95</span>
					 
					<meta content="79,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('219');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('219');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('219');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a009"><img src="https://www.webkarpet.nl/image/cache/catalog/2016/steenrood-270x203.png" alt="Vloerkleed Bordo Rood |170 x 230 cm " title="Vloerkleed Bordo Rood |170 x 230 cm " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/2016/steenrood.png" class="btn btn-theme-default product-zoom" title="Vloerkleed Bordo Rood |170 x 230 cm ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a009">Vloerkleed Bordo Rood |170 x 230 cm </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Bordo Rood |170 x 230 cm ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			39,95</span>
					 
					<meta content="39,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('58');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('58');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('58');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/houston-parelmoer-karpet-200-300"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /Houston-Parelmoer-Taupe-nieuw-270x203.jpg" alt="Karpet Houston Parelmoer Taupe | 200 x 300 cm" title="Karpet Houston Parelmoer Taupe | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /Houston-Parelmoer-Taupe-nieuw.jpg" class="btn btn-theme-default product-zoom" title="Karpet Houston Parelmoer Taupe | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/houston-parelmoer-karpet-200-300">Karpet Houston Parelmoer Taupe | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Karpet Houston Parelmoer Taupe | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			79,95</span>
					 
					<meta content="79,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('218');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('218');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('218');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/houston-bruin-karpet-200-300"><img src="https://www.webkarpet.nl/image/cache/catalog/Houston nieuw /houston-bruin-270x203.jpg" alt="Karpet Houston Bruin | 200 x 300 cm " title="Karpet Houston Bruin | 200 x 300 cm " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Houston nieuw /houston-bruin.jpg" class="btn btn-theme-default product-zoom" title="Karpet Houston Bruin | 200 x 300 cm ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/houston-bruin-karpet-200-300">Karpet Houston Bruin | 200 x 300 cm </a></h3>
			 
				<div class="description" itemprop="description">Karpet Houston Bruin | 200 x 300 cm ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			79,95</span>
					 
					<meta content="79,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('223');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('223');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('223');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv200300g001"><img src="https://www.webkarpet.nl/image/cache/catalog/barcelona /Barcelona-leembruin-nieuwe-kleur-vierkant-270x203.jpg" alt="Vloerkleed Miami Leembruin | 200 x 300 cm" title="Vloerkleed Miami Leembruin | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/barcelona /Barcelona-leembruin-nieuwe-kleur-vierkant.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Miami Leembruin | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv200300g001">Vloerkleed Miami Leembruin | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Miami Leembruin | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			79,95</span>
					 
					<meta content="79,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>
	            </span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('115');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('115');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('115');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a074"><img src="https://www.webkarpet.nl/image/cache/catalog/barcelona /Barcelona-Zwart-270x203.jpg" alt="Vloerkleed Miami Zwart | 170 x 230 cm" title="Vloerkleed Miami Zwart | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/barcelona /Barcelona-Zwart.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Miami Zwart | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a074">Vloerkleed Miami Zwart | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Miami Zwart | 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			49,95</span>
					 
					<meta content="49,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('688');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('688');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('688');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  				</div>
			</div>
					<div class="tab-pane box-products  tabcarousel4 slide" id="tab-special4">

								<div class="carousel-controls">
					<a class="carousel-control left fa fa-angle-left" href="#tab-special4"   data-slide="prev"></a>
					<a class="carousel-control right fa fa-angle-right" href="#tab-special4"  data-slide="next"></a>
				</div>
								<div class="carousel-inner ">
				 				  						<div class="item active">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-lexus-soft-parel-wit-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Lexus Soft/Lexus-Soft-Parel-Wit-270x203.jpg" alt="Vloerkleed Lexus Soft Parel Wit | 200 x 300 cm" title="Vloerkleed Lexus Soft Parel Wit | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Lexus Soft/Lexus-Soft-Parel-Wit.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Lexus Soft Parel Wit | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-lexus-soft-parel-wit-200-x-300-cm">Vloerkleed Lexus Soft Parel Wit | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Lexus Soft Parel Wit | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			149,95</span>
					<span class="price-old">			299,99</span> 
					 
					<meta content="149,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1658');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1658');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1658');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-lexus-soft-parel-wit-170-x-230-cm-"><img src="https://www.webkarpet.nl/image/cache/catalog/Lexus Soft/Lexus-Soft-Parel-Wit-270x203.jpg" alt="Vloerkleed Lexus Soft Parel Wit | 170 x 230 cm" title="Vloerkleed Lexus Soft Parel Wit | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Lexus Soft/Lexus-Soft-Parel-Wit.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Lexus Soft Parel Wit | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-lexus-soft-parel-wit-170-x-230-cm-">Vloerkleed Lexus Soft Parel Wit | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Lexus Soft Parel Wit | 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			99,95</span>
					<span class="price-old">			199,99</span> 
					 
					<meta content="99,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1654');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1654');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1654');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-lexus-soft-parel-grijs-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Lexus Soft/Lexus-Soft-Parel-Grijs-270x203.jpg" alt="Vloerkleed Lexus Soft Parel Grijs | 200 x 300 cm" title="Vloerkleed Lexus Soft Parel Grijs | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Lexus Soft/Lexus-Soft-Parel-Grijs.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Lexus Soft Parel Grijs | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-lexus-soft-parel-grijs-200-x-300-cm">Vloerkleed Lexus Soft Parel Grijs | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Lexus Soft Parel Grijs | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			149,95</span>
					<span class="price-old">			299,99</span> 
					 
					<meta content="149,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1657');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1657');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1657');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-lexus-soft-parel-grijs-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Lexus Soft/Lexus-Soft-Parel-Grijs-270x203.jpg" alt="Vloerkleed Lexus Soft Parel Grijs | 170 x 230 cm" title="Vloerkleed Lexus Soft Parel Grijs | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Lexus Soft/Lexus-Soft-Parel-Grijs.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Lexus Soft Parel Grijs | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-lexus-soft-parel-grijs-170-x-230-cm">Vloerkleed Lexus Soft Parel Grijs | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Lexus Soft Parel Grijs | 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			99,95</span>
					<span class="price-old">			199,99</span> 
					 
					<meta content="99,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1653');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1653');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1653');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-lexus-soft-grijs-gemeleerd-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Lexus Soft/Lexus-Soft-Grijs-Gemeleerd-270x203.jpg" alt="Vloerkleed Lexus Soft Grijs Gemeleerd | 200 x 300 cm" title="Vloerkleed Lexus Soft Grijs Gemeleerd | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Lexus Soft/Lexus-Soft-Grijs-Gemeleerd.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Lexus Soft Grijs Gemeleerd | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-lexus-soft-grijs-gemeleerd-200-x-300-cm">Vloerkleed Lexus Soft Grijs Gemeleerd | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Lexus Soft Grijs Gemeleerd | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			149,95</span>
					<span class="price-old">			299,99</span> 
					 
					<meta content="149,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1656');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1656');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1656');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-lexus-soft-grijs-gemeleerd-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Lexus Soft/Lexus-Soft-Grijs-Gemeleerd-270x203.jpg" alt="Vloerkleed Lexus Soft Grijs Gemeleerd | 170 x 230 cm" title="Vloerkleed Lexus Soft Grijs Gemeleerd | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Lexus Soft/Lexus-Soft-Grijs-Gemeleerd.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Lexus Soft Grijs Gemeleerd | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-lexus-soft-grijs-gemeleerd-170-x-230-cm">Vloerkleed Lexus Soft Grijs Gemeleerd | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Lexus Soft Grijs Gemeleerd | 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			99,95</span>
					<span class="price-old">			199,99</span> 
					 
					<meta content="99,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1652');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1652');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1652');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-lexus-soft-beige-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Lexus Soft/Lexus-Soft-Beige-270x203.jpg" alt="Vloerkleed Lexus Soft Beige | 200 x 300 cm" title="Vloerkleed Lexus Soft Beige | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Lexus Soft/Lexus-Soft-Beige.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Lexus Soft Beige | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-lexus-soft-beige-200-x-300-cm">Vloerkleed Lexus Soft Beige | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Lexus Soft Beige | 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			149,95</span>
					<span class="price-old">			299,99</span> 
					 
					<meta content="149,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1655');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1655');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1655');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-lexus-soft-beige-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Lexus Soft/Lexus-Soft-Beige-270x203.jpg" alt="Vloerkleed Lexus Soft Beige | 170 x 230 cm" title="Vloerkleed Lexus Soft Beige | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Lexus Soft/Lexus-Soft-Beige.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Lexus Soft Beige | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-lexus-soft-beige-170-x-230-cm">Vloerkleed Lexus Soft Beige | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Lexus Soft Beige | 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			99,95</span>
					<span class="price-old">			199,99</span> 
					 
					<meta content="99,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1124');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1124');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1124');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-Zilver-taupe-gemeleerd-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/2016/vegas-elite-zilver-Taupe-270x203.jpg" alt="Vloerkleed Vegas Elite Zilver Taupe Gemeleerd | 170 x 230 cm " title="Vloerkleed Vegas Elite Zilver Taupe Gemeleerd | 170 x 230 cm " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/2016/vegas-elite-zilver-Taupe.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Vegas Elite Zilver Taupe Gemeleerd | 170 x 230 cm ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-Zilver-taupe-gemeleerd-170-x-230-cm">Vloerkleed Vegas Elite Zilver Taupe Gemeleerd | 170 x 230 cm </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Vegas Elite Zilver Taupe Gemeleerd | 170 x 230 cm ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			75,00</span>
					<span class="price-old">			150,00</span> 
					 
					<meta content="75,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('776');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('776');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('776');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-Gemeleerd -taupe-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/2016/vintage-boucle-grijs-taupe-stripe-270x203.jpg" alt="Vloerkleed Vintage Boucle Grijs Taupe Stripe | 170 x 230 cm " title="Vloerkleed Vintage Boucle Grijs Taupe Stripe | 170 x 230 cm " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/2016/vintage-boucle-grijs-taupe-stripe.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Vintage Boucle Grijs Taupe Stripe | 170 x 230 cm ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-Gemeleerd -taupe-170-x-230-cm">Vloerkleed Vintage Boucle Grijs Taupe Stripe | 170 x 230 cm </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Vintage Boucle Grijs Taupe Stripe | 170 x 230 cm ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			39,95</span>
					<span class="price-old">			49,95</span> 
					 
					<meta content="39,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('761');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('761');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('761');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a077"><img src="https://www.webkarpet.nl/image/cache/catalog/2016/2017/vogue-zee-groen-270x203.jpg" alt="Vloerkleed Empire Zeegroen  | 170 x 230 cm   " title="Vloerkleed Empire Zeegroen  | 170 x 230 cm   " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/2016/2017/vogue-zee-groen.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Empire Zeegroen  | 170 x 230 cm   ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a077">Vloerkleed Empire Zeegroen  | 170 x 230 cm   </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Empire Zeegroen  | 170 x 230 cm   ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			100,00</span>
					<span class="price-old">			195,00</span> 
					 
					<meta content="100,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('737');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('737');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('737');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a073"><img src="https://www.webkarpet.nl/image/cache/catalog/2018/mystic-taupe-grey-270x203.jpg" alt="Vloerkleed Mystic Taupe Grey | 170 x 230 cm  " title="Vloerkleed Mystic Taupe Grey | 170 x 230 cm  " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/2018/mystic-taupe-grey.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Mystic Taupe Grey | 170 x 230 cm  ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a073">Vloerkleed Mystic Taupe Grey | 170 x 230 cm  </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Mystic Taupe Grey | 170 x 230 cm  ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			69,95</span>
					<span class="price-old">			100,00</span> 
					 
					<meta content="69,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('658');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('658');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('658');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-Mystic-taupe grey -200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/2018/mystic-taupe-grey-270x203.jpg" alt="Vloerkleed mystic taupe grey  | 200 x 300 cm  " title="Vloerkleed mystic taupe grey  | 200 x 300 cm  " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/2018/mystic-taupe-grey.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed mystic taupe grey  | 200 x 300 cm  ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-Mystic-taupe grey -200-x-300-cm">Vloerkleed mystic taupe grey  | 200 x 300 cm  </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed mystic taupe grey  | 200 x 300 cm  ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			100,00</span>
					<span class="price-old">			150,00</span> 
					 
					<meta content="100,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1623');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1623');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1623');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-casanova-valentino-kastanje-bruin-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/2016/2017/empire-melange-270x203.jpg" alt="Vloerkleed Empire Melange | 200 x 300 cm  " title="Vloerkleed Empire Melange | 200 x 300 cm  " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/2016/2017/empire-melange.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Empire Melange | 200 x 300 cm  ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-casanova-valentino-kastanje-bruin-200-x-300-cm">Vloerkleed Empire Melange | 200 x 300 cm  </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Empire Melange | 200 x 300 cm  ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			150,00</span>
					<span class="price-old">			250,00</span> 
					 
					<meta content="150,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1474');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1474');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1474');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/vloerkleed-toscane-dreams-pastel-blauw-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/2016/2017/ROMANCE-GROEN-270x203.jpg" alt="Vloerkleed Romance Robijn groen | 200 x 300 cm " title="Vloerkleed Romance Robijn groen | 200 x 300 cm " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/2016/2017/ROMANCE-GROEN.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Romance Robijn groen | 200 x 300 cm ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-toscane-dreams-pastel-blauw-200-x-300-cm">Vloerkleed Romance Robijn groen | 200 x 300 cm </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Romance Robijn groen | 200 x 300 cm ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			69,95</span>
					<span class="price-old">			89,95</span> 
					 
					<meta content="69,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('925');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('925');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('925');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      		    	<span class="product-label product-label-special"><span class="special">Aanbiedingen</span></span>
	    					<a class="img" href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a059"><img src="https://www.webkarpet.nl/image/cache/catalog/2016/clean-air-fuchsia-Roze-270x203.png" alt="Vloerkleed Fuchsia Roze | 170 x 230 cm Clean Air Karpet " title="Vloerkleed Fuchsia Roze | 170 x 230 cm Clean Air Karpet " class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/2016/clean-air-fuchsia-Roze.png" class="btn btn-theme-default product-zoom" title="Vloerkleed Fuchsia Roze | 170 x 230 cm Clean Air Karpet ">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/goedkope-vloerkleden-gv170230a059">Vloerkleed Fuchsia Roze | 170 x 230 cm Clean Air Karpet </a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Fuchsia Roze | 170 x 230 cm Clean Air Karpet ...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="price-new">			39,95</span>
					<span class="price-old">			49,95</span> 
					 
					<meta content="39,95" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('108');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('108');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('108');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  				</div>
			</div>
					<div class="tab-pane box-products  tabcarousel4 slide" id="tab-mostviewed4">

								<div class="carousel-controls">
					<a class="carousel-control left fa fa-angle-left" href="#tab-mostviewed4"   data-slide="prev"></a>
					<a class="carousel-control right fa fa-angle-right" href="#tab-mostviewed4"  data-slide="next"></a>
				</div>
								<div class="carousel-inner ">
				 				  						<div class="item active">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
				<!-- zoom image-->
	    	    <a href="" class="btn btn-theme-default product-zoom" title="test product 3">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/test-product-3">test product 3</a></h3>
			 
				<div class="description" itemprop="description">...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			0,00</span>
					 
					<meta content="0,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1848');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1848');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1848');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-silver"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-silver-270x203.jpg" alt="Rond Vloerkleed Four Seasons Silver" title="Rond Vloerkleed Four Seasons Silver" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-silver.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Silver">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-silver">Rond Vloerkleed Four Seasons Silver</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Silver...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1845');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1845');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1845');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-cognac"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-cognac-270x203.jpg" alt="Rond Vloerkleed Four Seasons Cognac" title="Rond Vloerkleed Four Seasons Cognac" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-cognac.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Cognac">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-cognac">Rond Vloerkleed Four Seasons Cognac</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Cognac...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1844');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1844');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1844');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ocean"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-ocean-270x203.jpg" alt="Rond Vloerkleed Four Seasons Ocean" title="Rond Vloerkleed Four Seasons Ocean" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-ocean.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Ocean">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ocean">Rond Vloerkleed Four Seasons Ocean</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Ocean...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1843');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1843');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1843');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-olive"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-olive-270x203.jpg" alt="Rond Vloerkleed Four Seasons Olive" title="Rond Vloerkleed Four Seasons Olive" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-olive.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Olive">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-olive">Rond Vloerkleed Four Seasons Olive</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Olive...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1842');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1842');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1842');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-platinum"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-platinum-2--270x203.jpg" alt="Rond Vloerkleed Four Seasons Platinum" title="Rond Vloerkleed Four Seasons Platinum" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-platinum-2-.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Platinum">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-platinum">Rond Vloerkleed Four Seasons Platinum</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Platinum...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1841');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1841');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1841');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-bronze"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-brons-270x203.jpg" alt="Rond Vloerkleed Four Seasons Bronze" title="Rond Vloerkleed Four Seasons Bronze" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-brons.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Bronze">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-bronze">Rond Vloerkleed Four Seasons Bronze</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Bronze...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1840');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1840');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1840');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ivory"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-ivory-270x203.jpg" alt="Rond Vloerkleed Four Seasons Ivory" title="Rond Vloerkleed Four Seasons Ivory" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-ivory.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Ivory">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ivory">Rond Vloerkleed Four Seasons Ivory</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Ivory...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1839');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1839');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1839');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ash-1837"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-ash-270x203.jpg" alt="Rond Vloerkleed Four Seasons Ash" title="Rond Vloerkleed Four Seasons Ash" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-ash.jpg" class="btn btn-theme-default product-zoom" title="Rond Vloerkleed Four Seasons Ash">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/rond-vloerkleed-four-seasons-ash-1837">Rond Vloerkleed Four Seasons Ash</a></h3>
			 
				<div class="description" itemprop="description">Rond&nbsp;Vloerkleed Four Seasons Ash...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			323,40</span>
					 
					<meta content="323,40" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1837');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1837');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1837');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-silver-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-silver-270x203.jpg" alt="Vloerkleed Four Seasons Silver | 200 x 300 cm" title="Vloerkleed Four Seasons Silver | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-silver.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Silver | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-silver-200-x-300-cm">Vloerkleed Four Seasons Silver | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Silver&nbsp;| 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			585,00</span>
					 
					<meta content="585,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1836');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1836');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1836');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-silver-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-silver-270x203.jpg" alt="Vloerkleed Four Seasons Silver | 170 x 230 cm" title="Vloerkleed Four Seasons Silver | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-silver.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Silver | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-silver-170-x-230-cm">Vloerkleed Four Seasons Silver | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Silver&nbsp;| 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			375,00</span>
					 
					<meta content="375,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1835');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1835');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1835');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-platinum-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-platinum-2--270x203.jpg" alt="Vloerkleed Four Seasons Platinum | 200 x 300 cm" title="Vloerkleed Four Seasons Platinum | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-platinum-2-.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Platinum | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-platinum-200-x-300-cm">Vloerkleed Four Seasons Platinum | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Platinum&nbsp;| 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			585,00</span>
					 
					<meta content="585,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1834');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1834');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1834');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-platinum-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-platinum-2--270x203.jpg" alt="Vloerkleed Four Seasons Platinum | 170 x 230 cm" title="Vloerkleed Four Seasons Platinum | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-platinum-2-.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Platinum | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-platinum-170-x-230-cm">Vloerkleed Four Seasons Platinum | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Platinum&nbsp;| 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			375,00</span>
					 
					<meta content="375,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1833');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1833');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1833');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-olive-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-olive-270x203.jpg" alt="Vloerkleed Four Seasons Olive | 200 x 300 cm" title="Vloerkleed Four Seasons Olive | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-olive.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Olive | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-olive-200-x-300-cm">Vloerkleed Four Seasons Olive | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Olive&nbsp;| 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			585,00</span>
					 
					<meta content="585,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1832');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1832');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1832');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  																								  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-olive-170-x-230-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-olive-270x203.jpg" alt="Vloerkleed Four Seasons Olive | 170 x 230 cm" title="Vloerkleed Four Seasons Olive | 170 x 230 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-olive.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Olive | 170 x 230 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-olive-170-x-230-cm">Vloerkleed Four Seasons Olive | 170 x 230 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Olive&nbsp;| 170 x 230 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			375,00</span>
					 
					<meta content="375,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1831');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1831');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1831');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  						<div class="item ">
																							  <div class="row product-items">
																	  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 product-cols">
									  	<div class="product-block item-default" itemtype="http://schema.org/Product" itemscope>

	<div class="image">
			      					<a class="img" href="https://www.webkarpet.nl/vloerkleed-four-seasons-ocean-200-x-300-cm"><img src="https://www.webkarpet.nl/image/cache/catalog/Four seasons /four-seasons-ocean-270x203.jpg" alt="Vloerkleed Four Seasons Ocean | 200 x 300 cm" title="Vloerkleed Four Seasons Ocean | 200 x 300 cm" class="img-responsive" /></a>
				<!-- zoom image-->
	    	    <a href="https://www.webkarpet.nl/image/catalog/Four seasons /four-seasons-ocean.jpg" class="btn btn-theme-default product-zoom" title="Vloerkleed Four Seasons Ocean | 200 x 300 cm">
	    	<i class="fa fa-search-plus"></i>
	    </a>
	    			</div>
	
	<div class="product-meta">
		<div class="warp-info">
			<h3 class="name"><a href="https://www.webkarpet.nl/vloerkleed-four-seasons-ocean-200-x-300-cm">Vloerkleed Four Seasons Ocean | 200 x 300 cm</a></h3>
			 
				<div class="description" itemprop="description">Vloerkleed Four Seasons Ocean&nbsp;| 200 x 300 cm...</div>
									<div class="price" itemtype="http://schema.org/Offer" itemscope itemprop="offers">
									<span class="special-price">			585,00</span>
					 
					<meta content="585,00" itemprop="price">
													<meta content="" itemprop="priceCurrency">
			</div>
			
				          <div class="rating">
	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	            	            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
	            	            	          </div>
	         		</div>

		<div class="action"> 				     

		    				<div class="cart">
					<button data-loading-text="Loading..." type="button" value="In winkelwagen" onclick="cart.addcart('1830');" class="btn btn-shopping-cart">In winkelwagen</button>		
				</div>
			
		    <!-- <div class="button-group hidden-xs">
		    	<div class="wishlist">					
					<a class="fa fa-heart product-icon" data-toggle="tooltip" title="Verlanglijst" onclick="wishlist.addwishlist('1830');"><span>Verlanglijst</span></a>	
				</div>
				<div class="compare">										
					<a class="fa fa-refresh product-icon" data-toggle="tooltip" title="Product vergelijk" onclick="compare.addcompare('1830');"><span>Product vergelijk</span></a>	
				</div>								
			</div> -->
		</div>
		

	</div>

</div>





  
									  </div>

							  								 </div>
																					</div>
				  				</div>
			</div>
			</div>
</div>


<script>
$(function () {
$('#producttabs4 a:first').tab('show');
})
$('.tabcarousel4').carousel({interval:false,auto:false,pause:'hover'});
</script>
<div class="box">
	<div class="box-heading">Home - tekst</div>
	<div class="box-content">
		<h2>Vloerkleden en karpetten</h2>
<p>Van goedkope <a href="https://www.webkarpet.nl/goedkope-vloerkleden" target="">vloerkleden&nbsp;</a>tot <a href="https://www.webkarpet.nl/vintage-vloerkleed" target="">vintage karpetten</a>, bij webkarpet.nl vind je altijd het juiste vloerkleed. Doordat al onze kleden uit eigen fabriek komen kunnen wij lage prijzen hanteren. Tegen fabrieksprijzen koop je dus de hoogste kwaliteit tegen een voordelige prijs. Voor extra sfeer en het optimale comfort zijn er onze <a href="https://www.webkarpet.nl/xilento" target="">Exclusieve Xilento kleden</a>. Het hoogpolig vloerkleed voelt lekker warm en comfortabel aan en wordt veel gebruikt bij de bank, in de woonkamer of op de slaapkamer. Ook de laagpolige bieden optimale luxe. Vaak wordt de loper in de keuken of hal gebruikt. Ook is er een ruime keus in de <a href="https://www.webkarpet.nl/goedkope-vloerkleden" target="">goedkope kleden</a> en de <a href="https://www.webkarpet.nl/vloerkleden-outlet" target="_blank">vloerkleden outlet</a>. Voor iedere vloer en ieder budget is er iets dat past en geschikt is. Alle kleden zijn in verschillende kleuren en maten leverbaar. Verhoog de sfeer in huis en maak het interieur compleet door te kiezen voor stijl, luxe en comfort tegen een voordelige prijs. Webkarpet, daar kies jij toch ook voor?</p>

<h2>Slaapkamer</h2>
<p>Wie een gladde of kale vloer heeft zal merken dat dit in de winter koud aan kan voelen. Een eenvoudige en passende oplossing is een karpet. Het voordeel hiervan is dat deze verschuifbaar is en de indeling van het slaapvertrek dus makkelijk aangepast kan worden. Een ander voordeel is het feit dat hier zo een enorme keus in is, dat deze altijd aangepast kan worden aan de inrichting. Daarnaast voelt het natuurlijk warm aan de voeten aan en wordt uitglijden zoveel mogelijk voorkomen. Het kleed kan stevig vastgelegd worden zodat er geen ongelukjes gebeuren. Ook dempt een kleed ongewenste geluiden zoals voetstappen.</p>

<h2>Babykamer en kinderkamer</h2>
<p>Baby's en kinderen houden van warmte, veiligheid en zachtheid. <a href="https://www.webkarpet.nl/goedkope-vloerkleden" target="">Karpetten</a> doen het dan ook altijd goed op de babykamer en de kinderkamer. Jonge maar ook oudere kinderen gaan er graag op zitten en spelen met hun speelgoed. Lego, Playmobiel of Barbie: met warme voetjes en warme billen spelen ze eindeloos door. Springen in de kamer? Een kleed dempt deze geluiden. Baby's oefenen hun spieren door het optillen van het hoofd vanuit buikligging, Daarna drukken ze zich op en beginnen langzaam met kruipen. Wist je dat dit prima op een zacht kleed geoefend kan worden? Doordat de kleden in leuke kleurtjes verkrijgbaar zijn wordt de kinderkamer opgevrolijkt. Slapen was nog nooit zo fijn geweest.</p>

<h2>Vloerkleed op maat gemaakt</h2>
<p>Een hoogpolig vloerkleed of een gewoon kleed kan op maat gemaakt worden. De standaard afmetingen voldoen immers niet altijd en wij vinden het belangrijk dat een <a href="https://www.webkarpet.nl/goedkope-vloerkleden" target="">vloerkleed</a> perfect past. Een kleine kamer komt niet mooi tot zijn recht met een te groot vloerkleed. En soms is het standaard kleed te klein voor een grote ruimte. Niet alleen de kleur en het materiaal is uit te kiezen, ook de afmeting. Kies voor een <a href="https://www.webkarpet.nl/vloerkleed-op-maat" target="">vloerkleed op maat</a> gemaakt en laat je interieur stralen. Altijd in de juiste verhoudingen.</p>

<h2>Modern en vintage</h2>
<p>Of je nu voor <a href="https://www.webkarpet.nl/wollen-vloerkleed" target="">wollen</a>, patchwork of <a href="https://www.webkarpet.nl/vintage-vloerkleed" target="">vintage</a> kiest, bij Webkarpet vind je echt alles . Wij proberen continue in te springen op de huidige wensen en eisen van onze klanten. Onze moderne kleden mogen gezien worden en de vintage kleden laten oude tijden herleven. <a href="https://www.webkarpet.nl/patchwork-vloerkleden" target="">Patchwork</a> staat voor lapjes stof in verschillende kleuren aaneen gemaakt. Oud of modern, met <a href="https://www.webkarpet.nl/patchwork-vloerkleden" target="">patchwork</a> valt jouw kleed zeker op. Helemaal aan te passen aan jouw persoonlijke stijl. Een ruim assortiment met de meest uiteenlopende kleden in alle kleuren en maten.</p>
	</div>
</div>
</div>
   			<div class="content-bottom"><script type="text/javascript">
(function() {
        _webwinkelkeur_id = 3717;
        _webwinkelkeur_sidebar = true;
        _webwinkelkeur_tooltip = true;
        _webwinkelkeur_sidebar_position = "left";
        var js = document.createElement("script"); js.type = "text/javascript";
    js.async = true; js.src = "//www.webwinkelkeur.nl/js/sidebar.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(js, s);
})();
</script>
<div class="webwinkelkeur-rich-snippet" itemscope=""
     itemtype="http://www.data-vocabulary.org/Review-aggregate"
     style="padding:10px;text-align:center;">
    <p>
        De waardering van <span itemprop="itemreviewed">www.webkarpet.nl</span> bij <a href="https://www.webwinkelkeur.nl/leden/Webkarpet_3717.html" target="_blank">Webwinkel Keurmerk Klantbeoordelingen</a> is <span itemprop="rating" itemscope="" itemtype="http://data-vocabulary.org/Rating"><span itemprop="average">9.7</span>/<span itemprop="best">10</span></span> gebaseerd op <span itemprop="votes">957</span> reviews.
    </p>
</div></div>
   		</div>
   </section> 
</div>
</div>

<script src="/catalog/view/javascript/custom.js?=1491547727"></script>

<!--
  $ospans: allow overrides width of columns base on thiers indexs. format array( column-index=>span number ), example array( 1=> 3 )[value from 1->12]
 -->


<section class="mass-bottom " id="pavo-mass-bottom">
	<div class="container">
				<div class="row">	
		<div class="col-lg-12 col-md-12  "><div class="lastviewed-container"></div>
<script type="text/javascript">
$(".lastviewed-container").load("index.php?route=module/lastviewed/getindex&product_id=");
</script></div>
		</div>	
			
	</div>
</section>



<footer id="footer">

  


      
<div class="footer-color-menu">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h3>Vind een vloerkleed <span>op kleur</span></h3>
            </div>
            <div class="col-sm-12">
                <ul class="color-nav">
                    <li><a href="/kleuren-vloerkleden/antraciet-vloerkleed"><span class="color antraciet"></span>Antraciet</a></li>
                    <li><a href="/kleuren-vloerkleden/beige-vloerkleed"><span class="color beige"></span>Beige</a></li>
                    <li><a href="/kleuren-vloerkleden/bruin-vloerkleed"><span class="color bruin"></span>Bruin</a></li>
                    <li><a href="/kleuren-vloerkleden/grijs-vloerkleed"><span class="color grijs"></span>Grijs</a></li>
                    <li><a href="/kleuren-vloerkleden/oranje-vloerkleed"><span class="color oranje"></span>Oranje</a></li>
                    <li><a href="/kleuren-vloerkleden/paars-vloerkleed"><span class="color paars"></span>Paars</a></li>
                    <li><a href="/kleuren-vloerkleden/rood-vloerkleed"><span class="color rood"></span>Rood</a></li>
                    <li><a href="/kleuren-vloerkleden/roze-vloerkleed"><span class="color roze"></span>Roze</a></li>
                    <li><a href="/kleuren-vloerkleden/taupe-vloerkleed"><span class="color taupe"></span>Taupe</a></li>
                    <li><a href="/kleuren-vloerkleden/vloerkleed-blauw"><span class="color blauw"></span>Blauw</a></li>
                    <li><a href="/kleuren-vloerkleden/vloerkleed-geel"><span class="color geel"></span>Geel</a></li>
                    <li><a href="/kleuren-vloerkleden/vloerkleed-turquoise"><span class="color turquoise"></span>Turquoise</a></li>
                    <li><a href="/kleuren-vloerkleden/vloerkleed-zwart-wit"><span class="color zwart-wit"></span>Zwart wit</a></li>
                    <li><a href="/kleuren-vloerkleden/wit-vloerkleed"><span class="color wit"></span>Wit</a></li>
                    <li><a href="/kleuren-vloerkleden/zilver-vloerkleed"><span class="color zilver"></span>Zilver</a></li>
                    <li><a href="/kleuren-vloerkleden/zwart-vloerkleed"><span class="color zwart"></span>Zwart</a></li>
                    <li><a href="/kleuren-vloerkleden/vloerkleed-groen"><span class="color groen"></span>Groen</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="footer-service">
     <div class="container">
        <div class="row">
           <div class="col-md-3 col-sm-6 col-xs-12">
            	<div class="footer-person">
            		<img src="/image/catalog/marielle1.png" alt="Marielle Klantenservice">
            	</div>
           </div>
           <div class="col-md-6 col-sm-12 col-xs-12">
             <h3>Hulp nodig?</h3>
             <div class="footer-service-title">Neem contact op met onze klantenservice</div>
             <div class="footer-service-item">
                <div class="footer-service-item-phone">
                   <div class="footer-service-item-title">Telefoon</div>
                   <div class="footer-service-item-text">038 2022304</div>
                </div>
             </div>
             <div class="footer-service-item">
                <div class="footer-service-item-mail">
                   <div class="footer-service-item-title">E-mail</div>
                   <div class="footer-service-item-text"><a href="mailto:info@webkarpet.nl">info@webkarpet.nl</a></div>
                </div>
             </div>
             <div class="footer-service-item">
                <div class="footer-service-item-chat">
                   <div class="footer-service-item-title">Live chat</div>
                   <div class="footer-service-item-text"><a href="javascript:$zopim.livechat.window.show()">Direct antwoord</a></div>
                </div>
             </div>
             <div class="footer-service-item">
                <div class="footer-service-item-mail">
                   <div class="footer-service-item-title">Online antwoord</div>
                   <div class="footer-service-item-text"><a href="/Veel-gestelde-vragen">Veelgestelde vragen</a></div>
                </div>
             </div>
           </div>
           <div class="col-md-3 col-sm-12 col-xs-12">
             <h3>Vertrouwd online shoppen</h3>
             <div class="footer-webwinkelkeur">
                <div class="footer-webwinkelkeur-title">Wij zijn aangesloten bij Webwinkelkeur.</div>
             </div>
             <div class="footer-webwinkelkeurbanner">
               <a href="https://www.webwinkelkeur.nl/leden/webkarpet_3717.html" target="_blank" class="webwinkelkeurPopup" title="WebwinkelKeur Webwinkel Keurmerk" ><img src="https://www.webwinkelkeur.nl/banners/180x120.png" alt="WebwinkelKeur Webwinkel Keurmerk" /></a>
             </div>
           </div>
        </div>
     </div>
  </div>
  <div class="footer-link">
    <div class="container">
        <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
            <h5>Informatie</h5>
            <ul class="list-unstyled">
              <li><a href="/over-webkarpet">Over Webkarpet.nl</a></li>
                <li><a href="/marktagenda">Marktagenda</a></li>
                <li><a href="/randafwerking">Randafwerkingen</a></li>
                <li><a href="/inspiratie">Inspiratie</a></li>
                <li><a href="/blog">Blog</a></li>
            </ul>
          </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
            <h5>Klantenservice</h5>
            <ul class="list-unstyled">
              <li><a href="/klantenservice">Klantenservice</a></li>
              <li><a href="https://www.webkarpet.nl/index.php?route=account/return/add">Retourneren</a></li>
                <li><a href="/privacybeleid">Privacybeleid</a></li>
              <li><a href="/algemene-voorwaarden">Algemene voorwaarden</a></li>
              
              
            </ul>
          </div>
          <div class="col-md-3 col-sm-6 col-xs-12">
            <h5>Extra</h5>
            <ul class="list-unstyled">
              <li><a href="/vacatures">Vacatures</a></li>
              <li><a href="https://www.webkarpet.nl/special">Aanbiedingen</a></li>
              <li><a href="https://www.webkarpet.nl/sitemap">Sitemap</a></li>
              <li><a href="https://www.webkarpet.nl/index.php?route=account/voucher">Cadeaubon</a></li>
            </ul>
          </div>
          <div class="col-md-3 col-sm-6 col-xs-12">
            <h5>Mijn account</h5>
            <ul class="list-unstyled">
              <li><a href="https://www.webkarpet.nl/account">Mijn account</a></li>
              <li><a href="https://www.webkarpet.nl/index.php?route=account/order">Bestelhistorie</a></li>
              <li><a href="https://www.webkarpet.nl/wishlist">Verlanglijst</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  
  
</footer>

<div id="powered">
  <div class="container">
    <div class="copyright pull-right">
                      <p><a href="https://www.okeonline.nl"><img src="/catalog/view/theme/lexus_superstore_first/image/OKE-Online-antra-wit.svg" width="120" alt="OKE Online"></a></p>
    </div>

          <div class="paypal pull-right">
        <p><br></p>      </div>
      </div>
</div>

<div id="top"><a class="scrollup" href="#"><i class="fa fa-angle-up"></i>Top</a></div>


</div>
<div class="sidebar-offcanvas visible-xs visible-sm">
    <div class="offcanvas-inner panel panel-offcanvas">
        <div class="offcanvas-heading panel-heading">
            <button data-toggle="offcanvas" class="btn btn-theme-default" type="button"><span class="fa fa-times"></span></button>
        </div>
        <div class="offcanvas-body panel-body">
            <div class="box category highlights">
                <div class="box-heading">
                    <span>Menu</span>
                </div>
                <div class="box-content" id="offcanvas-menu">
                </div>
            </div>
            
           <!-- <div class="box category highlights">
  <div class="box-heading"><span>Categorie&euml;n</span></div>
  <div class="box-content">
    <ul id="429875512accordion" class="box-category box-panel accordion">
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/accessoires">Accessoires</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/bekend-van-tv">Bekend van TV</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/exclusieve-vloerkleden">Exclusieve vloerkleden</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/karpetten">Karpetten</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/kleuren-vloerkleden">Kleuren vloerkleden</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/natuur-vloerkleed-op-maat ">Natuur vloerkleed op maat </a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/patchwork-vloerkleed-op-maat">Patchwork vloerkleed op maat</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/speciale-vloerkleden">Speciale vloerkleden</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/vloerkleden-outlet">Vloerkleden Outlet</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/aanbiedingen">Vloerkleed aanbiedingen</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/wollen-vloerkleed-op-maat">Wollen vloerkleed op maat</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/karpet-van-het-jaar">Karpet van het jaar</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/goedkope-vloerkleden">Goedkope vloerkleden</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/rond-vloerkleed">Ronde vloerkleden</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/vloerkleed-op-maat">Vloerkleed op maat</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/maandaanbieding-op-maat">Maandaanbieding op maat</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/patchwork-custom-made">Patchwork Custom Made</a>
                      </li>
            <li class="list-group-item accordion-group">
                <a href="https://www.webkarpet.nl/casablanca-natura">Casablanca Natura</a>
                      </li>
          </ul>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var active = $('.collapse.in').attr('id');
        $('span[data-target="#'+active+'"]').html("-");

      $('.collapse').on('show.bs.collapse', function () {
        $('span[data-target="#'+$(this).attr('id')+'"]').html("-");
    });
    $('.collapse').on('hide.bs.collapse', function () {
        $('span[data-target="#'+$(this).attr('id')+'"]').html("+");
    });
    });
</script>

 -->
        </div>
        <!--<div class="offcanvas-footer panel-footer">
            <div class="input-group" id="offcanvas-search">
                <input type="text" class="form-control" placeholder="Zoeken" value="" name="search">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div> -->
    </div>
</div>
<script>
(function() {
    var $ = jQuery;

    $menu = $('#bs-megamenu > ul').first().clone();
    $menu.prop('className', '');

    // $menu.find('.dropdown-menu').closest('li').remove();
    $menu.find('.samplesNavLink').remove();

    $('#offcanvas-menu').append($menu);

    window.setTimeout(function() {
        // $('button[data-toggle=offcanvas]').first().trigger('click');
    }, 100);
})();
</script>
</section>


<script src="//v2.zopim.com/?2kkghZBOcQ6NBt67bVAiNtJDpn2x847e" async defer></script>
<script src="//pixel.adcrowd.com/smartpixel/e4acb4c86de9d2d9a41364f93951028d.js" async defer></script>

<script>(function () {
(_wwk_id = (typeof _wwk_id !== 'undefined') ? _wwk_id : []).push(3717);
(_wwk_layout = (typeof _wwk_layout !== 'undefined') ? _wwk_layout : []).push('default');
(_wwk_theme = (typeof _wwk_theme !== 'undefined') ? _wwk_theme : []).push('light');
(_wwk_color = (typeof _wwk_color !== 'undefined') ? _wwk_color : []).push('#ea0e8b');
(_wwk_show = (typeof _wwk_show !== 'undefined') ? _wwk_show : []).push('yes');
(_wwk_view = (typeof _wwk_view !== 'undefined') ? _wwk_view : []).push('slider');
(_wwk_amount = (typeof _wwk_amount !== 'undefined') ? _wwk_amount : []).push(6);
(_wwk_width = (typeof _wwk_width !== 'undefined') ? _wwk_width : []).push('auto');
(_wwk_width_amount = (typeof _wwk_width_amount !== 'undefined') ? _wwk_width_amount : []).push('250px');
(_wwk_height = (typeof _wwk_height !== 'undefined') ? _wwk_height : []).push(0);
(_wwk_interval = (typeof _wwk_interval !== 'undefined') ? _wwk_interval : []).push(5000);
(_wwk_language = (typeof _wwk_language !== 'undefined') ? _wwk_language : []).push(1);
var js = document.createElement('script'), c = _wwk_id.length - 1;
js.className = 'wwk_script';
js.type = 'text/javascript';
js.async = true;
js.src = 'https://www.webwinkelkeur.nl/widget2.js?c=' + c;
var s = document.getElementsByClassName('wwk_script_container')[c];
s && s.parentNode.insertBefore(js, s);
})();</script>


						

          <!-- Google-code voor remarketingtag -->
<!--------------------------------------------------
Remarketingtags mogen niet worden gekoppeld aan gegevens waarmee iemand persoonlijk kan worden geïdentificeerd of op pagina's worden geplaatst die binnen gevoelige categorieën vallen. Meer informatie en instructies voor het instellen van de tag zijn te vinden op: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">

var google_tag_params = {
ecomm_pagetype: "home"
};
</script>
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 959220333;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/959220333/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
        

                  
</body></html>

<!-- Kibo Cache Buster ran in 0.001s -->
