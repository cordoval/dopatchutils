<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 08:29
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Configure extends Command {

  protected function configure()
  {
    $this
    ->setName('configure')
    ->setDescription('Configures d.o. patch utility');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $app = $this->getApplication();
    $dialog = $app->getHelperSet()->get('dialog');
    $dir = $dialog->askAndValidate($output, "Enter path to Drupal repository? ", array($this, 'validateDrupalRepo'), FALSE);

    $config = new Config();
    $config
      ->load()
      ->setDrupalRepoDir($dir)
      ->write();
  }

  public function validateDrupalRepo($dir) {
    if (!is_dir($dir)) {
      throw new \InvalidArgumentException(sprintf('"%s" is not a directory.', $dir));
    }
    if (!is_dir($dir . '/.git')) {
      throw new \InvalidArgumentException(sprintf('"%s" is not a git repository.', $dir));
    }
    if (!is_dir($dir . '/modules') && !is_dir($dir . '/themes')) {
      throw new \InvalidArgumentException(sprintf('"%s" is not likely to be Drupal. There are no modules and themes directories.', $dir));
    }
    return $dir;
  }

}