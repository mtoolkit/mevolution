<?php
namespace mtoolkit\evolution\model\evolution;

use mtoolkit\evolution\exception\FileNotFoundException;
use mtoolkit\evolution\exception\NoDownPlaceholderException;
use mtoolkit\evolution\exception\NoUpPlaceholderException;
use mtoolkit\evolution\model\string\StringBook;

class EvolutionFile
{
    const UP = '-- UP';
    const DOWN = '-- DOWN';

    /**
     * Returns an array of string. They are the absolute pathes of the evolution files.<br>
     * The files must have the .sql extension and the name must be a numeric.<br>
     * Example:
     * <ul>
     *      <li>1.sql</li>
     *      <li>2.sql</li>
     * </ul>
     *
     * @param string $path Relative path to the folder containing the evolutions.
     * @return \string[] List of absolute path of the evolution files.
     * @throws FileNotFoundException
     */
    public static function getList($path)
    {
        $absolutePath = $path;
        $workingFolder = getcwd();

        if (file_exists($absolutePath) == false)
        {
            $absolutePath = realpath(sprintf('%s/%s', $workingFolder, $path));
        }

        if (file_exists($absolutePath) === false)
        {
            throw new FileNotFoundException($absolutePath);
        }

        $dh = opendir($absolutePath);
        $files = array();
        while (false !== ($filename = readdir($dh)))
        {
            if (in_array($filename, array('.', '..')))
            {
                continue;
            }

            if (StringBook::is($filename)->endsWith('.sql') === false)
            {
                continue;
            }

            $basename = basename($filename, '.sql');

            if (is_numeric($basename) === false)
            {
                continue;
            }

            $files[] = realpath(sprintf('%s%s%s', $absolutePath, DIRECTORY_SEPARATOR, $filename));
        }

        return $files;
    }

    /**
     * @param string $path
     * @return Evolution
     * @throws NoDownPlaceholderException
     * @throws NoUpPlaceholderException
     */
    public static function getEvolution($path)
    {
        $evolution = new Evolution();
        $basename = basename($path, '.sql');

        $fileContent = file_get_contents($path);

        $upPosition = strpos($fileContent, EvolutionFile::UP);
        if ($upPosition === false)
        {
            throw new NoUpPlaceholderException($basename);
        }

        $upPosition = ($upPosition == -1 ? 0 : $upPosition);

        $downPosition = strpos($fileContent, EvolutionFile::DOWN);
        if ($downPosition === false)
        {
            throw new NoDownPlaceholderException($basename);
        }

        $downPosition = ($downPosition == -1 ? strlen($fileContent) : $downPosition);

        $up = substr($fileContent, $upPosition, $downPosition);
        $down = substr($fileContent, $downPosition);

        $evolution->setId((int)$basename)
            ->setUp($up)
            ->setDown($down);

        return $evolution;
    }
}