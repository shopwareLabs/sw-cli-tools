
# Shopware CLI Tools

[![Build Status](https://img.shields.io/travis/shopwareLabs/sw-cli-tools/master.svg?style=flat-square)](https://travis-ci.org/shopwareLabs/sw-cli-tools)
[![Scrutinizer Score](https://img.shields.io/scrutinizer/g/shopwareLabs/sw-cli-tools.svg?style=flat-square)](https://scrutinizer-ci.com/g/shopwareLabs/sw-cli-tools)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

The shopware CLI tools are your console helpers for all kind of Shopware tasks. They will allow you to:

 * setup Shopware from VCS
 * setup Shopware from a release tag
 * create Shopware plugins
 * define a bunch of plugin repositories you use
 * install plugins from the repositories and activate them in shopware
 * zip plugins in the way the Shopware store or the Shopware plugin manager expect it to be

The shopware CLI tools come with a slim extension interface, so you can extend it for your needs.

This is an **early development preview** - so don't expect it to be stable. Please wait for a 1.0.0 stable release before using this tools for production deployments

 Pull requests are very welcome as well as ideas for possible extensions.

# Support

Use at your own risk, there is no support for this tools.

# Install

Generally we recommend to use the sw.phar release files, you can download them to e.g. ~/bin/sw and add this directory to the $PATH of your local user (download current build from http://shopwarelabs.github.io/sw-cli-tools/).

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
* sw plugin:zip:dir
* sw plugin:zip:vcs
* sw plugin:create
* sw generate

## Using the Commands
### sw plugin:install
Will install a plugin from one of the configured repos

Valid options/arguments are:

    --useHttp: use HTTP for checkout if possible (e.g. for VPN, SSH seems to be slow here sometimes)
    --small: Show 3 columns of plugins in a short form
    --shopware-root Shopware root directory
    --branch branch to checkout out
    names1…nameN - names of plugins to install

This command will automatically create a VCS mapping for phpstorm if `.idea/vcs.xml` can be found. You will need
to refresh you config with File->Sychronize (Control+Alt+Y)

### sw plugin:zip:vcs
Will checkout a plugin from VCS and zip it properly for the store

Valid options/arguments are:

    --useHttp: use HTTP for checkout if possible (e.g. for VPN, SSH seems to be slow here sometimes)
    --small: Show 3 columns of plugins in a short form
    --branch branch to checkout out
    names1…nameN - names of plugins to zip

If the plugins contains a file in its root named `.sw-zip-blacklist`, the files/directories described there, will be excluded from zipping

### sw plugin:zip:dir
Will zip the given plugin directory. DIRECTORY must point to the directory where the plugin bootstrap can be found.

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
    --release=RELEASE                Release version. Default: Latest. Possible are all shopware release versions like "4.2.0"
    --install-dir[=INSTALL-DIR]      Install directory
    --unpack-only                    Only unpack the downloaded release
    --skip-download                  Skip release downloading
    --db-host=DB-HOST                Database host [default: "localhost"]
    --db-port=DB-PORT                Database port [default: "3306"]
    --db-socket=DB-SOCKET            Database socket
    --db-user=DB-USER                Database user
    --db-password=DB-PASSWORD        Database password
    --db-name=DB-NAME                Database name
    --shop-locale=SHOP-LOCALE        Shop locale [default: "de_DE"]
    --shop-host=SHOP-HOST            Shop host [default: "localhost"]
    --shop-path=SHOP-PATH            Shop path [default: "/"]
    --shop-name=SHOP-NAME            Shop name [default: "Demo shop"]
    --shop-email=SHOP-EMAIL          Shop email address [default: "your.email@shop.com"]
    --shop-currency=SHOP-CURRENCY    Shop currency [default: "EUR"]
    --admin-username=ADMIN-USERNAME  Administrator username [default: "demo"]
    --admin-password=ADMIN-PASSWORD  Administrator password [default: "demo"]
    --admin-email=ADMIN-EMAIL        Administrator email address [default: "demo@demo.demo"]
    --admin-locale=ADMIN-LOCALE      Administrator locale [default: "de_DE"]
    --admin-name=ADMIN-NAME          Administrator name [default: "Demo user"]

### sw plugin:create
Will create a Shopware plugin with all the boilerplate code. If the command will be executed from the shopware root folder the plugins will be placed in the plugin folder, otherwise it will be created in the same directory.

Valid options / arguments are:

    --legacy: Create a legacy Plugin for Shopware versions lower than 5.2
    --namespace[="..."]: Namespace of the plugin, default: Frontend
    --haveBackend: Generate a backend Controller + a simple ExtJS module
    --backendModel[="..."]: The name of the model for your backend application
    --haveFilter: Generate Condition/Facet/CriteriaRequestHandler in order to add a new filter in the frontend
    --haveFrontend | -f: Generate a frontend controller
    --haveModels | -m: Generate a simple doctrine model
    --haveCommands | -c: Generate a console command
    --haveWidget | -w: Generate a backend widget
    --haveApi | -a: Generate an API resource + REST controller
    --licenseHeader[="/home/user/license.txt"]: Include a license header file at the beginning of any file name: Name of your plugin. Must at least have a dev prefix + plugin name, e.g. "SwagBundle", "PrefixPluginName"; "SwagBrowserLanguageDetection"
    --haveElasticSearch | -e: Generate ElasticSearch blueprint classes and an example of a decorator for the product number search.

The options will be read interactively, if not provided.
The database credentials are configured in ~/.config/sw-cli-tools/config.yaml

### sw generate
Will generate data which can be used to fill the shop for e.g. load tests.

!Warning! this command is destructive

Valid options / arguments are:

     -a, --articles[=ARTICLES]                          Number of articles to create
         --articleFilterGroups[=ARTICLEFILTERGROUPS]    Number article filter option groups to create
         --articleFilterOptions[=ARTICLEFILTEROPTIONS]  Number article filter options to create
         --articleFilterValues[=ARTICLEFILTERVALUES]    Number article filter values to create per each filter option
     -o, --orders[=ORDERS]                              Number of orders to create
     -c, --categories[=CATEGORIES]                      Number of categories to create
         --categoriesPerArticle[=CATEGORIESPERARTICLE]  Number of categories to assign to each article
     -e, --newsletter[=NEWSLETTER]                      Number of newsletter to create
     -u, --customers[=CUSTOMERS]                        Number of customers to create
         --vouchers[=VOUCHERS]                          Number of vouchers to create
         --chunk-size[=CHUNK-SIZE]                      Chung size
     -s, --seed[=SEED]                                  Random seed
         --installDir[=INSTALLDIR]                      Your Shopware installation path. If provided, data will be automatically injected into the configured database. [default: ""]
     -n, --no-interaction                               Do not ask any interactive question

If executed from a Shopware installation, or if 'installDir' is provided, the data will
be automatically written into your Shopware database.
Otherwise, data will be exported to individual files in the '<current dir>\output' folder
You need to import the .sql files in the order in which they are generated (see the command output)

Requires 'local-infile=1' in your MySQL installation.

# Extending the cli tools

## New Extensions

Extensions are created in the `/home/USER/.config/sw-cli-tools/extensions` folder and consist of a vendor folder, the extension folder (with the extension's name) and a `Bootstrap.php` inside the extension folder. Additionally you can provide an own `config.yaml` inside the extension folder which will extend the default `config.yaml`.

The namespace of your extension bootstrap should be `VENDOR_FOLDER\EXTENSION_FOLDER\Bootstrap`.

### Bootstrap
The bootstrap is the main entry point of your extension. If it implements `ContainerAwareExtension`, it will get the container builder
 of the application injected via setContainer. This way the container can be extended by your extension.
Additionally the Bootstrap can implement `ConsoleAwareExtension`. If this is the case, the method "getConsoleCommands" will be
called after creation - please return an array of console command instances here.
Finally the extension will call the `getRepositories` method of extensions implementing `RepositoryAwareInterface`.

### Changing existing components

As you Bootstrap.php is instantiated after the container, you can replace any service of the container

# Paths
The SW cli tools make use of the XDG directory standard. Following directories are used:

    ~/.config/sw-cli-tools: Here the main configuration as well as the extensions are stored
    ~/.cache/sw-cli-tools: Here caches (like repo content) as well as release downloads are cached
    ~/.local/share/sw-cli-tools: Assets the demo data package are stored here

If you changed some of these directories via `XDG` environment variables, those directories are used instead

# Configuration
The configuration of the script is done in `~/.config/sw-cli-tools/config.yaml`. If the file does not exist on your system, it is created after the first usage of the script.

# Building sw.phar
For building the release package (`sw.phar`) [box](http://box-project.github.io/box2/) is used.
A new release is build automatically for every push on the master branch by Travis CI (See `bin/deploy.sh`).


# Coding standard
Coding standard for the project is [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).
Coding standard violations may be detected using php-cs-fixer:

    ./vendor/bin/php-cs-fixer fix -v --level=psr2 ./src
    ./vendor/bin/php-cs-fixer fix -v --level=psr2 ./tests

# General hints
## Timeouts
If you are having a slow internet connection and e.g. git checkouts or tasks related to the `ProcessExecutor` fail
with a timeout, you can increase the timeout by setting the environment variable `SW_TIMEOUT`:

    SW_TIMEOUT=500 sw install:vcs
