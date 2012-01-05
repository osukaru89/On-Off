#!/bin/bash

php ./calledByCron.php `date +"%k %M %u %m %d"`
php ./daemonGetStatus.php `date +"%k %M %u"`
