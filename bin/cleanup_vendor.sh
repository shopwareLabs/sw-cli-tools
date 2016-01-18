#!/bin/bash

echo "Cleanup Vendors"
find vendor/fzaninotto/faker/src/Faker/Provider/ -maxdepth 1 -mindepth 1 -type d -not -name en_US  -exec rm -r {} \;
