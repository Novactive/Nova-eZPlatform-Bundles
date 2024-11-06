<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Xml;

class XmlParser
{
    public const STATE_SEARCHING_ELEMENT = 0;
    public const STATE_PARSING_ELEMENT = 1;

    protected int $state = self::STATE_SEARCHING_ELEMENT;
    protected int $depth = 0;
    protected ?string $currentLineXml = null;
    protected ?int $foundElementStartColumn = 1;
    protected string $tmpElement = '';
    protected array $foundElementStack = [];

    /** @var resource */
    protected $nativeXmlParser;

    /** @var resource */
    protected $stream;
    protected string $elementNameSelector;
    protected bool $debug = false;

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * @param resource $stream
     */
    public function __construct($stream, string $searchedElementName)
    {
        $this->searchedElementName = $searchedElementName;
        $this->stream = $stream;
        $this->createNativeParser();
    }

    protected function resetNativeParser(): void
    {
        if ($this->nativeXmlParser) {
            xml_parser_free($this->nativeXmlParser);
            $this->nativeXmlParser = null;
        }
    }

    protected function createNativeParser(): void
    {
        $this->resetNativeParser();
        $this->nativeXmlParser = xml_parser_create('UTF-8');
        xml_set_object($this->nativeXmlParser, $this);
        xml_set_element_handler($this->nativeXmlParser, 'startElement', 'endElement');
    }

    public function startElement($parser, $name, $attribs): void
    {
        if (self::STATE_SEARCHING_ELEMENT !== $this->state) {
            return;
        }
        if (strtolower($name) == $this->searchedElementName) {
            if (0 === $this->depth) {
                $this->foundElementStartColumn = xml_get_current_column_number($parser) - strlen(
                    $this->searchedElementName
                ) - 1;
                $this->state = self::STATE_PARSING_ELEMENT;
            }
            ++$this->depth;
        }
    }

    public function endElement($parser, $name): void
    {
        if (self::STATE_PARSING_ELEMENT !== $this->state) {
            return;
        }
        if (strtolower($name) == $this->searchedElementName) {
            --$this->depth;
            if (0 === $this->depth) {
                $this->state = self::STATE_SEARCHING_ELEMENT;
                $columnNumber = xml_get_current_column_number($parser);
                $this->foundElementStack[] = $this->tmpElement.mb_substr(
                    $this->currentLineXml,
                    $this->foundElementStartColumn - 1,
                    $columnNumber - $this->foundElementStartColumn
                )
                ;
                $this->foundElementStartColumn = $columnNumber;
                $this->tmpElement = '';
            }
        }
    }

    public function rewind(): void
    {
        rewind($this->stream);
        $this->state = self::STATE_SEARCHING_ELEMENT;
        $this->tmpElement = '';
        $this->foundElementStack = [];
        $this->foundElementStartColumn = 1;
        $this->depth = 0;
        $this->createNativeParser();
    }

    public function parse(): ?string
    {
        while (!feof($this->stream) || !empty($this->foundElementStack)) {
            if (!empty($this->foundElementStack)) {
                return array_shift($this->foundElementStack);
            }

            if (!feof($this->stream)) {
                $xml = fgets($this->stream);
                $this->currentLineXml = $xml ? $xml : '';
                xml_parse($this->nativeXmlParser, $this->currentLineXml, feof($this->stream));

                if (self::STATE_PARSING_ELEMENT === $this->state) {
                    $this->tmpElement .= mb_substr($this->currentLineXml, $this->foundElementStartColumn - 1);
                    $this->foundElementStartColumn = 1;
                }
            }
        }

        $this->resetNativeParser();

        return null;
    }

    public function __destruct()
    {
        $this->resetNativeParser();
    }
}
