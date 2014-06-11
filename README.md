# Shopware 4 QA-CLI Tools

# Installation

## On Server ###
    aptitude update && aptitude install openjdk-7-jre-headless ant git curl php5-curl
    mkdir -p ~/bin && git clone ssh://git@stash.shopware.in:7999/TOOL/qa-cli-tools.git ~/bin/qa-cli-tools && cd ~/bin/qa-cli-tools/ && ./install.sh

The installer will add ~/bin to the users $PATH and install a ready only ssh key.

## General requirements
 * If you want to use coloring you should enable php.posix in your php.ini
 * In order to upload plugins, you need to have the php extension enabled (pecl install -f ssh2)

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

## New plugins

Plugins are created in the "/home/USER/.config/sw-cli-tools/plugins" folder and consist of the plugin folder (with the plugin's name) and a Bootstrap.php inside the plugin folder. Additionally you can provide an own config.yaml inside the plugin folder which will extend the default config.yaml.

### Bootstrap
The bootstrap is the main entry point of your plugin. If it implements ContainerAwarePlugin, it will get the container builder
 of the application injected upon creation. This way the container can be extended by your plugin.
Additionally the Bootstrap can implement ConsoleAwarePlugin. If this is the case, the method "getConsoleCommands" will be
called after creation - please return an array of console command instances here.

### Changing existing components

As you Bootstrap.php is instantiated after the container, you can replace any service of the container

# Paths
The SW cli tools make use of the XDG directory standard. Following directories are used:

    ~/.config/sw-cli-tools: Here the main configuration as well as the plugins are stored
    ~/.cache/sw-cli-tools: Here caches (like repo content) as well as release downloads are cached
    ~/.local/share/sw-cli-tools: Assets the demo data package are stored here

If you changed some of these directories via XDG environment variables, those directories are used instead

# Configuration
The configuration of the script is done in ~/.config/sw-cli-tools/config.yaml. If the file does not exist on your system, it is created after the first usage of the script.

# Building sw.phar
For building the release packages (sw.phar) box is used. It's recommended to uninstall dev-dependencies in order to keep the phar small.