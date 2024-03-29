<?php

class FormMenu extends Cible_Form_Block_Multilingual
{
    public function __construct($options = null)
    {
        $this->_addSubmitSaveClose = true;
        parent::__construct($options);
        $this->setName('page');
        
        $imageSrc   = $options['imageSrc'];
        $isNewImage = $options['isNewImage'];
        $menuId     = $options['menuId'];

        if($menuId == '')
            $pathTmp = "../../../../../data/images/menu/tmp";                                                
        else
            $pathTmp = "../../../../../data/images/menu/$menuId/tmp";                                                

        // input text for the title of the page
        $title = new Zend_Form_Element_Text('MenuTitle');
        $title->setLabel( Cible_Translation::getCibleText('form_label_menu_title'))
        ->setRequired(true)
        ->addValidator('NotEmpty',true,array('messages'=> Cible_Translation::getCibleText('error_field_required')))
        ->setAttrib('class','stdTextInput');

        $this->addElement($title);
        
        $menuItemType = new Zend_Form_Element_Radio('menuItemType');
        $menuItemType->setLabel( Cible_Translation::getCibleText('form_label_menu_type') )
            ->addMultiOption('page', Cible_Translation::getCibleText('form_label_menu_type_page'))
            ->addMultiOption('placeholder', Cible_Translation::getCibleText('form_label_menu_type_placeholder'))
            ->addMultiOption('external', Cible_Translation::getCibleText('form_label_menu_type_external'))
            ->setValue('page')            
            ->setAttrib('onclick','javascript:openTypePanel(this.value)')
            ->setSeparator('');            
            
        $this->addElement($menuItemType);

        $menuItemSecured = new Zend_Form_Element_Radio('menuItemSecured');
        $menuItemSecured->setLabel( Cible_Translation::getCibleText('manage_block_secured_menu_status') )
            ->addMultiOption('0', Cible_Translation::getCibleText('button_no'))
            ->addMultiOption('1', Cible_Translation::getCibleText('button_yes'))
            ->setValue('0')            
//            ->setAttrib('onclick','javascript:openTypePanel(this.value)')
            ->setSeparator('');            
            
        $this->addElement($menuItemSecured);

        $controllerName = new Zend_Form_Element_Text('ControllerName');
        $controllerName->setLabel( Cible_Translation::getCibleText('form_label_menu_destination_page') )
                       ->setAttrib('onfocus', "openPagePicker('page-picker-pagePicker');")
                       ->setRequired(true)
                       ->addValidator('NotEmpty',true,array('messages'=> Cible_Translation::getCibleText('error_field_required')))
                       ->setAttrib('class','stdTextInput');
    
        $this->addElement($controllerName);
        
		$pagePicker = new Cible_Form_Element_PagePicker('pagePicker',array(
            'menu'=>'Principal',
            'associatedElement' => 'ControllerName',
            'onclick' => "javascript:closePagePicker(\"page-picker-pagePicker\")"
        ));
        
		$pagePicker->setLabel(Cible_Translation::getCibleText('form_label_page_picker'))
			->setRequired(true)
            ->addValidator('NotEmpty',true,array('messages'=> Cible_Translation::getCibleText('error_page_selection_required')));
        
        $pagePicker->setDecorators(array(
                    'ViewHelper',
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => "page-picker", 'id' => "page-picker-pagePicker")),
                ));
            
        $this->addElement($pagePicker);

        $this->addDisplayGroup(array('ControllerName','pagePicker'), 'pageSelectionGroup');
        $this->getDisplayGroup('pageSelectionGroup')
            ->setAttrib('class', 'pageSelectionGroup')
            ->removeDecorator('DtDdWrapper');
        
		$link = new Zend_Form_Element_Text('MenuLink');
        $link->setLabel(Cible_Translation::getCibleText('form_label_menu_destination_link'))
        ->setRequired(true)
        ->addValidator('NotEmpty',true,array('messages'=> Cible_Translation::getCibleText('error_field_required')))
        //->addValidator('Url', true, array('messages'=> Cible_Translation::getCibleText('error_invalid_url')))
        ->addPrefixPath('Cible','Cible')
        ->setAttrib('class','stdTextInput');
        
        $this->addElement($link);
        
        // input text for the css li
        $style = new Zend_Form_Element_Text('MenuTitleStyle');
        $style->setLabel( Cible_Translation::getCibleText('form_label_menu_title_style'))
        ->setAttrib('class','stdTextInput');
        $this->addElement($style);

        $this->addDisplayGroup(array('MenuLink'), 'externalLinkSelectionGroup');
        $this->getDisplayGroup('externalLinkSelectionGroup')
            ->setAttrib('class', 'externalLinkSelectionGroup')
            ->setAttrib('style','display: none')
            ->removeDecorator('DtDdWrapper');;
        
        // Uses image
        $loadImage = new Zend_Form_Element_Checkbox('loadImage');
        $loadImage->setLabel($this->getView()->getCibleText('form_label_menu_load_image'));            
        $loadImage->removeDecorator('DtDdWrapper');
        $loadImage->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));
        
        // Show this menu item
        $MID_Show = new Zend_Form_Element_Checkbox('MID_Show');
        $MID_Show->setValue(1);
        $MID_Show->setLabel($this->getView()->getCibleText('form_label_menu_show_item'));            
        $MID_Show->removeDecorator('DtDdWrapper');
        $MID_Show->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));

        $this->addElement($MID_Show);

        $this->addElement($loadImage);
        // hidden specify if new image for the news
        $newImage = new Zend_Form_Element_Hidden('isNewImage', array('value'=>$isNewImage));
        $newImage->removeDecorator('Label');
        $this->addElement($newImage);

        // To allow to display title and image
        $imgTitle = new Zend_Form_Element_Checkbox('menuImgAndTitle');
        $imgTitle->setLabel($this->getView()->getCibleText('form_label_menu_display_image_and_title'));
        $imgTitle->removeDecorator('DtDdWrapper');
        $imgTitle->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));

        $this->addElement($imgTitle);

        // IMAGE
        $imageTmp  = new Zend_Form_Element_Hidden('menuImage_tmp');
        $imageTmp->removeDecorator('Label');
        $this->addElement($imageTmp);

        $imageOrg  = new Zend_Form_Element_Hidden('menuImage_original');
        $imageOrg->removeDecorator('Label');
        $this->addElement($imageOrg);

        $imageView = new Zend_Form_Element_Image(
            'menuImage_preview',
            array('onclick'=>'return false;')
            );
        $imageView->setImage($imageSrc)
            ->removeDecorator('DtDdWrapper');
        $this->addElement($imageView);

        $imagePicker = new Cible_Form_Element_ImagePicker(
            'menuImage', 
            array( 
                'onchange' => "document.getElementById('imageView').src = document.getElementById('menuImage').value",
                'associatedElement' => 'menuImage_preview',
                'pathTmp'=>$pathTmp,
                'contentID'=>$menuId
                ));            
        $imagePicker->removeDecorator('Label');
        $this->addElement($imagePicker);

        $this->addDisplayGroup(array('menuImgAndTitle', 'isNewImage', 'menuImage_tmp', 'menuImage_original', 'menuImage_preview', 'menuImage'), 'imageGroup');
        $this->getDisplayGroup('imageGroup')
            ->setAttrib('class', 'imageGroup')
            ->setAttrib('style','display: none')
            ->removeDecorator('DtDdWrapper');
    }
}
