<?php

    class GalleryObject extends DataObject
    {
        protected $_dataClass = 'Galleries';
        protected $_dataId = 'G_ID';
        protected $_dataColumns = array(
            'G_Position' => 'G_Position',
            'G_Online' => 'G_Online',
            'G_ImageID'=> 'G_ImageID',
            'G_CreationDate' => 'G_CreationDate',
            'G_CategoryID' => 'G_CategoryID'            
        );
        
        protected $_indexClass = 'GalleriesIndex';
        protected $_indexId = 'GI_GalleryID';
        protected $_indexLanguageId = 'GI_LanguageID';
        protected $_indexColumns = array(
            'GI_Title' => 'GI_Title',
            'GI_Description' => 'GI_Description',
            'GI_ValUrl' => 'GI_ValUrl'
        );
    }
    
?>
