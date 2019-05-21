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

namespace KevinGH\RequirementChecker;

use Generator;
use function getenv;
use PHPUnit\Framework\TestCase;
use function putenv;

/**
 * @covers \KevinGH\RequirementChecker\IO
 */
class IOTest extends TestCase
{
    /**
     * @var bool
     */
    private static $defaultExpectedInteractive;
    
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        // @see https://github.com/travis-ci/travis-ci/issues/7967
        // When a secure env var is present, the TTY is not passed correctly. The output is no longer interactive and
        // colored.
        self::$defaultExpectedInteractive = false !== getenv('TRAVIS_SECURE_ENV_VARS') ? true : false;
    }


    /**
     * @dataProvider provideOptions
     */
    public function test_it_can_parse_the_options(array $argv, bool $interactive, int $verbosity): void
    {
        $_SERVER['argv'] = $argv;

        $io = new IO();

        $this->assertSame($interactive, $io->isInteractive());
        $this->assertSame($verbosity, $io->getVerbosity());
    }

    /**
     * @dataProvider provideOptionsWithShellVerbosity
     */
    public function test_it_uses_the_shell_verbosity_environment_variable_over_the_options(array $argv, string $putenv, bool $interactive, int $verbosity): void
    {
        $_SERVER['argv'] = $argv;
        putenv($putenv);

        $io = new IO();

        $this->assertSame($interactive, $io->isInteractive());
        $this->assertSame($verbosity, $io->getVerbosity());
    }

    public function provideOptions(): Generator
    {
        yield [
            ['cli.php', '--foo'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_NORMAL,
        ];

        yield [
            ['cli.php', '--foo', '--verbose=0'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_VERBOSE,
        ];

        yield [
            ['cli.php', '--foo', '--quiet'],
            false,
            IO::VERBOSITY_QUIET,
        ];

        yield [
            ['cli.php', '--foo', '-q'],
            false,
            IO::VERBOSITY_QUIET,
        ];

        yield [
            ['cli.php', '--foo', '-vvv'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_DEBUG,
        ];

        yield [
            ['cli.php', '--foo', '--verbose=3'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_DEBUG,
        ];

        yield [
            ['cli.php', '--foo', '--verbose  3'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_DEBUG,
        ];

        yield [
            ['cli.php', '--foo', '-vv'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_VERY_VERBOSE,
        ];

        yield [
            ['cli.php', '--foo', '--verbose=2'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_VERY_VERBOSE,
        ];

        yield [
            ['cli.php', '--foo', '--verbose  2'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_VERY_VERBOSE,
        ];

        yield [
            ['cli.php', '--foo', '-v'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_VERBOSE,
        ];

        yield [
            ['cli.php', '--foo', '--verbose=1'],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_VERBOSE,
        ];

        yield [
            ['cli.php', '--foo', '--verbose  '],
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_VERBOSE,
        ];

        yield [
            ['cli.php', '--no-interaction'],
            false,
            IO::VERBOSITY_NORMAL,
        ];

        yield [
            ['cli.php', '-n'],
            false,
            IO::VERBOSITY_NORMAL,
        ];
    }

    public function provideOptionsWithShellVerbosity(): Generator
    {
        yield [
            ['cli.php', '--foo'],
            'SHELL_VERBOSITY=-1',
            false,
            IO::VERBOSITY_QUIET,
        ];

        yield [
            ['cli.php', '--foo'],
            'SHELL_VERBOSITY=0',
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_NORMAL,
        ];

        yield [
            ['cli.php', '--foo'],
            'SHELL_VERBOSITY=1',
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_VERBOSE,
        ];

        yield [
            ['cli.php', '--foo'],
            'SHELL_VERBOSITY=2',
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_VERY_VERBOSE,
        ];

        yield [
            ['cli.php', '--foo'],
            'SHELL_VERBOSITY=3',
            self::$defaultExpectedInteractive,
            IO::VERBOSITY_DEBUG,
        ];
    }
}
