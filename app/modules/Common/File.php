<?php
declare(strict_types=1);

namespace Common;

use Carbon\Carbon;
use Common\Entity\DirectoryEntity;
use Common\Entity\FileInfoEntity;
use Common\Entity\FileSizeEntity;
use Common\Exception\LogicException;

class File
{
    public static function write(string $file, string $text): bool
    {
        self::validateFileDirectoryEntityAndCreateIfNotFound($file);

        $fileWrite = file_put_contents($file, $text);

        return ($fileWrite || $text === '') ?? false;
    }

    // TODO: write test
    public static function delete(string $file): bool
    {
        if (!self::exists($file)) {
            throw new LogicException('File not found.');
        }

        if (is_dir($file)) {
            return rmdir($file);
        }

        return unlink($file);
    }

    // TODO: write test
    public static function move(string $from, string $to): bool
    {
        if (!self::exists($from)) {
            throw new LogicException('Source file not found.');
        }

        if (self::exists($to)) {
            throw new LogicException('Destination file already exists.');
        }

        return rename($from, $to);
    }

    public static function read(string $file): ?string
    {
        if (!self::exists($file)) {
            return null;
        }

        return file_get_contents($file);
    }

    public static function exists(string $file): bool
    {
        return file_exists($file);
    }

    public static function getInfo(string $file): ?FileInfoEntity
    {
        if (!self::exists($file)) {
            return null;
        }

        $fileInfo = (new FileInfoEntity())
            ->setType(mime_content_type($file))
            ->setHash(md5_file($file))
            ->setLastModified(Carbon::createFromTimestamp(filemtime($file)))
            ->setFileSize(self::getSize($file))
            ->setDirectory(self::getDirectory($file))
        ;

        $pathSplitter = explode('/', $file);
        $fileInfo->setFullName($pathSplitter[count($pathSplitter)-1]);

        $fileNameSplitter = explode('.', $fileInfo->getFullName());
        $fileInfo->setExt($fileNameSplitter[count($fileNameSplitter)-1]);

        foreach ($fileNameSplitter as $namePart) {
            $name = !isset($name) ? $namePart : $name . '.' . $namePart;
        }
        $fileInfo->setName(substr($name, 0, (strlen($fileInfo->getExt()) + 1) * -1));

        return $fileInfo;
    }

    public static function getSize(string $file): ?FileSizeEntity
    {
        if (!self::exists($file)) {
            return null;
        }

        $bytes = filesize($file);

        return (new FileSizeEntity())
            ->setBytes($bytes)
            ->setKilobytes((string)number_format($bytes/1024, 2, '.', ''))
            ->setMegabytes((string)number_format($bytes/1024/1024, 2, '.', ''))
            ->setGigabytes((string)number_format($bytes/1024/1024/1024, 2, '.', ''))
        ;
    }

    public static function getDirectory(string $file): ?DirectoryEntity
    {
        $map = [];
        $pathSplitter = explode('/', $file);
        foreach ($pathSplitter as $location) {
            if (!empty($location)) {
                $map[] = $location;
            }
        }
        unset($map[count($map)-1]);

        $name = $map[count($map)-1];

        $path = '';
        foreach ($map as $location) {
            $path .= '/' . $location;
        }

        return (new DirectoryEntity())
            ->setName($name)
            ->setPath($path)
            ->setMap($map)
        ;
    }

    // TODO: create test
    public function readDirectory(
        string $directory,
        bool $scanHiddenFiles = true,
        bool $readSubDirs = false
    ): array {
        if (!self::exists($directory)) {
            throw new LogicException('Directory not found.');
        }

        $directories = [];
        $files = [];

        $handle = opendir($directory);

        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if (in_array($file, ['.', '..'], true)) {
                    continue;
                }

                if (!$scanHiddenFiles && substr($file, 0, 1) === '.') {
                    continue;
                }

                $path = $directory . '/' . $file;

                if (filetype($path) === 'dir') {
                    if ($readSubDirs) {
                        $directories[$file] = self::readDirectory($path, $scanHiddenFiles, true);
                        continue;
                    }

                    $directories[$file] = $path;
                    continue;
                }

                $files[$file] = $path;
            }
            closedir($handle);
        }

        return array_merge($directories, $files);
    }

    private static function validateFileDirectoryEntityAndCreateIfNotFound(string $file): void
    {
        $directory = self::getDirectory($file);

        if (self::exists($directory->getPath())) {
            return;
        }

        $fullPath = '';
        for ($i = 0; $i < count($directory->getMap()); ++$i) {
            $fullPath .= '/' . $directory->getMap()[$i];

            if (self::exists($fullPath)) {
                continue;
            }

            mkdir($fullPath);
        }
    }
}
