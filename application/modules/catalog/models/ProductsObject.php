<?php
/**
 * Module Catalog
 * Management of the products for Logiflex.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsObject.php 598 2011-09-07 19:32:59Z freynolds $id
 */

/**
 * Manage data from products table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsObject.php 598 2011-09-07 19:32:59Z freynolds $id
 */
class ProductsObject extends DataObject
{
    protected $_dataClass   = 'ProductsData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();

    protected $_indexClass      = 'ProductsIndex';
//    protected $_indexId         = '';
    protected $_indexLanguageId = 'PI_LanguageID';
    protected $_constraint      = 'P_SubCategoryID';
//    protected $_indexColumns    = array();

    protected $_query;
    protected $_searchColumns = array(
        'data' => array(),
        'index' => array(
            'PI_Name',
            'PI_MotsCles',
            'PI_DescriptionPro',
            'PI_DescriptionPublic',
            'PI_NoteSupplementairePro',
            'PI_NoteSupplementairePublic'
        )
    );
    
    /**
     * Setter for the query used to find data.
     * 
     * @param Zend_Db_Select $query
     * @return void
     */
    public function setQuery(Zend_Db_Select $query)
    {
        $this->_query = $query;
    }
    
    /**
     * Build the list of products for a dropdown select.
     * Data are grouped by category and subcategory.
     * 
     * @param int $langId
     * 
     * @return array
     */
    public function productsCollection($langId)
    {
        (array) $array = array();
        
        $select = $this->getAll($langId, false);

        $select->joinLeft(
            'Catalog_SousCategoriesData',
            'P_SubCategoryID = SC_ID',
            array())
            ->join(
                'Catalog_SousCategoriesIndex',
                'SC_ID = SCI_SousCategoryID',
                'SCI_Name')
            ->join(
                'Catalog_CategoriesData',
                'SC_CategoryID = CC_ID',
                array())
            ->join('Catalog_CategoriesIndex', 'CC_ID = CI_CategoryID', 'CCI_Name')
//            ->where($this->_indexLanguageId .' = ?', $langId)
            ->where('SCI_LanguageID = ?', $langId)
            ->where('CCI_LanguageID = ?', $langId)
            ->order('CCI_Name')
        ;

        $products =$this->_db->fetchAll($select);
        
        foreach ($products as $data)
        {
            $key = $data['CCI_Name'] . "-" . $data['SCI_Name'];
            //If cat not in array add it as an array
            if(!array_key_exists($key, $array))
            {
                $array[$key] = array($data['P_ID'] => $data['PI_Name']);
                
            }
            //Else Add values product id and product name into the subcat array
            else
            {
                $array[$key][$data['P_ID']] = $data['PI_Name'];
            }

        }

        return $array;
    }

    public function getProducts($langId = null, $array = true, $id = null){

        if (isset($this->_query) && $this->_query instanceof Zend_Db_Select)
        {
            $select = $this->_query;

            $select->joinRight(
                    $this->_oDataTableName,
                    'SC_ID = P_SubCategoryID'
                    );
        }
        else
        {
            $select = $this->_db->select()
                    ->from($this->_oDataTableName);
        }
        $select->joinLeft(
                    $this->_oIndexTableName,
                    $this->_dataId . " = " . $this->_indexId
                    );
        /**
         * @todo Else case for direct product list.
         */
        if (!is_null($langId))
        {
            $select->where("{$this->_indexLanguageId} = ?", $langId);
        }
       
         if ($array)
            return $products = $this->_db->fetchAll($select);
        else
            return $select;
    }

    /**
     * Set filters for search by keywords.
     *
     * @param Zend_Db_Select $select The begining of the query to complete.
     *
     * @return void
     */
    public function autocompleteSearch($value, $langId = 1)
    {
        $select = parent::getAll($langId, false);

//        $select->where('P_Type = ?', 'catalog');

        $this->keywordExist(
                array($value),
                $select,
                $langId);

        $products = $this->_db->fetchAll($select);

        return $products;

    }

    /**
     * Fetch the id of a product according the formatted string from URL.
     *
     * @param string $string
     *
     * @return int Id of the searched category
     */
    public function getIdByName($string)
    {
        $select = $this->_db->select()
                ->from($this->_oDataTableName, 'P_ID')
                ->joinLeft(
                        $this->_oIndexTableName,
                        $this->_dataId . " = " . $this->_indexId,
                        '')
                ->where("PI_ValUrl LIKE ?", "%" . $string . "%")
                ;

        $id = $this->_db->fetchRow($select);

        return $id['P_ID'];
    }
}