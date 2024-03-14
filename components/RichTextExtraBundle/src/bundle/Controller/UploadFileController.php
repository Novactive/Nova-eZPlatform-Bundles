<?php

namespace AlmaviaCX\Bundle\IbexaRichTextExtraBundle\Controller;

use AlmaviaCX\Bundle\IbexaRichTextExtra\FieldType\BinaryFile\Mapper;
use AlmaviaCX\Bundle\IbexaRichTextExtra\Form\Data\FileUploadData;
use Exception;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Core\FieldType\BinaryFile\Value as BinaryFileValue;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UploadFileController extends Controller
{
    public const CSRF_TOKEN_HEADER = 'X-CSRF-Token';

    public const LANGUAGE_CODE_KEY = 'languageCode';
    public const FILE_KEY = 'file';

    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface */
    private $validator;

    /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var \AlmaviaCX\Bundle\IbexaRichTextExtra\FieldType\BinaryFile\Mapper */
    private $imageAssetMapper;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        ValidatorInterface $validator,
        CsrfTokenManagerInterface $csrfTokenManager,
        Mapper $imageAssetMapper,
        TranslatorInterface $translator
    ) {
        $this->validator = $validator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->imageAssetMapper = $imageAssetMapper;
        $this->translator = $translator;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function uploadBinaryFileAction(Request $request): Response
    {
        if ($this->isValidCsrfToken($request)) {
            $data = new FileUploadData(
                $request->files->get(self::FILE_KEY),
                $request->request->get(self::LANGUAGE_CODE_KEY)
            );

            $errors = $this->validator->validate($data);
            if (0 === $errors->count()) {
                try {
                    $file = $data->getFile();

                    $content = $this->imageAssetMapper->createAsset(
                        $file->getClientOriginalName(),
                        new BinaryFileValue([
                                                 'path' => $file->getRealPath(),
                                                 'fileSize' => $file->getSize(),
                                                 'fileName' => $file->getClientOriginalName(),
                                             ]),
                        $data->getLanguageCode()
                    );

                    return new JsonResponse([
                                                 'destinationContent' => [
                                                     'id' => $content->contentInfo->id,
                                                     'name' => $content->getName(),
                                                     'locationId' => $content->contentInfo->mainLocationId,
                                                 ],
                                                 'value' => $this->imageAssetMapper->getAssetValue($content),
                                             ]);
                } catch (Exception $e) {
                    return $this->createGenericErrorResponse($e->getMessage());
                }
            } else {
                return $this->createInvalidInputResponse($errors);
            }
        }

        return $this->createInvalidCsrfResponse();
    }

    private function createInvalidCsrfResponse(): JsonResponse
    {
        $errorMessage = $this->translator->trans(
        /* @Desc("Missing or invalid CSRF token") */
            'asset.upload.invalid_csrf',
            [],
            'assets'
        );

        return $this->createGenericErrorResponse($errorMessage);
    }

    private function createInvalidInputResponse(ConstraintViolationListInterface $errors): JsonResponse
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return $this->createGenericErrorResponse(implode(', ', $errorMessages));
    }

    private function createGenericErrorResponse(string $errorMessage): JsonResponse
    {
        return new JsonResponse([
                                     'status' => 'failed',
                                     'error' => $errorMessage,
                                 ]);
    }

    private function isValidCsrfToken(Request $request): bool
    {
        $csrfTokenValue = $request->headers->get(self::CSRF_TOKEN_HEADER);

        return $this->csrfTokenManager->isTokenValid(
            new CsrfToken('authenticate', $csrfTokenValue)
        );
    }
}
