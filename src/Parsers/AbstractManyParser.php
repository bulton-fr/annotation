<?php

namespace BultonFr\Annotation\Parsers;

use Countable;
use Exception;
use Iterator;
use Reflector;
use BultonFr\Annotation\ParserManager;

/**
 * Abstract class for parser which contain a list of parser.
 *
 * @package BultonFr\Annotation
 */
abstract class AbstractManyParser implements Iterator, Countable
{
    /**
     * @const EXCEP_KEY_NOT_EXIST Exception code if user ask a key which
     * not exist.
     */
    const EXCEP_KEY_NOT_EXIST = 301001;

    /**
     * The parser manager system
     *
     * @var \BultonFr\Annotation\ParserManager
     */
    protected $parserManager;

    /**
     * The Reflection object for the readed item
     *
     * @var \Reflector
     */
    protected $reflection;
    
    /**
     * The list of Parser object
     *
     * @var AbstractParser[<string>]
     */
    protected $list = [];

    /**
     * The list of keys used in $list
     *
     * @var string[<int>]
     */
    protected $itemKeys = [];

    /**
     * The current index in the Iterator loop
     *
     * @var integer
     */
    protected $index = 0;

    /**
     * Constructor
     *
     * @param ParserManager $parserManager
     * @param Reflector $reflection
     */
    public function __construct(
        ParserManager $parserManager,
        Reflector $reflection
    ) {
        $this->parserManager = $parserManager;
        $this->reflection    = $reflection;
    }

    /**
     * Instanciate a new parser for each item find
     *
     * @return void
     */
    abstract public function run();

    /**
     * Get the parser manager system
     *
     * @return \BultonFr\Annotation\ParserManager
     */
    public function getParserManager(): ParserManager
    {
        return $this->parserManager;
    }

    /**
     * Get the Reflection object for the readed item
     *
     * @return \Reflector
     */
    public function getReflection(): Reflector
    {
        return $this->reflection;
    }

    /**
     * Get the list of Parser object
     *
     * @return AbstractParser[<string>]
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * Get the list of keys used in $list
     *
     * @return string[<int>]
     */
    public function getItemKeys(): array
    {
        return $this->itemKeys;
    }

    /**
     * Get the current index in the Iterator loop
     *
     * @return integer
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * {@inheritDoc}
     */
    public function current(): AbstractParser
    {
        $key = $this->itemKeys[$this->index];

        return $this->list[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function key(): string
    {
        return $this->itemKeys[$this->index];
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        return array_key_exists($this->index, $this->itemKeys);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->list);
    }

    /**
     * Add a new parser for an item to the list
     *
     * @param string $itemKey The item name (method or property name)
     * @param AbstractParser $item The parser for the item
     *
     * @return void
     */
    public function addItem(string $itemKey, AbstractParser $item)
    {
        $this->list[$itemKey] = $item;
        $this->itemKeys[]     = $itemKey;
    }

    /**
     * Check if a key exist
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->list);
    }

    /**
     * Obtain key value
     *
     * @param string $key
     *
     * @return AbstractParser
     */
    public function obtainForKey(string $key): AbstractParser
    {
        if ($this->hasKey($key) === false) {
            throw new Exception(
                'The key '.$key.' not exist in the list',
                static::EXCEP_KEY_NOT_EXIST
            );
        }

        return $this->list[$key];
    }
}
