<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Xml;

class XmlParser
{
    public const STATE_SEARCHING_ELEMENT = 0;
    public const STATE_PARSING_ELEMENT = 1;
    protected int $state = self::STATE_SEARCHING_ELEMENT;
    protected int $openedTags = 0;

    protected ?string $currentLineData = null;
    protected ?string $currentXml = null;

    /** @var resource */
    protected $nativeXmlParser;

    /** @var resource */
    protected $stream;
    protected string $nodeNameSelector;
    protected bool $debug = false;

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * @param resource $stream
     */
    public function __construct($stream, string $nodeNameSelector)
    {
        $this->nodeNameSelector = $nodeNameSelector;
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

    protected function createNativeParser()
    {
        $this->resetNativeParser();
        $this->nativeXmlParser = xml_parser_create('UTF-8');
        xml_set_object($this->nativeXmlParser, $this);
        xml_set_element_handler($this->nativeXmlParser, 'startElement', 'endElement');
    }

    public function startElement($parser, $name, $attribs)
    {
        if (strtolower($name) == $this->nodeNameSelector) {
            if (0 === $this->openedTags) {
                $this->currentXml = $this->currentLineData;
                $this->state = self::STATE_PARSING_ELEMENT;
            }
            ++$this->openedTags;
        }
    }

    public function endElement($parser, $name)
    {
        if (strtolower($name) == $this->nodeNameSelector) {
            --$this->openedTags;
            if (0 === $this->openedTags) {
                $this->state = self::STATE_SEARCHING_ELEMENT;
            }
        }
    }

    public function rewind()
    {
        rewind($this->stream);
        $this->state = self::STATE_SEARCHING_ELEMENT;
        $this->currentXml = null;
        $this->openedTags = 0;
        $this->createNativeParser();
    }

    public function parse(): ?string
    {
        while (!feof($this->stream)) {
            $data = fgets($this->stream);
            $this->currentLineData = $data ? $data : '';
            if (self::STATE_PARSING_ELEMENT === $this->state) {
                $this->currentXml .= $this->currentLineData;
            }
            xml_parse($this->nativeXmlParser, $this->currentLineData, feof($this->stream));
            if (self::STATE_SEARCHING_ELEMENT === $this->state && null !== $this->currentXml) {
                $xml = $this->currentXml;
                $this->currentXml = null;

                return $xml;
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
