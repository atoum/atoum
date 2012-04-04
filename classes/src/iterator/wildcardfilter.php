<?php

namespace mageekguy\atoum\src\iterator;

use
    mageekguy\atoum
;

/**
 * Wildcard filter
 *
 * Class that allow directory argument to be specified with a wildcard parameter.
 * It does not support custom regex and double wildcard marker.
 */
class wildcardFilter extends \recursiveFilterIterator
{
    /**
     * @var string The base pattern (path) without delimiter
     */
    protected $basePattern;

    /**
     * @var string The pattern with delimiter
     */
    protected $pattern;

    /**
     * @var array A list of children pattern
     */
    protected $subPatterns;

    /**
     * __construct
     *
     * Initialize the current pattern for the iterator
     *
     * @param \RecursiveIterator $iterator    RecursiveFilterIterator implementation
     * @param string             $basePattern Base parent pattern (parent path)
     * @param array              $subPatterns List of all children patterns
     */
    public function __construct(\RecursiveIterator $iterator, $basePattern, array $subPatterns)
    {
        // Append children pattern & remove it from the subPatterns list.
        // If no more children are available/specified, allows everything not starting by a dot.
        $childrenPattern = array_shift($subPatterns);
        $this->basePattern .= ($childrenPattern === null)
                           ? DIRECTORY_SEPARATOR.'[^\.]{0}.*'
                           : DIRECTORY_SEPARATOR.str_replace('*', '(?=[^\.])[^/]*', $childrenPattern);

        // Add delimiter to the basePattern (optimization to avoid on-the-fly concatenation in the accept method)
        $this->pattern = '#^'.$this->basePattern.'$#';

        // Store subpattern to pass them into further nested filter
        $this->subPatterns = $subPatterns;

        parent::__construct($iterator);
    }

    public function accept(\splFileInfo $file = null)
    {
        if ($file === null)
        {
            $file = $this->getInnerIterator()->current();
        }

        return (preg_match($this->pattern, $file->getPathname()) > 0);
    }

    public function getChildren()
    {
        return new self ($this->getInnerIterator()->getChildren(), $this->basePattern, $this->subPatterns);
    }
}