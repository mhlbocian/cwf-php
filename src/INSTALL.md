CWF-PHP Framework
=================

# Installation instructions

## Requirements
* PHP version 8.4+ (with enabled modules: filter, json, PDO, session)
* Web server (tested on PHP development server)

## Run
Copy the whole contents of this directory into directory with right permissions
for the web server. Make sure, that "Config" and "Data" directories are
writeable. Set DOCROOT to the "Public" directory.

Default configuration is set to HTTP protocol, on port 8000 and DOCROOT url path
as `/`. If your configuration is different, remember to change settings in the
`url` section in the file `Config/application.json`.