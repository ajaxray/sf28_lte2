<?php
/*
 * This file is part of the IBBL project.
 *
 * (c) Anis Uddin Ahmad <anisniit@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Helper;

/**
 * Dir
 *
 * Directory related utility functions and constants
 *
 * @author Anis Uddin Ahmad <anis.programmer@gmail.com>
 */
class Dir 
{

    /**
     * Create writable directory (irrespective of current umask) with recursive path
     * Change filemode to writable if directory already exist
     *
     * @param $path
     *
     * @return bool
     */
    public static function makeWritable($path)
    {
        $oldmask = umask(0);
        $created = (! is_dir($path))? mkdir($path, 0777, true) : chmod($path, 0777);
        umask($oldmask);

        return $created;
    }

    /**
     * @param $dir
     *
     * Remove a directory recursively
     *
     * @return bool
     */
    public static function remove($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file") && !is_link($dir)) ? self::remove("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public static function pathToUrl($path)
    {
        global $kernel;
        $webRoot = $kernel->getRootDir() . '/../web';

        return substr($path, strlen($webRoot));
    }
} 