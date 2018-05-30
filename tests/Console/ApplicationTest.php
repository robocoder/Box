<?php

declare(strict_types=1);

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace KevinGH\Box\Console;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @covers \KevinGH\Box\Console\Application
 */
class ApplicationTest extends TestCase
{
    public function test_it_can_display_the_version_when_no_specific_version_is_given(): void
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $appTester = new ApplicationTester($application);

        $input = ['--version'];

        $appTester->run($input);

        $this->assertSame(0, $appTester->getStatusCode());

        $expected = <<<'EOF'
Box (repo)

EOF;

        $actual = $appTester->getDisplay(true);

        $this->assertSame($expected, $actual);
    }

    public function test_it_can_display_the_version_when_a_specific_version_is_given(): void
    {
        $application = new Application('Box', '1.2.3');
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $appTester = new ApplicationTester($application);

        $input = ['--version'];

        $appTester->run($input);

        $this->assertSame(0, $appTester->getStatusCode());

        $expected = <<<'EOF'
Box version 1.2.3 build @git-commit@

EOF;

        $actual = $appTester->getDisplay(true);

        $this->assertSame($expected, $actual);
    }

    public function test_get_helper_menu(): void
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $appTester = new ApplicationTester($application);

        $appTester->run([]);

        $expected = <<<'EOF'

    ____            
   / __ )____  _  __
  / __  / __ \| |/_/
 / /_/ / /_/ />  <  
/_____/\____/_/|_|  
                    

Box (repo)

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  build     Builds a new PHAR (deprecated, use "compile" instead)
  compile   Compile an application into a PHAR
  diff      Display the differences between all of the files in two PHARs
  help      Displays help for a command
  info      Displays information about the PHAR extension or file
  list      Lists commands
  validate  Validates the configuration file
  verify    Verifies the PHAR signature

EOF;

        $actual = $appTester->getDisplay(true);

        $this->assertSame($expected, $actual);
    }
}
