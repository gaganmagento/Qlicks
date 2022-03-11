<?php

namespace Qlicks\Updateproducts\Controller\Adminhtml\Index;

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
        $headers = ['sku','store_view_code','attribute_set_code','categories','name','description','short_description','price','special_price','special_from_date','special_to_date','url_key','meta_title','meta_keyword','meta_description','news_from_date','news_to_date','anhaenger_form','anhaenger_inhalt','anzahl_diamanten','buchstabe','carat','cost','delivery_time','product_group_id','discontinued_products','durchmesser','ean','engraving_product','farbe','geschlecht','groesse','halskette_laenge','kategorie','kettenart','kettenform','ketteninhalt','legierungsgewicht','material','materialstaerke','ohrschmuck_form','perlenart','perlenfarbe','perlenform','perlen_durchmesser','produzent','ringbreiten','ringgrosse','schliffform','schliffguete','schmuck_art','steine','steinfarbe','steinreinheit','sternzeichen','veredelung','verschluss','tax_class_name','realated_sku','crosssell_sku','upsell_sku','relation_skus','discontinue_sku','image','additional_images'];
        return $headers;
    }

     
}


