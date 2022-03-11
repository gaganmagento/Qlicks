<?php

namespace Qlicks\Updateproducts\Controller\Adminhtml\System\Config;

use Magento\SalesRule\Model\Rule;
use Magento\Customer\Model\GroupManagement;
use Magento\SalesRule\Model\Rule\Condition\Address;
use Magento\SalesRule\Model\Rule\Condition\Combine;

class Save extends \Magento\Config\Controller\Adminhtml\System\Config\Save
{
    protected $request;
    protected $_fileUploaderFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $_uploaderFactory,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\RuleRepository $ruleRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupColl
    ) {
        $this->customerGroupColl = $customerGroupColl;
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
        $this->_uploaderFactory = $_uploaderFactory;
        $this->messageManager = $messageManager;
        $this->_varDirectory = $filesystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR
        );
        parent::__construct(
            $context,
            $configStructure,
            $sectionChecker,
            $configFactory,
            $cache,
            $string
        );
    }
    public function execute()
    {
        try {
            if (isset($_FILES["groups"]["name"])) {
                if (isset($_FILES["groups"]["name"]["uploadconfig"])) {
                    if (
                        $_FILES["groups"]["name"]["uploadconfig"]["fields"][
                            "Qlicks_file_up"
                        ]["value"]
                    ) {
                        $toupload = [
                            "name" =>
                                $_FILES["groups"]["name"]["uploadconfig"][
                                    "fields"
                                ]["Qlicks_file_up"]["value"],
                            "type" =>
                                $_FILES["groups"]["type"]["uploadconfig"][
                                    "fields"
                                ]["Qlicks_file_up"]["value"],
                            "size" =>
                                $_FILES["groups"]["size"]["uploadconfig"][
                                    "fields"
                                ]["Qlicks_file_up"]["value"],
                            "tmp_name" =>
                                $_FILES["groups"]["tmp_name"]["uploadconfig"][
                                    "fields"
                                ]["Qlicks_file_up"]["value"],
                            "error" =>
                                $_FILES["groups"]["error"]["uploadconfig"][
                                    "fields"
                                ]["Qlicks_file_up"]["value"],
                        ];
                        $ext = strtolower(
                            pathinfo($toupload["name"], PATHINFO_EXTENSION)
                        );

                        if ($ext == "csv") {
                            $uploader = $this->_uploaderFactory->create([
                                "fileId" => $toupload,
                            ]);
                            $workingDir = $this->_varDirectory->getAbsolutePath(
                                "importexport/"
                            );
                            $filename =
                                date("Y-m-d h:i:s") . "-" . $toupload["name"];
                            $result = $uploader->save($workingDir, $filename);
                            $pathSaved = $result["path"] . $result["file"];
                            chmod($pathSaved, 0777);
                            $updatedskus = $this->getAssignedProduct(
                                $pathSaved
                            );

                            if ($updatedskus) {
                                $message = __(
                                    "Sku " .
                                        $updatedskus .
                                        " updated succesfully"
                                );
                            } else {
                                $message = __("No sku updated");
                            }
                            $this->messageManager->addSuccessMessage($message);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            //  $this->messageManager->addSuccess('Some thing wrong with configuration');
        }
        return parent::execute();
    }
    private function getAssignedProduct($path)
    {
        $filename = $path;
        $updatedskus = "";
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext != "csv") {
            return;
        }
        $delimiter = ";";
        $csvdata = [];
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }
        $header = null;
        $data = [];
        if (($handle = fopen($filename, "r")) !== false) {
            $k = 0;
            while (($row = fgetcsv($handle)) !== false) {
                $k++;
                if ($k == 1) {
                    $header[] = $row;
                    continue;
                }
                $csvdata[] = $row;
            }
            fclose($handle);
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get(
            "Magento\Framework\App\ResourceConnection"
        );
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName("catalog_product_link");
        $tableName1 = $resource->getTableName("catalog_product_link_type");
        $productObject = $objectManager->get("Magento\Catalog\Model\Product");
        if ($header) {
            $skuIndex[array_search("sku", $header[0])] = "sku";
            $skuInd = array_search("sku", $header[0]); // index sku
            $skuIndex[array_search("upsell_skus", $header[0])] = "upsell_skus";
            $skuIndex[array_search("crossell_skus", $header[0])] =
                "crossell_skus";
            $skuIndex[array_search("related_skus", $header[0])] =
                "related_skus";
        }
        if ($csvdata) {
            foreach ($csvdata as $key => $reslinks) {
                if (isset($reslinks[$skuInd])) {
                    $sku = $reslinks[$skuInd];
                }
                for ($i = 0; $i < count($reslinks); $i++) {
                    if (isset($skuIndex[$i])) {
                        $prodcuctcode = $skuIndex[$i];
                        if ($prodcuctcode != "sku") {
                            $prodcuctcode = $skuIndex[$i];
                            if ($prodcuctcode == "upsell_skus") {
                                $prodcuctcode = "up_sell";
                            }
                            if ($prodcuctcode == "crossell_skus") {
                                $prodcuctcode = "cross_sell";
                            }
                            if ($prodcuctcode == "related_skus") {
                                $prodcuctcode = "relation";
                            }
                            $productnew = $productObject->loadByAttribute(
                                "sku",
                                $sku
                            );
                            $sql =
                                "Select link_type_id FROM " .
                                $tableName1 .
                                " WHERE code='" .
                                $prodcuctcode .
                                "'";
                            $linkId = $connection->fetchOne($sql);
                            if (isset($reslinks[$i])) {
                                $skuLinks = explode(",", $reslinks[$i]);
                                foreach ($skuLinks as $skuLink) {
                                    $skuLink = ltrim($skuLink);
                                    $productObject1 = $objectManager->get(
                                        "Magento\Catalog\Model\Product"
                                    );
                                    $linkedProduct = $productObject1->loadByAttribute(
                                        "sku",
                                        $skuLink
                                    );
                                    if ($linkedProduct) {
                                        $sql =
                                            "Select link_id FROM " .
                                            $tableName .
                                            " WHERE product_id=" .
                                            $productnew->getId() .
                                            " and linked_product_id=" .
                                            $linkedProduct->getId() .
                                            " and link_type_id=" .
                                            $linkId .
                                            "";
                                        $linkExist = $connection->fetchOne(
                                            $sql
                                        );
                                        if (empty($linkExist)) {
                                            $updatedskus .= $skuLink.',';
                                            $sql =
                                                "Insert Into " .
                                                $tableName .
                                                " (product_id, linked_product_id, link_type_id) Values (" .
                                                $productnew->getId() .
                                                "," .
                                                $linkedProduct->getId() .
                                                "," .
                                                $linkId .
                                                ")";
                                            $connection->query($sql);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $updatedskus;
    }
}
