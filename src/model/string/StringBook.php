<?php
namespace mtoolkit\evolution\model\string;

class StringBook
{
    private $haystack;

    private function __construct($haystack)
    {
        $this->haystack = $haystack;
    }

    /**
     * @param string $haystack
     * @return StringBook
     */
    public static function is($haystack)
    {
        return new StringBook($haystack);
    }

    /**
     * @param String $needle
     * @return bool
     */
    public function startsWith($needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($this->haystack, $needle, -strlen($this->haystack)) !== FALSE;
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function endsWith($needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($this->haystack) - strlen($needle)) >= 0 && strpos($this->haystack, $needle, $temp) !== FALSE);
    }
}