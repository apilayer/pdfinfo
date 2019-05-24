<?php

namespace apilayer\PDFInfo;

/*
* Inspired by http://stackoverflow.com/questions/14644353/get-the-number-of-pages-in-a-pdf-document/14644354
* @author howtomakeaturn
*/

class PDFInfo
{
    protected $file;
    public $attributes, $output;
    public static $bin;

    public function __construct($file)
    {
        $this->file = $file;

        $this->loadOutput();

        $this->parseOutput();
    }

    public function __get($field)
    {
        return property_exists($this->attributes, $field) ?
            $this->attributes->{$field} :
            null;
    }

    public function getBinary()
    {
        if (empty(static::$bin)) {
            static::$bin = trim(trim(getenv('PDFINFO_BIN'), '\\/" \'')) ?: 'pdfinfo';
        }

        return static::$bin;
    }

    private function loadOutput()
    {
        $cmd = escapeshellarg($this->getBinary()); // escapeshellarg to work with Windows paths with spaces.

        $file = escapeshellarg($this->file);

        $page_limit = intval(getenv('PDFINFO_PAGE_LIMIT')) ?: 999;

        // Parse entire output
        // Surround with double quotes if file name has spaces
        exec("$cmd -l $page_limit $file", $output, $returnVar);

        if ($returnVar === 1) {
            throw new Exceptions\OpenPDFException();
        } else {
            if ($returnVar === 2) {
                throw new Exceptions\OpenOutputException();
            } else {
                if ($returnVar === 3) {
                    throw new Exceptions\PDFPermissionException();
                } else {
                    if ($returnVar === 99) {
                        throw new Exceptions\OtherException();
                    }
                }
            }
        }

        $this->output = $output;
    }

    private function parseOutput()
    {
        foreach ($this->output as $output_line) {
            list($key, $value) = explode(':', $output_line, 2);

            if (preg_match('/\b(?<number>\d+)\b/', $key, $key_matches)) {
                $key = str_replace($key_matches['number'], '', $key);
            }

            $key = $this->formatKey($key);
            $value = $this->formatValue($value);

            // Only set attributes once
            if (!property_exists($this->attributes, $key)) {
                $this->attributes->{$key} = $value;
            }

            // Attributes for multiple pages
            if (isset($key_matches['number'])) {
                $this->attributes->{"${key}s"}->{$key_matches['number']} = $value;
            }
        }

        // Compatibility with version 4.0 which has page rotation data inside of page size
        $rot_pattern = '/\(rot\w+\s(?<degrees>\d+)\s\w+\)$$/';
        $rot_replace = '/\s+\([^\)]+\)$/';

        if (is_null($this->pageRot) && $this->pageSize && preg_match($rot_pattern, $this->pageSize, $rot_matches)) {
            $this->attributes->{'pageRot'} = $rot_matches['degrees'];
            $this->attributes->{'pageSize'} = preg_replace($rot_replace, '', $this->pageSize);

            // Also process attributes for all pages
            if (property_exists($this->attributes, 'pageSizes')) {
                foreach ($this->pageSizes as $page_number => $page_size) {
                    preg_match($rot_pattern, $page_size, $page_matches);
                    if ($page_matches) {
                        $this->attributes->{'pageRots'}->{$page_number} = $page_matches['degrees'];
                        $this->attributes->{'pageSizes'}->{$page_number} = preg_replace($rot_replace, '', $this->pageSizes->{$page_number});
                    }
                }
            }
        }
    }

    private function formatKey($string)
    {
        return preg_replace_callback('/^([A-Z])(?![A-Z])/', function ($m) {
            return strtolower($m[1]);
        }, preg_replace('/\s+/', '', ucwords($string)));
    }

    private function formatValue($string)
    {
        return trim(preg_replace('/\s+/', ' ', $string));
    }
}
