<?php
require_once('../vendor/autoload.php');

use apilayer\PDFInfo\PDFInfo;

$pdf = new PDFInfo('files/Sample.pdf');

echo 'Title: ', $pdf->title, '<hr />', PHP_EOL;
echo 'Author: ', $pdf->author, '<hr />', PHP_EOL;
echo 'Creator: ', $pdf->creator, '<hr />', PHP_EOL;
echo 'Producer: ', $pdf->producer, '<hr />', PHP_EOL;
echo 'Creation date: ', $pdf->creationDate, '<hr />', PHP_EOL;
echo 'Last modified date: ', $pdf->modDate, '<hr />', PHP_EOL;
echo 'Tagged: ', $pdf->tagged, '<hr />', PHP_EOL;
echo 'Form: ', $pdf->form, '<hr />', PHP_EOL;
echo 'Pages: ', $pdf->pages, '<hr />', PHP_EOL;
echo 'Encrypted: ', $pdf->encrypted, '<hr />', PHP_EOL;
echo 'Page size: ', $pdf->pageSize, '<hr />', PHP_EOL;
echo 'Page rotation: ', $pdf->pageRot, '<hr />', PHP_EOL;
echo 'File size: ', $pdf->fileSize, '<hr />', PHP_EOL;
echo 'Optimized: ', $pdf->optimized, '<hr />', PHP_EOL;
echo 'PDF Version: ', $pdf->PDFVersion, '<hr />', PHP_EOL;
