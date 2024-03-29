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
class ParentProfilesObject extends DataObject
{

    protected $_dataClass   = 'ParentProfilesData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();

    protected $_indexClass      = '';
//    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_constraint      = '';
    protected $_foreignKey      = 'PP_GenericProfileId';
    protected $_formDataName    = 'parentForm';
    protected $_addressField    = 'PP_AddressId';

    public function getFormDataName()
    {
        return $this->_formDataName;
    }

    public function getAddressField()
    {
        return $this->_addressField;
    }
    
    public function insert($data, $langId)
    {
        $oProfile = new GenericProfilesObject();

        if (!empty($data[$this->_formDataName]))
        {
            $oAddress = new AddressObject();
            $parentAddrId = $oAddress->insert($data[$this->_formDataName], $langId);
            unset($data[$this->_formDataName]);
        }
        $data[$this->_addressField] = $parentAddrId;

        $pId = $oProfile->insert($data, $langId);
        $data['PP_GenericProfileId'] = $pId;
        $id = parent::insert($data, $langId);

        return $id;
    }

    public function save($id, $data, $langId)
    {
        $addrId = 0;
        if (isset($data[$this->_formDataName]))
        {
            $oAdress = new AddressObject();
            $addr = $data[$this->_formDataName];
//            $addrShip = $data['addressShipping'];
        }

        if (!empty($addr))
        {
            $addrId = $oAdress->save($data[$this->_addressField], $addr, $langId);

//            if ($addrShip['duplicate'] == 1)
//            {
//                $addrBill['A_Duplicate'] = $billId;
//                $shipId = $oAdress->save($addrShip['MP_ShippingAddrId'], $addrBill, $langId);
//            }
//            else
//            {
//                $addrShip['A_Duplicate'] = 0;
//                $shipId = $oAdress->save($addrShip['MP_ShippingAddrId'], $addrShip, $langId);
//            }
            if (empty($data[$this->_addressField]))
                $data[$this->_addressField] = $addrId;
//            $data['MP_ShippingAddrId'] = $shipId;
        }

        parent::save($id, $data, $langId);
    }

    public function findData($filters = array())
    {
        $addr = array();
//        $shipAddr = array();
        $oAddress = new AddressObject();
        $langId   = Zend_Registry::get('languageID');
        $data = parent::findData($filters);
        if (!empty($data))
        {
            $data = $data[0];
            $addrId = $data[$this->_addressField];

            if (!empty($addrId))
            {
                $addr = $oAddress->getAll($langId, true, $addrId);
                $addr = $addr[0];
                $addr[$this->_addressField] = $addrId;
            }

            $data[$this->_formDataName] = $addr;
//            $data['addressShipping'] = $shipAddr;
        }

        return $data;
    }

    public function getParentDetails($id, $filters = array())
    {
        $data = array();
        $parentData = $this->findData($filters);
        if ($id > 0 && !empty($parentData))
        {
            $oGeneric = new GenericProfilesObject();
            $data = $oGeneric->populate($id, 1);

            $role = $this->_parentsProfileSrc();
            $roleId = $parentData['PP_Role'];

            $roleLabel = $role[$roleId];
            $parentData['RoleLabel'] = $roleLabel;
            $data = array_merge($parentData, $data);
        }
        return $data;
    }
    public function _parentsProfileSrc($meta = array())
    {
        $src = array();
        $oRef = new ReferencesObject();
        $roles = $oRef->getRefByType('role');

        foreach ($roles as $role)
        {
            $src[$role['R_ID']] = $role['RI_Value'];
        }

        return $src;
    }

    public function _listRespSrc($meta = array())
    {
        $src = array();
        $oRef = new ReferencesObject();
        $roles = $oRef->getRefByType('garde');

        foreach ($roles as $role)
        {
            $src[$role['R_ID']] = $role['RI_Value'];
        }

        return $src;
    }
}