<?php

/**
 * Module Utilities
 * Management of the banner data.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Utilities
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannerImageObject.php 826 2012-02-01 04:15:13Z ssoares $id
 */

/**
 * Manage data from banners table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_References
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannerImageObject.php 826 2012-02-01 04:15:13Z ssoares $id
 */
class BannerImageObject extends DataObject
{

    protected $_dataClass = 'BannerImageData';
    /* protected $_dataColumns = array(
      'ImageID' => 'BI_ID',
      'ImageFileName' => 'BI_Filename',
      'GroupID' => 'BI_GroupID'
      ); */
    protected $_indexClass = 'BannerImageIndex';
    protected $_indexLanguageId = 'BII_LanguageID';
    /* protected $_indexColumns = array(
      'ImageIndexID' => 'BII_ID',
      'ImageLanguageID' => 'BII_LanguageID',
      'ImageText' => 'BII_Text'
      ); */
    protected $_constraint = '';
    protected $_foreignKey = 'BI_GroupID';

    public function imageCollection($id = 0, $langId = null)
    {
        (array) $array = array();
        if (!$langId)
            $langId = Zend_Registry::get('languageID');

        $this->setOrderBy('BI_Seq ASC');

        if ($id > 0)
        {
            $groups = $this->getAll($langId, true, $id);
        }
        else
        {
            $groups = $this->getAll($langId);
        }

        return $groups;
    }

    public function imageCollectionInThisGroup($groupid)
    {
        (array) $return_array = array();
        $objArray = $this->imageCollection();
        foreach ($objArray as $object1)
        {
            if ($object1['BI_GroupID'] == $groupid)
            {
                array_push($return_array, $object1);
            }
        }
        return $return_array;
    }

    public function getImageToShow($imageGroupIndex)
    {
        $data = array();
        $config = Zend_Registry::get('config')->toArray();

        $originalMaxHeight = $config['banners']['image']['original']['maxHeight'];
        $originalMaxWidth = $config['banners']['image']['original']['maxWidth'];
        $imagePrefix = $originalMaxWidth . "x" . $originalMaxHeight . "_";

        $details = $this->imageCollectionInThisGroup($imageGroupIndex);
        $totalRow = count($details);
        if ($totalRow > 0)
        {
            foreach ($details as $key => $detail)
            {
                $data[$key]['text'] = $detail['BII_Text'];
                $data[$key]['url'] = $detail['BII_Url'];
                $data[$key]['img'] = Zend_Registry::get("web_root")
                    . "/data/images/banners/"
                    . $detail['BI_ID'] . "/"
                    . $imagePrefix
                    . $detail['BI_Filename'];
            }
        }

        return $data;
    }

}