#!/bin/bash
set -e
set -o pipefail

# Remove some files from vendor directory
./bin/cleanup_vendor.sh

# Get box
./bin/download_box.sh

# Build PHAR
./box.phar compile -vv
