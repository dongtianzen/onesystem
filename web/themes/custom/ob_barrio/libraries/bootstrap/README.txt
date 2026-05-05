Bootstrap 5 local files
=======================

This theme is configured to load Bootstrap 5 locally. Do not use CDN files.

Place the compiled Bootstrap 5 files in these paths:

css/bootstrap.min.css
js/bootstrap.bundle.min.js

Expected final structure:

libraries/bootstrap/css/bootstrap.min.css
libraries/bootstrap/js/bootstrap.bundle.min.js

You can download Bootstrap 5 from the official Bootstrap website:
https://getbootstrap.com/

Use bootstrap.bundle.min.js because it includes Popper, which is required by
Bootstrap components such as dropdowns, tooltips, and popovers.
