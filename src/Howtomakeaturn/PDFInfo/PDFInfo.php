<?php
namespace Howtomakeaturn\PDFInfo;

/*
* Inspired by http://stackoverflow.com/questions/14644353/get-the-number-of-pages-in-a-pdf-document/14644354
* @author howtomakeaturn
*/

class PDFInfo
{
    protected $file;
    public $output;
    
    public $title;
    public $author;
    public $creator;
    public $producer;
    public $creationDate;
    public $modDate;
    public $tagged;
    public $form;
    public $pages;
    public $encrypted;
    public $pageSize;
    public $fileSize;
    public $optimized;
    public $PDFVersion;

    public static $bin;

    public function __construct($file)
    {
        $this->file = $file;
        
        $this->loadOutput();
        
        $this->parseOutput();        
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
        // Parse entire output
        // Surround with double quotes if file name has spaces
        exec("$cmd -l 500 $file", $output, $returnVar);
        
        if ( $returnVar === 1 ){
            throw new Exceptions\OpenPDFException();
        } else if ( $returnVar === 2 ){
            throw new Exceptions\OpenOutputException();
        } else if ( $returnVar === 3 ){
            throw new Exceptions\PDFPermissionException();            
        } else if ( $returnVar === 99 ){
            throw new Exceptions\OtherException();                        
        }
        
        $this->output = $output;
    }

    private function parseOutput()
    {
        $this->title = $this->parse('Title');
        $this->author = $this->parse('Author');
        $this->creator = $this->parse('Creator');
        $this->producer = $this->parse('Producer');
        $this->creationDate = $this->parse('CreationDate');
        $this->modDate = $this->parse('ModDate');
        $this->tagged = $this->parse('Tagged');
        $this->form = $this->parse('Form');
        $this->pages = $this->parse('Pages');
        $this->encrypted = $this->parse('Encrypted');
        $this->pageSize = $this->parse('Page    1 size');
		
		$x=1;
		foreach(range(1,$this->pages) as $index) {
		if ($x>99){
        $this->newpageSize->$x = $this->parse('Page  '.$x.' size');
		} else if ($x>9){
        $this->newpageSize->$x = $this->parse('Page   '.$x.' size');
		} else {
        $this->newpageSize->$x = $this->parse('Page    '.$x.' size');
		}
		$x++;
		}
		
		$x=1;
		foreach(range(1,$this->pages) as $index) {
		if ($x>99){
        $this->newrotation->$x = $this->parse('Page  '.$x.' rot');
		} else if ($x>9){
        $this->newrotation->$x = $this->parse('Page   '.$x.' rot');
		} else {
        $this->newrotation->$x = $this->parse('Page    '.$x.' rot');
		}
		$x++;
		}
		
        $this->rotation = $this->parse('Page    1 rot');
        $this->fileSize = $this->parse('File size');
        $this->optimized = $this->parse('Optimized');
        $this->PDFVersion = $this->parse('PDF version');
    }
        
    private function parse($attribute)
    {
        // Iterate through lines
        $result = null;
        foreach($this->output as $op)
        {
            // Extract the number
            if(preg_match("/" . $attribute . ":\s*(.+)/i", $op, $matches) === 1)
            {
                $result = $matches[1];
                break;
            }
        }

        return $result;    
    }
    
}
