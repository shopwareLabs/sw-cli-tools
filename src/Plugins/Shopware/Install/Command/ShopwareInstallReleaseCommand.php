<?php
namespace Shopware\Install\Command;

use ShopwareCli\Command\BaseCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShopwareInstallReleaseCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('install:release')
            ->setDescription('Allows setting up shopware from release package.')
            ->addOption(
                'release',
                '-r',
                InputOption::VALUE_OPTIONAL,
                'Release version. Default: Latest'
            )
            ->addOption(
                'databaseName',
                '-d',
                InputOption::VALUE_OPTIONAL,
                'Name of database'
            )
            ->addOption(
                'installDir',
                'i',
                InputOption::VALUE_OPTIONAL,
                'Install directory'
            )
            ->addOption(
                'basePath',
                'p',
                InputOption::VALUE_OPTIONAL,
                'base path of the shop'
            )
            ->addOption(
                'username',
                null,
                InputOption::VALUE_REQUIRED,
                'User name of the backend user'
            )
            ->addOption(
                'password',
                null,
                InputOption::VALUE_REQUIRED,
                'password name of the backend user'
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'Full name of the backend user'
            )
            ->addOption(
                'mail',
                null,
                InputOption::VALUE_REQUIRED,
                'email of the backend user'
            )
            ->addOption(
                'language',
                null,
                InputOption::VALUE_OPTIONAL,
                'Language of the backend user. Currently only de_DE and en_GB are supported',
                'de_DE'
            )
            ->setHelp(
<<<EOF
            The <info>%command.name%</info> sets up shopware
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Shopware\Install\Services\Install\Release $installService */
        $installService = $this->container->get('shopware_release_install_service');

        $installService->installShopware(
            $input->getOption('username'),
            $input->getOption('password'),
            $input->getOption('name'),
            $input->getOption('mail'),
            $input->getOption('language'),
            $input->getOption('release'),
            trim($input->getOption('installDir'), '/'),
            $input->getOption('basePath'),
            $input->getOption('databaseName')
        );
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        $this->validateInput($input);

        /** @var \Symfony\Component\Console\Helper\DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        $required = array(
            'username' => 'backend user name',
            'password' => 'backend user password',
            'name' => 'your full name',
            'mail' => 'your email'
        );
        foreach ($required as $field => $description) {
            $fieldData = $input->getOption($field);
            if ($fieldData) {
                continue;
            }

            $fieldData = $dialog->askAndValidate(
                $output,
                "Please enter $description: ",
                array($this, 'genericValidator')
            );
            $input->setOption($field, $fieldData);
        }

        $release = $input->getOption('release');
        if (!$release) {
            $release = $dialog->ask($output, 'Please provide the release you want to install <latest>: ');
            $release = trim($release) ? $release : 'latest';
            $input->setOption('release', $release);
        }

        $suggestion = $release ?: 'latest';

        $installDir = $input->getOption('installDir');
        if (!$installDir) {
            $installDir = $dialog->askAndValidate(
                $output,
                "Please provide the install directory <{$suggestion}>: ",
                array($this, 'validateInstallDir')
            );
            $input->setOption('installDir', trim($installDir) ? $installDir : $suggestion);
        }

        $suggestion = $installDir ?: $suggestion;

        $databaseName = $input->getOption('databaseName');
        if (!$databaseName) {
            $databaseName = $dialog->ask($output, "Please provide the database name you want to use <{$suggestion}>: ");
            $input->setOption('databaseName', trim($databaseName) ? $databaseName : $suggestion);
        }

        $basePath = $input->getOption('basePath');
        if (!$basePath) {
            $basePath = $dialog->ask($output, "Please provide the basepath you want to use <{$suggestion}>: ");
            $input->setOption('basePath', trim($basePath) ? $basePath : $suggestion);
        }
    }

    public function genericValidator($input)
    {
        if (empty($input)) {
            throw new \RuntimeException("Field may not be empty");
        }

        return $input;
    }

    /**
     * @param $path
     * @return mixed
     * @throws \RuntimeException
     */
    public function validateInstallDir($path)
    {
        if (is_dir($path)) {
            throw new \RuntimeException("Path '{$path}'' is not empty");
        }

        return $path;
    }

    /**
     * @param  InputInterface    $input
     * @throws \RuntimeException
     */
    protected function validateInput(InputInterface $input)
    {
        $language = $input->getOption('language');
        if (!in_array($language, array('en_GB', 'de_DE'))) {
            throw new \RuntimeException("Invalid language: '$language'");
        }
    }
}
