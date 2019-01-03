#!/bin/bash

# Decrypt Secrets
openssl aes-256-cbc -K $encrypted_22aba2995326_key -iv $encrypted_22aba2995326_iv -in .travis/secrets.tar.enc -out .travis/secrets.tar -d

# Unpack secrets
tar xvf .travis/secrets.tar -C .travis

# Setup SSH agent
eval "$(ssh-agent -s)" #start the ssh agent
chmod 600 .travis/build-key.pem
ssh-add .travis/build-key.pem

# Setup git defaults
git config --global user.email "entwicklung@shopware.de "
git config --global user.name "shopwareBot"

# Remove some files from vendor directory
./bin/cleanup_vendor.sh

# Get box
./bin/download_box.sh

# Build PHAR
./box.phar build -vv
mv sw.phar sw.phar.tmp

# Add SSH-based remote
git remote add deploy git@github.com:shopwareLabs/sw-cli-tools.git
git fetch deploy
git branch -D gh-pages

# Checkout gh-pages and add PHAR file and version
git checkout -b gh-pages deploy/gh-pages
mv sw.phar.tmp sw.phar
sha1sum sw.phar > sw.phar.version
git add sw.phar sw.phar.version

# Commit and push
git commit -m 'Rebuilt phar'
git push deploy gh-pages:gh-pages
