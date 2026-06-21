<?php

declare(strict_types=1);

namespace Tests\Codecov\Unit\Driver;

use Internal\Path;
use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Codecov\Internal\Driver\NormalizePath;
use Testo\Test;

#[Test]
#[Covers(NormalizePath::class)]
final class NormalizePathTest
{
    use NormalizePath {
        normalizePath as public;
    }

    public function directoryPathEndsWithSeparator(): void
    {
        $path = Path::create(__DIR__);

        $result = self::normalizePath($path);

        Assert::true(\str_ends_with($result, \DIRECTORY_SEPARATOR));
    }

    public function filePathDoesNotEndWithSeparator(): void
    {
        $path = Path::create(__FILE__);

        $result = self::normalizePath($path);

        Assert::string($result)->notContains(\DIRECTORY_SEPARATOR . \DIRECTORY_SEPARATOR);
        Assert::false(\str_ends_with($result, \DIRECTORY_SEPARATOR));
    }

    public function resultContainsNativeSeparators(): void
    {
        $path = Path::create(__DIR__);

        $result = self::normalizePath($path);

        // No forward slashes on Windows.
        if (\DIRECTORY_SEPARATOR === '\\') {
            Assert::false(\str_contains(\rtrim($result, '\\'), '/'));
        }
    }

    public function resultIsAbsolutePath(): void
    {
        $path = Path::create('src');

        $result = self::normalizePath($path);

        Assert::string($result)->contains(\DIRECTORY_SEPARATOR . 'src');
    }

    public function directoryPathAppendsOnlyOneSeparator(): void
    {
        $path = Path::create(__DIR__);

        $result = self::normalizePath($path);

        // No double separator at the end.
        $withoutTrailing = \rtrim($result, \DIRECTORY_SEPARATOR);
        Assert::same(\strlen($result) - \strlen($withoutTrailing), 1);
    }
}
