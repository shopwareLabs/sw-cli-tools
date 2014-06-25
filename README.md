# Shopware CLI Tools

[![Build Status](https://travis-ci.org/ShopwareAG/sw-cli-tools.svg?branch=master)](https://travis-ci.org/ShopwareAG/sw-cli-tools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ShopwareAG/sw-cli-tools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ShopwareAG/sw-cli-tools/?branch=master)
[![License](https://poser.pugx.org/shopware-ag/cli-tools/license.svg)](https://packagist.org/packages/shopware-ag/cli-tools)

The shopware CLI tools are your console helpers for all kind of shopware tasks. They will allow you to:

 * setup shopware from VCS
 * setup shopware from a release tag
 * define a bunch of plugin repositories you use
 * install plugins from the repositories and activate them in shopware
 * zip plugins in the way the shopware store or the shopware plugin manager expect it to be

The shopware CLI tools come with a slim extension interface, so you can extend it for your needs.

This is an **early development preview** - so don't expect it to be stable. Please wait for a 1.0.0 stable release before using this tools for production deployments

 Pull requests are very welcome as well as ideas for possible extensions.

# Support

Use at your own risk, there is no support for this tools.

# Install

Generally we recommend to use the sw.phar release files, you can download them to e.g. ~/bin/sw and add this directory to the $PATH of your local user.

If you want to use the development version of sw-cli-tools, you should checkout the repository and run `composer install` to download all dependencies

## Requirements ###
On your system at least the following packages needs to be available.

 * openjdk-7-jre-headless
 * ant
 * git
 * curl
 * php5-curl
 * If you want to use coloring you should enable php.posix in your php.ini

## Available commands
* sw cache:clear
* sw install:vcs
* sw install:release
* sw plugin:install
* sw plugin:zip

## Using the Commands
### sw plugin:install
Will install a plugin from one of the configured repos

Valid options/arguments are:

    --useHttp: use HTTP for checkout if possible (e.g. for VPN, SSH seems to be slow here sometimes)
    --small: Show 3 columns of plugins in a short form
    --shopware-root Shopware root directory
    --branch branch to checkout out
    names1…nameN - names of plugins to install

This command will automatically create a VCS mapping for phpstorm if .idea/vcs.xml can be found. You will need
to refresh you config with File->Sychronize (Control+Alt+Y)

### sw plugin:zip
Will checkout a plugin from stash and zip it properly for the store

Valid options/arguments are:

    --useHttp: use HTTP for checkout if possible (e.g. for VPN, SSH seems to be slow here sometimes)
    --small: Show 3 columns of plugins in a short form
    --branch branch to checkout out
    names1…nameN - names of plugins to zip

### sw install:vcs
Checkout the latest shopware version from vcs (for development)

Valid options/arguments are:
    --branch: The branch to checkout
    --databaseName: Name of the database to use
    --installDir: Where to install shopware
    --user: Github user name. If provided, the checkout will be done via HTTP

The options will be read interactively, if not provided
The database credentials are configured in ~/.config/sw-cli-tools/config.yaml

### sw install:release
    --release: Release to install. Default: "latest". Possible are all shopware release versions like "4.2.0"
    --databaseName: Name of the database to use
    --basePath: Base path of the installation
    --username: backend user name for login
    --password: backend password for login
    --name: backend username
    --mail: Mail of the backend user
    --language: backend language (e.g. de_DE or en_GB)

The options will be read interactively, if not provided.
The database credentials are configured in ~/.config/sw-cli-tools/config.yaml

# Extending the cli tools

## New Extensions

Extensions are created in the "/home/USER/.config/sw-cli-tools/extensions" folder and consist of a vendor folder, the extension folder (with the extension's name) and a Bootstrap.php inside the extension folder. Additionally you can provide an own config.yaml inside the extension folder which will extend the default config.yaml.

The namespace of your extension bootstrap should be VENDOR_FOLDER\EXTENSION_FOLDER\Bootstrap.

### Bootstrap
The bootstrap is the main entry point of your extension. If it implements **ContainerAwareExtension**, it will get the container builder
 of the application injected via setContainer. This way the container can be extended by your extension.
Additionally the Bootstrap can implement **ConsoleAwareExtension**. If this is the case, the method "getConsoleCommands" will be
called after creation - please return an array of console command instances here.
Finally the extension will call the getRepositories method of extensions implementing **RepositoryAwareInterface will**

### Changing existing components

As you Bootstrap.php is instantiated after the container, you can replace any service of the container

# Paths
The SW cli tools make use of the XDG directory standard. Following directories are used:

    ~/.config/sw-cli-tools: Here the main configuration as well as the extensions are stored
    ~/.cache/sw-cli-tools: Here caches (like repo content) as well as release downloads are cached
    ~/.local/share/sw-cli-tools: Assets the demo data package are stored here

If you changed some of these directories via XDG environment variables, those directories are used instead

# Configuration
The configuration of the script is done in ~/.config/sw-cli-tools/config.yaml. If the file does not exist on your system, it is created after the first usage of the script.

# Building sw.phar
For building the release packages (sw.phar) [box](https://github.com/kherge/php-box) is used:

    box build

# Coding standard
Coding standard for the project is [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).
Coding standard violations may be detected using phpcs:

    ./vendor/bin/phpcs --standard=PSR2 ./src ./tests
