<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 09/08/2013
 * Time: 02:42
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use Guzzle\Http\Client;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SearchIssuePatch
 * @package DrupalPatchUtils\Command
 */
class SearchIssuePatch extends PatchChooserBase {

  protected function configure()
  {
    $this
    ->setName('searchIssuePatch')
    ->setDescription('Searches a d.o drupal 8 issue patch for text')
    ->addArgument(
      'url',
      InputArgument::REQUIRED,
      'What is the url of the issue to retrieve?'
    )
    ->addArgument(
      'searchText',
      InputArgument::REQUIRED,
      'What is the text to search for?'
    )
    ->addOption(
      'regex',
      null,
      InputOption::VALUE_NONE,
      'If set use preg_match to search patch'
    );

  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $regex = $input->getOption('regex');
    $search_text = $input->getArgument('searchText');

    if ($issue = $this->getIssue($input->getArgument('url'))) {
      if ($patch = $this->choosePatch($issue, $input, $output)) {
        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
          $output->writeln('Searching ' . $patch);
        }

        $client = new Client();
        $request = $client->get($patch);
        $response = $request->send();
        if ($regex) {
          $found = preg_match($search_text, $response->getBody());
        }
        else {
          $found = strpos($response->getBody(), $search_text);
        }

        if ($found) {
          $output->writeln('<fg=green>Found text "'. $search_text .'" in '. $issue->getUri() . '</fg=green>');
        }
      }
    }
  }


}