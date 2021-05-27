#!/bin/bash
cd /srv/scripts/onemeter
php onemeter-process-data.php > log/onemeter.log
exit 0