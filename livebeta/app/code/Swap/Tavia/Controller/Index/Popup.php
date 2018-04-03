<?php

namespace Swap\Tavia\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Popup extends \Magento\Framework\App\Action\Action {

    public function execute() {
//        echo "<pre>";
//        print_r($_REQUEST);
//        echo "</pre>";
        $catRequest = $_REQUEST['catRequest'];
//        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
//        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
//        print_r($response);
//        print_r($resultPage);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
        $id = 183; // YOUR PRODUCT ID;
        //$sku = '08155'; // YOUR PRODUCT SKU
        $product = $productRepository->getById($id);
        //$product = $productRepository->get($sku);
        $selectionCollection = $product->getTypeInstance(true)
                ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product), $product
        );
        foreach ($selectionCollection as $k => $proselection) {
            $selectionArray = [];
            $cats = $proselection->getCategoryIds();

//            echo "<pre>"; 
//            print_r($cats);
//           echo $proselection->getName();
//            echo "</pre>"; 
            if (@$cats[0]) {
                $cat = $objectManager->create('Magento\Catalog\Model\Category')->load($cats[0]);
                $cat_name = $cat->getName();
                if ($catRequest == $cat_name) {
                    $selectionArray['cat'] = $cats[0];
                    $selectionArray['cat_name'] = $cat_name;
                    $selectionArray['id'] = $proselection->getProductId();
                    $selectionArray['name'] = $proselection->getName();
                    $selectionArray['price'] = $proselection->getPrice();
                    $selectionArray['quantity'] = $proselection->getSelectionQty();
                    $selectionArray['scented'] = $proselection->getData('scented');
                    $selectionArray['wings'] = $proselection->getData('wings');
                    $selectionArray['deodorant'] = $proselection->getData('deodorant');
                    $selectionArray['baking_soda'] = $proselection->getData('with_baking_soda');
                    $selectionArray['long'] = $proselection->getData('long');
                    $selectionArray['manufacturerId'] = $proselection->getData('manufacturer');
                    $selectionArray['manufacturer'] = $proselection->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($proselection);
                    $selectionArray['brandId'] = $proselection->getData('brand');
                    $selectionArray['brand'] = $proselection->getResource()->getAttribute('brand')->getFrontend()->getValue($proselection);
                    $selectionArray['absorbancyId'] = $proselection->getData('absorbency');
                    $selectionArray['absorbancy'] = $proselection->getResource()->getAttribute('absorbency')->getFrontend()->getValue($proselection);
                    $selectionArray['sizeId'] = $proselection->getData('size');
                    $selectionArray['size'] = $proselection->getResource()->getAttribute('size')->getFrontend()->getValue($proselection);
                    $selectionArray['img'] = $proselection->getImage();
//            $selectionArray['img'] = $block->getUrl('pub/media/catalog') . 'product' . $proselection->getImage();
                    $productsArray[$proselection->getSelectionId()] = $selectionArray;
//            $productsArray[$cat_name][$proselection->getSelectionId()] = $selectionArray;
                }
            }
        }

