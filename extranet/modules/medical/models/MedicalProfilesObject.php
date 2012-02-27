<?php
/**
 * Member Profile data
 * Management of the Items.
 *
 * @category  Cible
 * @package   Cible_MemberProfilesObject
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: MemberProfilesObject.php 826 2012-02-01 04:15:13Z ssoares $id
 */

/**
 * Manages Member Profile data.
 *
 * @category  Cible
 * @package   Cible_MemberProfiles
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: MemberProfilesObject.php 826 2012-02-01 04:15:13Z ssoares $id
 */
class MedicalProfilesObject extends DataObject
{

    protected $_dataClass   = 'MedicalProfilesData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();

    protected $_indexClass      = '';
//    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_constraint      = '';
    protected $_foreignKey      = 'MR_GenericProfileId';

    public function insert($data, $langId)
    {
        $oProfile = new GenericProfilesObject();

//        if (!empty($data['parentForm']))
//        {
//            $oAddress = new AddressObject();
//            $parentAddrId = $oAddress->insert($data['parentForm'], $langId);
//            unset($data['parentForm']);
//        }
//        $data['PP_AddressId'] = $parentAddrId;
//
//        $pId = $oProfile->insert($data, $langId);
//        $data['PP_GenericProfileId'] = $pId;

        $id = parent::insert($data, $langId);

        return $id;
    }

    public function save($id, $data, $langId)
    {
        parent::save($id, $data, $langId);
    }

    public function findData($filters = array())
    {
        $addr = array();
        $langId   = Zend_Registry::get('languageID');
        $data = parent::findData($filters);
        if (!empty($data))
        {
            $data = $data[0];
            $data['MR_Allergy'] = explode(',', $data['MR_Allergy']);
            $data['MR_Diseases'] = explode(',', $data['MR_Diseases']);
        }

        return $data;
    }

    public function _allergySrc($meta = array())
    {
        $src = array();
        $oRef = new ReferencesObject();
        $refs = $oRef->getRefByType('allergy');

        foreach ($refs as $ref)
        {
            $src[$ref['R_ID']] = $ref['RI_Value'];
        }

        return $src;
    }

    public function _diseasesSrc($meta = array())
    {
        $src = array();
        $oRef = new ReferencesObject();
        $refs = $oRef->getRefByType('diseases');

        foreach ($refs as $ref)
        {
            $src[$ref['R_ID']] = $ref['RI_Value'];
        }

        return $src;
    }
}