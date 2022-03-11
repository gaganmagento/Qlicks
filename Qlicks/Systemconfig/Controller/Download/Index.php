<?php

namespace Qlicks\Systemconfig\Controller\download;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class Index extends Action
{
    public function __construct(
        Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
        $filepath = 'downloadable/tablerates.csv';
        $downloadedFileName = 'CustomerList.csv';
        $content['type'] = 'filename';
        $content['value'] = $filepath;
        $content['rm'] = 1;
        return $this->fileFactory->create($downloadedFileName, $content, DirectoryList::VAR_DIR);
    }
}