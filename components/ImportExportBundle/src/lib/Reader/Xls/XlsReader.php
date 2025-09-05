<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Xls;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\ArrayAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\CallbackIteratorItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\ItemIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReaderIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\AbstractFileReader;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Translation\TranslatableMessage;

class XlsReader extends AbstractFileReader implements TranslationContainerInterface
{
    protected Spreadsheet $spreadsheet;

    /**
     * @throws \League\Flysystem\FilesystemException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function __invoke(): AbstractReaderIterator
    {
        /** @var XlsReaderOptions $options */
        $options = $this->getOptions();

        $tmpFileName = $this->getFileTmpCopy();
        $reader = IOFactory::createReaderForFile($tmpFileName);
        $reader->setReadDataOnly(true);
        if ($options->tabName) {
            $reader->setLoadSheetsOnly([$options->tabName]);
        }

        $this->spreadsheet = $reader->load($tmpFileName);
        $worksheet = $this->spreadsheet->getActiveSheet();

        return $this->getIterator(
            $worksheet,
            $options->headerRowNumber ? $options->headerRowNumber + 1 : 1,
            $options->colsRange
        );
    }

    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    /**
     * @param array{'start': string, 'end': string}|null $colsRange
     */
    public function getIterator(Worksheet $worksheet, int $startRow = 1, ?array $colsRange = null): ItemIterator
    {
        $maxDataRow = 0;
        $endRow = $startRow - 1;
        $rowIterator = $worksheet->getRowIterator($startRow);
        foreach ($rowIterator as $row) {
            if (
                $row->isEmpty(
                    CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL | CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL
                )
            ) {
                break;
            }
            ++$maxDataRow;
            ++$endRow;
        }

        return new ItemIterator(
            $maxDataRow,
            $worksheet->getRowIterator($startRow, $endRow),
            new CallbackIteratorItemTransformer(function (Row $row) use ($colsRange) {
                $cellsIterator = $row->getCellIterator(
                    $colsRange ? $colsRange['start'] : 'A',
                    $colsRange ? $colsRange['end'] : null
                );

                return new ArrayAccessor(
                    array_map(
                        function (Cell $cell) {
                            return $cell->getValue();
                        },
                        iterator_to_array($cellsIterator)
                    )
                );
            })
        );
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('reader.xls.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [(new Message('reader.xls.name', 'import_export'))->setDesc('Excel reader')];
    }

    public static function getOptionsType(): ?string
    {
        return XlsReaderOptions::class;
    }

    public static function getOptionsFormType(): ?string
    {
        return XlsReaderOptionsFormType::class;
    }
}