//        return json_encode($productsArray);
//        echo "<pre>";
//        print_r($productsArray);
//        echo "</pre>";
        foreach ($productsArray as $key => $val) {
//            echo $val['absorbancy'];
//            echo $val['baking_soda'];
//            echo $val['scented'];
            ?>
            <tr id="item-<?php echo $val['id']; ?>" class="tr-popup <?php
            echo $val['cat_name'] . "_append_child ";
            if (@$val['brand']) {
                if ($val['brand'] == "O.B.") {
                    echo "OB ";
                } else {
                    echo $val['brand'] . ' ';
                }
            }
//            echo (@$val['brand']) ? $val['brand'] . " " : " ";
            echo (@$val['absorbancy']) ? "absorbancy" . $val['absorbancy'] . " " : " ";
            echo (@$val['long']) ? "long " : " ";
            echo (@$val['deodorant']) ? "deodorant " : " ";
            echo (@$val['scented']) ? "scented " : " ";
            echo (@$val['baking_soda']) ? "baking_soda " : " ";
            echo (@$val['wings']) ? "wings " : " ";
            ?>">
                <td class="carefree-title">
                    <span><img src="/pub/media/catalog/product/cache/thumbnail/75x75/beff4985b56e3afdbeabfc89641a4582<?php echo $val['img']; ?>"></span> 
                    <h4><strong><?php echo $val['brand']; ?></strong> <?php echo $val['name']; ?></h4> 
                    <span class="responsive_show_icons">
                        <span class="hideinresponsive">
                            <?php
                            if (@$val['absorbancy']) {
                                $image = $val['absorbancy'] . '.png';
                                ?><span class="tooltiptitle" >Absorbancy</span>
                                <img src="<?php echo "/pub/media/wysiwyg/" . $image; ?>">
                            <?php } ?>
                        </span >
                        <span class="hideinresponsive">
                            <?php if (@$val['scented']) { ?><span class="tooltiptitle" >Scented</span><img src="/pub/media/wysiwyg/carefree-icon-2.png"><?php } ?> 
                            <?php if (@$val['deodorant']) { ?><span class="tooltiptitle" >Deodorant</span><img src="/pub/media/wysiwyg/carefree-icon-3.png"><?php } ?>
                        </span>
                    </span>
                </td>
                <td class="carefree-icon hideicons">

                    <span>
                        <?php
                        if (@$val['absorbancy']) {
                            $image = $val['absorbancy'] . '.png';
                            ?>
                            <span class="tooltiptitle" >Absorbancy</span>
                            <img src="<?php echo "/pub/media/wysiwyg/" . $image; ?>">
                        <?php } ?>
                    </span>
                    <span>
                        <?php if (@$val['scented']) { ?>
                            <span class="tooltiptitle" >Scented</span>
                            <img src="/pub/media/wysiwyg/carefree-icon-2.png"><?php } ?> 
                        <?php if (@$val['deodorant']) { ?>
                            <span class="tooltiptitle" >Deodorant</span>
                            <img src="/pub/media/wysiwyg/carefree-icon-3.png"><?php } ?>
                    </span>

                </td>
                <td class="plus_minus"> 
                    <a class="incr-btn-popup" data-action="decrease" href="javascript:;">–</a>
                    <input class="s_item-popup l_item" data-max="" data-id="option-<?php echo $val['id']; ?>"  type="text" name="itemSelected[<?php echo $val['id']; ?>]" value="0"/>
                    <!--<input class="s_item-popup l_item" readonly="true" data-max=""  type="text" name="<?php // echo $catRequest;         ?>[<?php echo $val['id']; ?>]" value="0"/>-->
                    <a class="incr-btn-popup" data-action="increase" href="javascript:;">&plus;</a>
                </td>
                <td class="carefree-icon remove-icons">

                    <span class="hideinresponsive">
                        <?php
                        if (@$val['absorbancy']) {
                            $image = $val['absorbancy'] . '.png';
                            ?>
                            <span class="tooltiptitle" >Absorbancy</span>
                            <img src="<?php echo "/pub/media/wysiwyg/" . $image; ?>">
                        <?php } ?>
                    </span>
                    <span class="hideinresponsive">
                        <?php if (@$val['scented']) { ?><span class="tooltiptitle" >Scented</span><img src="/pub/media/wysiwyg/carefree-icon-2.png"><?php } ?> 
                        <?php if (@$val['deodorant']) { ?><span class="tooltiptitle" >Deodorant</span><img src="/pub/media/wysiwyg/carefree-icon-3.png"><?php } ?>
                    </span>

                </td>
            </tr>
            <?php
        }
        ?>
        <input type="hidden" value='<?php echo json_encode($productsArray); ?>' id="completeArrayId" id="completeArrayClass">
        <?php
    }

}
?>