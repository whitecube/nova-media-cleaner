<?php

namespace Tests\Repository;

use PHPUnit\Framework\TestCase;
use Whitecube\NovaMediaCleaner\Repository\Storage;
use Whitecube\NovaMediaCleaner\Exceptions\MissingRepositoryArgument;
use Whitecube\NovaMediaCleaner\Exceptions\BadRepositoryArgument;

class StorageTest extends TestCase {

    /** @test */
    public function cannot_find_files_without_directory_argument()
    {
        $this->expectException(MissingRepositoryArgument::class);

        new Storage();
    }

    /** @test */
    public function cannot_find_files_with_undefined_directory_path()
    {
        $this->expectException(BadRepositoryArgument::class);

        $path = __DIR__ . '/foobar';

        new Storage($path);
    }

    /** @test */
    public function cannot_find_files_from_file_path()
    {
        $this->expectException(BadRepositoryArgument::class);

        $path = __FILE__;

        new Storage($path);
    }

    /** @test */
    public function can_act_as_collection()
    {
        $path = __DIR__;

        $storage = new Storage($path);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $storage);
    }

    /** @test */
    public function can_access_to_configuration_variables()
    {
        $path = __DIR__ . '/../storage';

        $storage = new Storage($path);

        $this->assertSame(realpath($path), $storage->directory);
        $this->assertTrue($storage->recursive);
    }

    /** @test */
    public function can_find_files_from_directory_path()
    {
        $expected = [
            'bar/foo.txt',
            'foo/bar.txt',
            'some-random-file.txt',
            'some-random-image.png',
            'some-random.file.txt',
        ];

        $path = __DIR__ . '/../storage';

        $storage = new Storage($path);

        $this->assertCount(count($expected), $storage);

        foreach ($expected as $key => $file) {
            $this->assertSame($file, $storage->get($key));
        }
    }

    /** @test */
    public function can_ignore_sub_directory_files()
    {
        $expected = [
            'some-random-file.txt',
            'some-random-image.png',
            'some-random.file.txt',
        ];

        $path = __DIR__ . '/../storage';

        $storage = new Storage($path, false);

        $this->assertCount(count($expected), $storage);

        foreach ($expected as $key => $file) {
            $this->assertSame($file, $storage->get($key));
        }
    }

}
