#!/usr/bin/env bash
# Start the development server with large upload support
php -d post_max_size=512M -d upload_max_filesize=512M -d max_execution_time=300 -d memory_limit=512M "$(dirname "$0")/artisan" serve "$@"
