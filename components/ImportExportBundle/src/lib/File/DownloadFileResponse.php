<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\File;

use DateTime;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DownloadFileResponse extends Response
{
    protected string $filepath;
    protected FileHandler $fileHandler;
    protected int $offset;
    protected int $maxlen;

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler $fileHandler
     */
    public function __construct(
        string $filepath,
        FileHandler $fileHandler,
        $status = 200,
        $headers = [],
        $public = true,
        $contentDisposition = null,
        $autoLastModified = true
    ) {
        $this->fileHandler = $fileHandler;

        parent::__construct(null, $status, $headers);
        $this->setFilepath($filepath, $contentDisposition, $autoLastModified);

        if ($public) {
            $this->setPublic();
        }
    }

    public function setFilepath(
        string $filepath,
        ?string $contentDisposition = null,
        bool $autoLastModified = true
    ): DownloadFileResponse {
        $this->filepath = $filepath;

        if ($autoLastModified) {
            $this->setAutoLastModified();
        }

        if ($contentDisposition) {
            $this->setContentDisposition($contentDisposition);
        }

        return $this;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function setAutoLastModified(): DownloadFileResponse
    {
        $date = new DateTime();
        $date->setTimestamp($this->fileHandler->lastModified($this->filepath)->lastModified());
        $this->setLastModified($date);

        return $this;
    }

    public function setContentDisposition($disposition, $filename = '', $filenameFallback = ''): DownloadFileResponse
    {
        if (empty($filename)) {
            $filename = pathinfo($this->filepath, PATHINFO_FILENAME);
        }

        if (empty($filenameFallback)) {
            $filenameFallback = mb_convert_encoding($filename, 'ASCII');
        }
        $dispositionHeader = $this->headers->makeDisposition($disposition, $filename, $filenameFallback);
        $this->headers->set('Content-Disposition', $dispositionHeader);

        return $this;
    }

    public function prepare(Request $request): DownloadFileResponse
    {
        $fileSize = $this->fileHandler->fileSize($this->filepath)->fileSize();
        $this->headers->set('Content-Length', $fileSize);
        $this->headers->set('Accept-Ranges', 'bytes');
        $this->headers->set('Content-Transfer-Encoding', 'binary');

        if (!$this->headers->has('Content-Type')) {
            $mimeType = $this->fileHandler->mimeType($this->filepath)->mimeType();
            $this->headers->set(
                'Content-Type',
                $mimeType ?: 'application/octet-stream'
            );
        }

        if ('HTTP/1.0' != $request->server->get('SERVER_PROTOCOL')) {
            $this->setProtocolVersion('1.1');
        }

        $this->ensureIEOverSSLCompatibility($request);

        $this->offset = 0;
        $this->maxlen = -1;

        if ($request->headers->has('Range')) {
            // Process the range headers.
            if (!$request->headers->has('If-Range') || $this->getEtag() == $request->headers->get('If-Range')) {
                $range = $request->headers->get('Range');

                list($start, $end) = explode('-', substr($range, 6), 2) + [0];

                $end = ('' === $end) ? $fileSize - 1 : (int) $end;

                if ('' === $start) {
                    $start = $fileSize - $end;
                    $end = $fileSize - 1;
                } else {
                    $start = (int) $start;
                }

                if ($start <= $end) {
                    if ($start < 0 || $end > $fileSize - 1) {
                        $this->setStatusCode(416); // HTTP_REQUESTED_RANGE_NOT_SATISFIABLE
                    } elseif (0 !== $start || $end !== $fileSize - 1) {
                        $this->maxlen = $end < $fileSize ? $end - $start + 1 : -1;
                        $this->offset = $start;

                        $this->setStatusCode(206); // HTTP_PARTIAL_CONTENT
                        $this->headers->set('Content-Range', sprintf('bytes %s-%s/%s', $start, $end, $fileSize));
                        $this->headers->set('Content-Length', $end - $start + 1);
                    }
                }
            }
        }

        return $this;
    }

    public function sendContent()
    {
        if (!$this->isSuccessful()) {
            parent::sendContent();

            return;
        }

        if (0 === $this->maxlen) {
            return;
        }

        $destinationStream = fopen('php://output', 'wb');
        $sourceStream = $this->fileHandler->readStream($this->filepath);
        stream_copy_to_stream($sourceStream, $destinationStream, $this->maxlen, $this->offset);

        fclose($destinationStream);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException when the content is not null
     */
    public function setContent($content)
    {
        if (null !== $content) {
            throw new LogicException('The content cannot be set on a BinaryStreamResponse instance.');
        }
    }

    public function getContent()
    {
        return null;
    }
}
