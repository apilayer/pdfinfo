Simple PHP wrapper to the pdfinfo unix tool.

Inspired by http://stackoverflow.com/questions/14644353/get-the-number-of-pages-in-a-pdf-document/14644354

# What is pdfinfo

pdfinfo is an unix tool helping extract information from pdf files.

http://linuxcommand.org/man_pages/pdfinfo1.html

You can get metadata, page count and rotation for every page, etc.

# Installation

## 1. Install pdfinfo

First you need to have pdfinfo in your system.

For ubuntu, there's an easy way for doing this:
```
sudo apt-get install poppler-utils
```

## 2. Install the library
You can just download the file to your project, or install it via composer:
```
composer require apilayer/pdfinfo
```

# Usage
Just pass the path to the pdf file to the constructor, and you can get metadata from its properties immediately:

```php
$pdf = new PDFInfo('path/to/the/pdf');
echo $pdf->title; // Get the title
echo $pdf->pages; // Get the number of pages
```

# Exceptions
This library throws 4 kind of exceptions to represent the official exit codes.
* OpenPDFException    
* OpenOutputException
* PDFPermissionException
* OtherException

Check the [official documentation](https://www.xpdfreader.com/pdfinfo-man.html) for more information.



# Reference

Currently this library supports the following metadata:

* title
* author
* creator
* producer
* creationDate
* modDate
* tagged
* form
* pages
* encrypted
* pageSize
* pageSizes
* fileSize
* optimized
* PDFVersion
* pageRot
* pageRots

Environment variables configuration:

* `PDFINFO_BIN` - location of PDFInfo executable
* `PDFINFO_PAGE_LIMIT` - last page to process, defaults to 999
