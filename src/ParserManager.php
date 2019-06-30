<?php

namespace BultonFr\Annotation;

use Exception;
use ReflectionClass;
use BultonFr\Annotation\Parsers\AbstractParser;

/**
 * Manage all differents parser used to class a full class.
 *
 * @package BultonFr\Annotation
 */
class ParserManager
{
    /**
     * @const EXCEP_SAVE_COMMENTS Exception code if the opcode cache system not
     * keep comments (so annotation not exists in opcache)
     *
     * @see README.md for code format
     */
    const EXCEP_SAVE_COMMENTS = 101001;

    /**
     * @const EXCEP_NS_ALREADY_EXIST Exception code if a new imported ns
     * already exist.
     *
     * @see README.md for code format
     */
    const EXCEP_NS_ALREADY_EXIST = 101002;

    /**
     * Reader class instance which have instanciate this class
     *
     * @var \BultonFr\Annotation\Reader
     */
    protected $reader;

    /**
     * Reflection class instance for the class to parse
     *
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * List of things parsed into the class
     *
     * @var AbstractParser[<string>]
     */
    protected $parserList = [];

    /**
     * List of namespaces imported with AddNS annotation
     *
     * @var string[<string>]
     */
    protected $importedNS = [];

    /**
     * Construct
     *
     * @param Reader $reader The main reader system
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;

        $this->checkLib();
    }

    /**
     * Get reader class instance which have instanciate this class
     *
     * @return \BultonFr\Annotation\Reader
     */
    public function getReader(): Reader
    {
        return $this->reader;
    }

    /**
     * Get reflection class instance for the class to parse
     *
     * @return \ReflectionClass
     */
    public function getReflection(): ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * Get list of things parsed into the class
     *
     * @return AbstractParser[<string>]
     */
    public function getParserList(): array
    {
        return $this->parserList;
    }

    /**
     * Get the value of importedNS
     *
     * @return string[<string>]
     */
    public function getImportedNS(): array
    {
        return $this->importedNS;
    }

    /**
     * Check if Opcache is configured to save comments. If not, we cannot use
     * the annotation system.
     *
     * @return void
     *
     * @throws Exception If opcache is not configured to save comments
     */
    protected function checkLib()
    {
        if (
            extension_loaded('Zend OPcache') &&
            (int) ini_get('opcache.save_comments') === 0
        ) {
            throw new Exception(
                'Zend OPcache should have save_comments enabled',
                self::EXCEP_SAVE_COMMENTS
            );
        }
    }

    /**
     * Obtain the reflection object for the asked class
     * And call methods to create the parser list and execute all parser.
     *
     * @return void
     */
    public function run()
    {
        $this->createReflectionObject();
        $this->createParserList();
        $this->execAllParser();
    }

    /**
     * Create the ReflectionClass object
     *
     * @return void
     */
    protected function createReflectionObject()
    {
        $this->reflection = new ReflectionClass($this->reader->getClassName());
    }

    /**
     * Generate the parser list which contain the class which parse a part
     * of the class.
     *
     * @return void
     */
    protected function createParserList()
    {
        $this->parserList = [
            'class'      => new Parsers\ClassParser($this, $this->reflection),
            'methods'    => new Parsers\MethodsParser($this, $this->reflection),
            'properties' => new Parsers\PropertiesParser($this, $this->reflection)
        ];
    }

    /**
     * Loop on all parser and execute it
     *
     * @return void
     */
    protected function execAllParser()
    {
        foreach ($this->parserList as $parser) {
            $parser->run();
        }
    }

    /**
     * Add a new imported namespace (obtain by AddNS or manually)
     *
     * @param string $name The class path (with ns) of the class which will be
     *  instancied when the annotation defined by $alias will be found.
     * @param string $alias The annotation key
     *
     * @return void
     */
    public function addImportedNS(string $name, string $alias)
    {
        if (
            array_key_exists($alias, $this->importedNS) &&
            $this->importedNS[$alias] !== $name
        ) {
            throw new Exception(
                'A ns '.$alias.' is already imported with a different value.',
                static::EXCEP_NS_ALREADY_EXIST
            );
        }

        $this->importedNS[$alias] = $name;
    }
}
