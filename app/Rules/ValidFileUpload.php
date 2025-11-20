<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class ValidFileUpload implements Rule
{
    protected array $allowedMimes;
    protected int $maxSize; // em KB

    public function __construct(array $allowedMimes = ['pdf', 'jpg', 'jpeg', 'png'], int $maxSize = 10240)
    {
        $this->allowedMimes = $allowedMimes;
        $this->maxSize = $maxSize;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (!$value instanceof UploadedFile) {
            return false;
        }

        // Verifica tipo MIME
        $mimeType = $value->getMimeType();
        $extension = strtolower($value->getClientOriginalExtension());

        // Lista de MIME types permitidos
        $allowedMimeTypes = [
            'pdf' => ['application/pdf'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
        ];

        // Verifica extensão
        if (!in_array($extension, $this->allowedMimes)) {
            return false;
        }

        // Verifica MIME type
        if (isset($allowedMimeTypes[$extension])) {
            if (!in_array($mimeType, $allowedMimeTypes[$extension])) {
                return false;
            }
        }

        // Verifica tamanho (em KB)
        $sizeInKB = $value->getSize() / 1024;
        if ($sizeInKB > $this->maxSize) {
            return false;
        }

        // Verifica se o arquivo não está corrompido
        try {
            if (str_starts_with($mimeType, 'image/')) {
                // Tenta ler a imagem
                $imageInfo = @getimagesize($value->getRealPath());
                if ($imageInfo === false) {
                    return false;
                }
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        $allowed = implode(', ', $this->allowedMimes);
        $maxSizeMB = round($this->maxSize / 1024, 2);
        return "O arquivo deve ser um dos seguintes tipos: {$allowed} e não pode exceder {$maxSizeMB}MB.";
    }
}

