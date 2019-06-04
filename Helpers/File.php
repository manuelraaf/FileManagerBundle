<?php

namespace Artgris\Bundle\FileManagerBundle\Helpers;

use Artgris\Bundle\FileManagerBundle\Service\FileTypeService;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\TranslatorInterface;

class File
{
    /**
     * @var SplFileInfo
     */
    private $file;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var FileTypeService
     */
    private $fileTypeService;
    /**
     * @var FileManager
     */
    private $fileManager;
    private $preview;

    /**
     * File constructor.
     *
     * @param SplFileInfo         $file
     * @param TranslatorInterface $translator
     * @param FileTypeService     $fileTypeService
     * @param FileManager         $fileManager
     *
     * @internal param $module
     */
    public function __construct(SplFileInfo $file, TranslatorInterface $translator, FileTypeService $fileTypeService, FileManager $fileManager)
    {
        $this->file = $file;
        $this->translator = $translator;
        $this->fileTypeService = $fileTypeService;
        $this->fileManager = $fileManager;
        $this->preview = $this->fileTypeService->preview($this->fileManager, $this->file);
    }

    public function getDimension()
    {
        try {
            return preg_match('/(gif|png|jpe?g|svg)$/i', $this->file->getExtension()) ?
                getimagesize($this->file->getPathname()) : '';
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getHTMLDimension()
    {
        try {
            $dimension = $this->getDimension();
            if ($dimension) {
                return "{$dimension[0]} × {$dimension[1]}";
            }
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getHTMLSize()
    {
        try {
            if ('file' === $this->getFile()->getType()) {
                $size = $this->file->getSize() / 1000;
                $kb = $this->translator->trans('size.kb');
                $mb = $this->translator->trans('size.mb');

                return $size > 1000 ? number_format(($size / 1000), 1, '.', '').' '.$mb : number_format($size, 1, '.', '').' '.$kb;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getAttribut()
    {
        try {
            if ($this->fileManager->getModule()) {
                $attr = '';
                $dimension = $this->getDimension();
                if ($dimension) {
                    $width = $dimension[0];
                    $height = $dimension[1];
                    $attr .= "data-width=\"{$width}\" data-height=\"{$height}\" ";
                }

                if ('file' === $this->file->getType()) {
                    $attr .= "data-path=\"{$this->getPreview()['path']}\"";
                    $attr .= ' class="select"';
                }

                return $attr;
            }
        } catch (\Exception $e) {
            return '';
        }
    }

    public function isImage()
    {
        return array_key_exists('image', $this->preview);
    }

    /**
     * @return SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param SplFileInfo $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return array
     */
    public function getPreview()
    {
        return @$this->preview;
    }

    /**
     * @param array $preview
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
    }
}
