<?php

namespace Qlicks\Updateproducts\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;

class Downloadfile extends \Magento\Framework\App\Action\Action
{   

 public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_fileFactory = $fileFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        parent::__construct($context);
    }
 
    public function execute()
    {

        $name = date('m_d_Y_H_i_s');
        $filepath = 'export/custom' . $name . '.csv';
        $this->directory->create('export');
        /* Open file */
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $columns = $this->getColumnHeader();
        foreach ($columns as $column) {
            $header[] = $column;
        }
        /* Write Header */
        $stream->writeCsv($header);
 
        $content = [];
        $content['type'] = 'filename'; // must keep filename
        $content['value'] = $filepath;
        $content['rm'] = '1'; //remove csv from var folder
 
        $csvfilename = 'Sample.csv';
        return $this->_fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);   
    }

    /* Header Columns */
    public function getColumnHeader() {
        $headers = ['sku','upsell_skus','crossell_skus','related_skus'];
        return $headers;
    }
}


