<?php

class Parent_IndexController extends Cible_Controller_Block_Abstract
{
    protected $_labelSuffix;
    protected $_colTitle      = array();
    protected $_moduleID      = 31;
    protected $_defaultAction = '';
    protected $_moduleTitle   = 'parent';
    protected $_name          = 'index';
    protected $_ID            = 'id';
    protected $_defaultRender = 'list';
    protected $_currentAction = '';
    protected $_actionKey     = '';
    protected $_imageSrc      = '';

    protected $_imageFolder;
    protected $_rootImgPath;
    protected $_formName   = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'list' => 'ParentProfilesObject'
    );
    protected $_actionsList = array();

    protected $_disableExportToExcel = false;
    protected $_disableExportToPDF   = false;
    protected $_disableExportToCSV   = false;
    protected $_enablePrint          = false;
    protected $_constraint;
    protected $_orderBy;

    /**
     * Set some properties to redirect and process actions.
     *
     * @access public
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        // Sets the called action name. This will be dispatched to the method
        $this->_currentAction = $this->_getParam('action');

        // The action (process) to do for the selected object
        $this->_actionKey = $this->_getParam('actionKey');

        $this->_formatName();
        $this->view->assign('cleaction', $this->_labelSuffix);

        $dataImagePath = "../../"
                . $this->_config->document_root
                . "/data/images/";

        if(isset($this->_objectList[$this->_currentAction]))
            $this->_imageFolder = $dataImagePath
                    . $this->_moduleTitle . "/"
                    . $this->_objectList[$this->_currentAction] . "/";

        if(isset($this->_objectList[$this->_currentAction]))
            $this->_rootImgPath = Zend_Registry::get("www_root")
                    . "/data/images/"
                    . $this->_moduleTitle . "/"
                    . $this->_objectList[$this->_currentAction] . "/";
    }

    /**
     * Allocates action for parents data management.<br />
     * Prepares data utilized to activate controller actions.
     *
     * @access public
     *
     * @return void
     */
    public function listAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_orderBy = 'GP_LastName';
            $this->_constraint = 'PP_Role';
            $this->_colTitle = array(
                'GP_FirstName' => array('width' => '250px'),
                'GP_LastName'  => array('width' => '250px'),
                'GP_Email'  => array('width' => '250px'),
                'RI_Value'  => array('width' => '250px'),
                );

            $this->_actionsList = array(
                array(
                    'commands' => array(
                        'add' => array(
                            'module'=>'users',
                            'controller'=>'index',
                            'action'=> 'general',
                            'actionKey'=> 'add',
                            'returnModule'=> $this->_moduleTitle,
                            'returnAction'=>'list'
                            )
                        )
                    ),
                array(
                    'action_panel' =>
                        array('edit' => array(
                            'label' => $this->view->getCibleText('menu_submenu_action_edit'),
                            'url' => $this->view->url(
                                    array('module'=>'users',
                                        'action' => 'general',
                                        'actionKey'=> 'edit',
                                        $this->_ID  => "-ID-",
                                        'returnModule'=>$this->_moduleTitle,
                                        'returnAction'=>'list'
                                        )
                                    ),
                            'findReplace' => array(
                                        'search' => '-ID-',
                                        'replace' => 'PP_GenericProfileId'
                                    )
                        )),
                        array('delete' => array(
                            'label' => $this->view->getCibleText('menu_submenu_action_delete'),
                            'url' => $this->view->url(
                                        array('module'=>'users',
                                        'action' => 'general',
                                        'actionKey'=> 'delete',
                                        $this->_ID  => "-ID-",
                                        'returnModule'=>$this->_moduleTitle,
                                        'returnAction'=>'list'
                                        )
                                    ),
                            'findReplace' =>
                                    array(
                                        'search' => '-ID-',
                                        'replace' => 'PP_GenericProfileId'
                                    )
                        ))
                    )
                );

            $this->_joinTables = array('GenericProfilesObject','ReferencesObject');

            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables);

                return $params;
            }

            $this->_formName = 'FormParentProfile';
            $this->_redirectAction();
        }
    }

    /**
     * Add action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function addAction()
    {
        $oDataName = $this->_objectList[$this->_currentAction];
        $oData     = new $oDataName();

        $lang = $this->_getParam('lang');
        if (!$lang)
        {
            $this->_registry->currentEditLanguage = $this->_defaultEditLanguage;
            $langId = $this->_defaultEditLanguage;
        }
        else
        {
            $langId = Cible_FunctionsGeneral::getLanguageID($lang);
            $this->_registry->currentEditLanguage = $langId;
        }

        $returnAction = $this->_getParam('return');

        $baseDir = $this->view->baseUrl() . "/";

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if (isset($formData[$this->_imageSrc]) && $formData[$this->_imageSrc] <> "")
                    if ($formData[$this->_imageSrc] <> "")
                        $imageSrc = $this->_rootImgPath
                                . "tmp/mcith/mcith_"
                                . $formData[$this->_imageSrc];
            }

            if ($returnAction)
                $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $returnAction;
            else
                $returnUrl = $this->_moduleTitle
                        . "/" . $this->_name . "/" . $this->_currentAction . "/";

            // generate the form
            $form = new $this->_formName(array(
                        'baseDir'    => $baseDir,
                        'cancelUrl'  => "$baseDir$returnUrl",
                        'moduleName' => $this->_moduleTitle . "/"
                            . $this->_objectList[$this->_currentAction],
                        'imageSrc'   => $imageSrc,
                        'imgField'   => $this->_imageSrc,
                        'object'     => $oData,
                        'dataId'     => '',
                        'isNewImage' => true,
                        'isXmlHttpRequest' => $this->_isXmlHttpRequest,
                    ));

            $this->view->form = $form;

            $options = array(
                'disabledDefaultActions' => true,
                'mode' => 'edit'
                );
            $profile = new FormGenericProfile($options);
            $elems = $profile->getElements();
            foreach ($elems as $key => $elem)
                $names[] = $key;

            $profile->setDecorators(array('FormElements','Form'));
            $profile->addDisplayGroup($names, 'genericForm');
            $profile->getDisplayGroup('genericForm')->removeDecorator('DtDdWrapper');
            $profile->getDisplayGroup('genericForm')->setLegend('Infos générales');
            $form->addSubForm($profile, 'genericForm');
            $form->getSubForm('genericForm')->setOrder(0);
            $form->getSubForm('parentForm')->setOrder(1);
            $form->getSubForm('parentForm')->getElement('selectedState')->removeDecorator('DtDdWrapper');
            $form->getDisplayGroup('data')->setOrder(3);
            $form->getDisplayGroup('data')->setAttrib('style', 'width:93%');
            $form->getElement('PP_EmploiTps')->getDecorator('row')
                ->setOption('style', 'width: 45%; display: inline-block; margin-right: 25px;');
            $form->getElement('PP_Notes')->getDecorator('row')
                ->setOption('style', 'width: 45%; display: inline-block; margin-left: 25px;');

            //width: 45%; display: inline-block; margin-right: 25px;
            // action
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                $formData = ($formData['data']);
                $formData = $this->_rebuildData($formData);
                if ($form->isValid($formData))
                {
//                    $formData = $this->_mergeFormData($formData);

                    $recordID = $oData->insert($formData, $langId);
                    /* IMAGES */
                    if (!empty($this->_imageSrc))
                    {
                        mkdir($this->_imageFolder . $recordID)
                                or die("Could not make directory");
                        mkdir($this->_imageFolder . $recordID . "/tmp")
                                or die("Could not make directory");
                    }

//                    if ($form->getValue($this->_imageSrc) <> '')
//                    {
//                        //Get config data
//                        $config = Zend_Registry::get('config')->toArray();
//                        //Set sizes for the image
//                        $srcOriginal       = $this->_imageFolder . "tmp/" . $form->getValue($this->_imageSrc);
//                        $originalMaxHeight = $config[$this->_moduleTitle]['image']['original']['maxHeight'];
//                        $originalMaxWidth  = $config[$this->_moduleTitle]['image']['original']['maxWidth'];
//
//                        $originalName = str_replace(
//                                        $form->getValue($this->_imageSrc),
//                                        $originalMaxWidth
//                                        . 'x'
//                                        . $originalMaxHeight
//                                        . '_'
//                                        . $form->getValue($this->_imageSrc),
//                                        $form->getValue($this->_imageSrc)
//                        );
//
//
//                        $srcMedium = $this->_imageFolder
//                                . "tmp/medium_"
//                                . $form->getValue($this->_imageSrc);
//                        $mediumMaxHeight = $config[$this->_moduleTitle]['image']['medium']['maxHeight'];
//                        $mediumMaxWidth = $config[$this->_moduleTitle]['image']['medium']['maxWidth'];
//                        $mediumName = str_replace(
//                                        $form->getValue($this->_imageSrc),
//                                        $mediumMaxWidth
//                                        . 'x'
//                                        . $mediumMaxHeight
//                                        . '_'
//                                        . $form->getValue($this->_imageSrc),
//                                        $form->getValue($this->_imageSrc)
//                        );
//
//                        $srcThumb = $this->_imageFolder
//                                . "tmp/thumb_"
//                                . $form->getValue($this->_imageSrc);
//                        $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
//                        $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];
//                        $thumbName = str_replace(
//                                        $form->getValue($this->_imageSrc),
//                                        $thumbMaxWidth
//                                        . 'x'
//                                        . $thumbMaxHeight
//                                        . '_'
//                                        . $form->getValue($this->_imageSrc),
//                                        $form->getValue($this->_imageSrc)
//                        );
//
//                        copy($srcOriginal, $srcMedium);
//                        copy($srcOriginal, $srcThumb);
//
//                        Cible_FunctionsImageResampler::resampled(
//                                        array(
//                                            'src' => $srcOriginal,
//                                            'maxWidth' => $originalMaxWidth,
//                                            'maxHeight' => $originalMaxHeight)
//                        );
//                        Cible_FunctionsImageResampler::resampled(
//                                        array(
//                                            'src' => $srcMedium,
//                                            'maxWidth' => $mediumMaxWidth,
//                                            'maxHeight' => $mediumMaxHeight)
//                        );
//                        Cible_FunctionsImageResampler::resampled(
//                                        array(
//                                            'src' => $srcThumb,
//                                            'maxWidth' => $thumbMaxWidth,
//                                            'maxHeight' => $thumbMaxHeight)
//                        );
//
//                        rename($srcOriginal, $this->_imageFolder . $recordID . "/" . $originalName);
//                        rename($srcMedium, $this->_imageFolder . $recordID . "/" . $mediumName);
//                        rename($srcThumb, $this->_imageFolder . $recordID . "/" . $thumbName);
//                    }

                    if (!$this->_isXmlHttpRequest)
                        // redirect
                        $this->_redirect($returnUrl);
                    else
                    {
                        $this->disableView();
                        echo json_encode($recordID);
                    }
                }
                else
                {
                    $form->populate($formData);
                }
            }
        }
    }

    /**
     * Edit action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function editAction()
    {
        $imageSrc = "";
        $id       = (int) $this->_getParam($this->_ID);
        $page     = (int) $this->_getParam('page');

        $baseDir      = $this->view->baseUrl() . "/";
        $returnAction = $this->_getParam('return');
        $cancelUrl    = $baseDir . "/"
                . $this->_moduleTitle . "/"
                . $this->_name . "/"
                . $this->_currentAction
                . "/page/" . $page;

        $oDataName = $this->_objectList[$this->_currentAction];

        $oData = new $oDataName();

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $this->_currentAction . "/"
                    . "page/" . $page;

            // Get data details
            $data = $oData->populate($id, $this->_defaultEditLanguage);

            // image src.
            if (!empty($data[$this->_imageSrc]))
            {
                $config = Zend_Registry::get('config')->toArray();
                $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
                $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];

                $this->view->assign(
                        'imageUrl',
                        $this->_rootImgPath
                        . $id . "/"
                        . str_replace(
                                $data[$this->_imageSrc],
                                $thumbMaxWidth
                                . 'x'
                                . $thumbMaxHeight
                                . '_'
                                . $data[$this->_imageSrc],
                                $data[$this->_imageSrc])
                );
                $isNewImage = 'false';

                if ($this->_request->isPost())
                {
                    $formData = $this->_request->getPost();
                    if ($formData[$this->_imageSrc] <> $data[$this->_imageSrc])
                    {
                        if ($formData[$this->_imageSrc] == "")
                            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                        else
                            $imageSrc = $this->_rootImgPath
                                    . $id
                                    . "/tmp/mcith/mcith_"
                                    . $formData[$this->_imageSrc];

                        $isNewImage = 'true';
                    }
                    else
                    {
                        if ($data[$this->_imageSrc] == "")
                            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                        else
                            $imageSrc = $this->_rootImgPath
                                    . $id . "/"
                                    . str_replace(
                                            $data[$this->_imageSrc],
                                            $thumbMaxWidth
                                            . 'x'
                                            . $thumbMaxHeight . '_'
                                            . $data[$this->_imageSrc],
                                            $data[$this->_imageSrc]);
                    }
                }
                else
                {
                    if (empty($data[$this->_imageSrc]))
                        $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                    else
                        $imageSrc = $this->_rootImgPath
                                . $id . "/"
                                . str_replace(
                                        $data[$this->_imageSrc],
                                        $thumbMaxWidth
                                        . 'x'
                                        . $thumbMaxHeight . '_'
                                        . $data[$this->_imageSrc],
                                        $data[$this->_imageSrc]);
                }
            }
            // generate the form
            $form = new $this->_formName(
                            array(
                                'moduleName' => $this->_moduleTitle . "/"
                                    . $this->_objectList[$this->_currentAction],
                                'baseDir'    => $baseDir,
                                'cancelUrl'  => $cancelUrl,
                                'imageSrc'   => $imageSrc,
                                'imgField'   => $this->_imageSrc,
                                'dataId'     => $id,
                                'data'       => $data,
                                'isNewImage' => 'true'
                            )
            );
            $this->view->form = $form;

            // action
            if (!$this->_request->isPost())
            {

                $form->populate($data);
            }
            else
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    $formData = $this->_mergeFormData($formData);
                    if ($formData['isNewImage'] == 'true' && $form->getValue($this->_imageSrc) <> '')
                    {
                        $config = Zend_Registry::get('config')->toArray();
                        $srcOriginal = $this->_imageFolder
                                . $id
                                . "/tmp/"
                                . $form->getValue($this->_imageSrc);
                        $originalMaxHeight = $config[$this->_moduleTitle]['image']['original']['maxHeight'];
                        $originalMaxWidth = $config[$this->_moduleTitle]['image']['original']['maxWidth'];
                        $originalName = str_replace(
                                        $form->getValue($this->_imageSrc),
                                        $originalMaxWidth
                                        . 'x'
                                        . $originalMaxHeight . '_'
                                        . $form->getValue($this->_imageSrc),
                                        $form->getValue($this->_imageSrc));


                        $srcMedium = $this->_imageFolder
                                . $id . "/tmp/medium_"
                                . $form->getValue($this->_imageSrc);

                        $mediumMaxHeight = $config[$this->_moduleTitle]['image']['medium']['maxHeight'];
                        $mediumMaxWidth = $config[$this->_moduleTitle]['image']['medium']['maxWidth'];
                        $mediumName = str_replace(
                                        $form->getValue($this->_imageSrc),
                                        $mediumMaxWidth
                                        . 'x'
                                        . $mediumMaxHeight . '_'
                                        . $form->getValue($this->_imageSrc),
                                        $form->getValue($this->_imageSrc));

                        $srcThumb = $this->_imageFolder
                                . $id
                                . "/tmp/thumb_"
                                . $form->getValue($this->_imageSrc);
                        $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
                        $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];
                        $thumbName = str_replace(
                                        $form->getValue($this->_imageSrc),
                                        $thumbMaxWidth
                                        . 'x'
                                        . $thumbMaxHeight . '_'
                                        . $form->getValue($this->_imageSrc),
                                        $form->getValue($this->_imageSrc));

                        copy($srcOriginal, $srcMedium);
                        copy($srcOriginal, $srcThumb);

                        Cible_FunctionsImageResampler::resampled(
                                        array(
                                            'src' => $srcOriginal,
                                            'maxWidth' => $originalMaxWidth,
                                            'maxHeight' => $originalMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                                        array(
                                            'src' => $srcMedium,
                                            'maxWidth' => $mediumMaxWidth,
                                            'maxHeight' => $mediumMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                                        array(
                                            'src' => $srcThumb,
                                            'maxWidth' => $thumbMaxWidth,
                                            'maxHeight' => $thumbMaxHeight)
                        );

                        rename($srcOriginal,
                                $this->_imageFolder
                                . $id . "/" . $originalName);
                        rename($srcMedium,
                                $this->_imageFolder
                                . $id . "/" . $mediumName);
                        rename($srcThumb,
                                $this->_imageFolder
                                . $id . "/" . $thumbName);
                    }
                    $oData->save($id, $formData, $this->getCurrentEditLanguage());
                    // redirect
                    $this->_redirect($returnUrl);
                }
            }
        }
    }

    /**
     * Delete action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function deleteAction()
    {
        // variables
        $page = (int) $this->_getParam('page');
        $blockId = (int) $this->_getParam('blockID');
        $id = (int) $this->_getParam($this->_ID);

        $this->view->return = $this->view->baseUrl() . "/"
                . $this->_moduleTitle . "/"
                . $this->_name . "/"
                . $this->_currentAction . "/"
                . "page/" . $page;

        $this->view->action = $this->_currentAction;

        $returnAction = $this->_getParam('return');

        if ($returnAction)
            $returnUrl = $this->_moduleTitle . "/index/" . $returnAction;
        else
            $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $this->_currentAction . "/"
                    . "page/" . $page;

        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();

        if (Cible_ACL::hasAccess($page))
        {
            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete');
                if ($del && $id > 0)
                {
                    $oData->delete($id);
                }

                $this->_redirect($returnUrl);
            }
            elseif ($id > 0)
            {
                // get date details
                $this->view->data = $oData->populate($id, $this->getCurrentEditLanguage());
            }
        }
    }

    /**
     * Creates the list of data for this action for the current object.
     *
     * @access public
     *
     * @param string $objectName String tot create the good object.
     *
     * @return void
     */
    private function _listAction($objectName)
    {
        $page = $this->_getParam('page');
        $lang = $this->_getParam('lang');
        if (!$lang)
        {
            $this->_registry->currentEditLanguage = $this->_defaultEditLanguage;
            $langId = $this->_defaultEditLanguage;
        }
        else
        {
            $langId = Cible_FunctionsGeneral::getLanguageID($lang);
            $this->_registry->currentEditLanguage = $langId;
        }

        if ($page == '')
            $page = 1;
        // Create the object from parameter
        $oData = new $objectName();
        if (!empty($this->_orderBy))
            $oData->setOrderBy($this->_orderBy);
        // get needed data to create the list
        $columnData  = $oData->getDataColumns();
        $dataTable   = $oData->getDataTableName();if (!empty($this->_joinTables))
            foreach ($this->_joinTables as $key => $obj)
            {
                $oTmpObj = new $obj();
                $tables[$oTmpObj->getDataTableName()] = $oTmpObj->getDataColumns();
                $tables[$oTmpObj->getIndexTableName()] = $oTmpObj->getIndexColumns();

            }
        $indexTable  = $oData->getIndexTableName();
        $columnIndex = $oData->getIndexColumns();
        $tabId = $oData->getDataId();
        //Set the tables from previous collected data
        $tables = array(
            $dataTable => $columnData,
            $indexTable => $columnIndex
        );
        if (!empty($this->_joinTables))
            foreach ($this->_joinTables as $key => $obj)
            {
                $oTmpObj = new $obj();
                $tables[$oTmpObj->getDataTableName()] = $oTmpObj->getDataColumns();
                $tables[$oTmpObj->getIndexTableName()] = $oTmpObj->getIndexColumns();

            }
        // Set the select query to create the paginator and the list.
        $select = $oData->getAll($langId, false);
        $params = array('constraint' => $oData->getForeignKey());
        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        $select = $this->_addJoinQuery($select, $params);
        // Set the the header of the list (columns name used to display the list)
        $field_list = $this->_colTitle;
        // Set the options of the list = links for actions (add, edit, delete...)
        $options = $this->_setActionsList($tabId, $page);
        //Create the list with the paginator data.
        $mylist = New Cible_Paginator($select, $tables, $field_list, $options);
        // Assign the view script for rendering
        $this->_helper->viewRenderer->setRender($this->_defaultRender);
        //Assign to the render the list created previously.
        $this->view->assign('mylist', $mylist);
    }

    /**
     * Export data according to given parameters.
     *
     * @return void
     */
    public function toExcelAction()
    {
        $this->type     = 'CSV';
        $this->filename = $this->_actionKey . '.csv';
        $params         = array();

        $actionName = $this->_actionKey . 'Action';
        $params     = $this->$actionName(true);
        $oDataName  = $this->_objectList[$this->_actionKey];
        $lines      = new $oDataName();

        $constraint = $lines->getForeignKey();
        $params['constraint'] = $constraint;

        $this->tables = array(
            $lines->getDataTableName() => $lines->getDataColumns()
        );

        $this->view->params = $this->_getAllParams();

        $columns       = array_keys($params['columns']);
        $this->fields  = array_combine($columns, $columns);
        $this->filters = array();

        $pageID = $this->_getParam('pageID');
        $langId = $this->_defaultEditLanguage;

        $select = $lines->getAll($langId, false);
        $select = $this->_addJoinQuery($select, $params);

        $this->select = $select;

        parent::toExcelAction();
    }

    /**
     * Format the current action name to bu used for label texts translations.
     *
     * @access private
     *
     * @return void
     */
    protected function _formatName()
    {
        $this->_labelSuffix = str_replace(array('/', '-'), '_', $this->_currentAction);
    }

    /**
     * Reditects the current action to the "real" action to process.
     *
     * @access public
     *
     * @return void
     */
    private function _redirectAction()
    {
        //Redirect to the real action to process If no actionKey = list page.
        switch ($this->_actionKey)
        {
            case 'add':
                $this->addAction();
                $this->_helper->viewRenderer->setRender('add');
                break;
            case 'edit':
                $this->editAction();
                $this->_helper->viewRenderer->setRender('edit');
                break;
            case 'delete':
                $this->deleteAction();
                $this->_helper->viewRenderer->setRender('delete');
                break;

            default:
                $this->_listAction($this->_objectList[$this->_currentAction]);
                break;
        }
    }

    /**
     * Set options array or the list view. Options are the actions in the page.
     *
     * @access public
     *
     * @param int $tabId Id of the row to be processed.
     * @param int $page  Id of the page if selected with the paginator.
     *
     * @return void
     */
    private function _setActionsList($tabId, $page = 1)
    {
        $commands = array();
        $actions = array();
        $actionPanel = array(
            'width' => '50px'
        );

        $options = array();

        if (count($this->_actionsList) == 0)
            $this->_actionsList = array(
                array('commands' => 'add'),
                array('action_panel' => 'edit', 'delete'),
            );

        foreach ($this->_actionsList as $key => $controls)
        {
            $actionOpt = array();
            foreach ($controls as $key => $action)
            {
                $valAction = $action;
                //Redirect to the real action to process If no actionKey = list page.
                if (is_array($action))
                {
                    $valAction = key($action);
                     $actionOpt = $action[$valAction];
                }
                switch ($valAction)
                {
                    case 'add':
//                        $lang = $this->_getParam('lang');
//                        if (!empty ($lang))
//                            $langId = Cible_FunctionsGeneral::getLanguageID($lang);
//                        if ($langId == $this->_defaultEditLanguage)
//                        {
                            if (empty($actionOpt))
                                $actionsOpt = array(
                                    'controller' => $this->_name,
                                    'action' => $this->_currentAction,
                                    'actionKey' => $valAction);

                            $commands = array(
                                $this->view->link($this->view->url($actionOpt),
                                $this->view->getCibleText(
                                    'button_add_' . $this->_labelSuffix),
                                    array('class' => 'action_submit add'))
                            );
//                        }
                        break;

                    case 'edit':
                        if (empty($actionOpt))
                            $actionOpt = array(
                                'label' => $this->view->getCibleText('button_edit'),
                                'url' => $this->view->baseUrl() . "/"
                                    . $this->_moduleTitle . "/"
                                    . $this->_name . "/"
                                    . $this->_currentAction . "/"
                                    . "actionKey/edit/"
                                    . $this->_ID . "/%ID%/page/" . $page,
                                'findReplace' => array(
                                    array(
                                        'search' => '%ID%',
                                        'replace' => $tabId
                                    )
                                ),
                                'returnUrl' => $this->view->Url() . "/"
                            );
                        $edit = $actionOpt;
                        $actions['edit'] = $edit;
                        break;

                    case 'delete':
                        if (empty($actionOpt))
                        $actionOpt = array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url'   => $this->view->baseUrl() . "/"
                                . $this->_moduleTitle . "/"
                                . $this->_name . "/"
                                . $this->_currentAction . "/"
                                . "actionKey/delete/"
                                . $this->_ID . "/%ID%/page/" . $page,
                            'findReplace' => array(
                                array(
                                    'search' => '%ID%',
                                    'replace' => $tabId
                                )
                            )
                        );
                        $delete = $actionOpt;
                        $actions['delete'] = $delete;
                        break;

                    default:

                        break;
                }
            }
        }
        $actionPanel['actions'] = $actions;

        $options = array(
            'commands'     => $commands,
            'action_panel' => $actionPanel
        );
        if ($this->_disableExportToExcel)
            $options['disable-export-to-excel']= 'true';
        if ($this->_disableExportToPDF)
            $options['disable-export-to-pdf']= 'true';
        if ($this->_disableExportToCSV)
            $options['disable-export-to-csv']= 'true';
        if ($this->_enablePrint)
            $options['enable-print']= 'true';

        $options['actionKey'] = $this->_currentAction;

        return $options;
    }

    /**
     * Transforms data of the posted form in one array
     *
     * @param array $formData Data to save.
     *
     * @return array
     */
    protected function _mergeFormData($formData)
    {
        (array)$tmpArray = array();

            $formData = $this->_rebuildData($formData);

        foreach($formData as $key => $data)
        {
            if(is_array($data))
            {
                $tmpArray = array_merge($tmpArray,$data);
            }
            else
                $tmpArray[$key] = $data;
        }

        return $tmpArray;
    }

    private function _rebuildData($data = null)
    {
        $build = array();
//        $cleaned = urldecode($data);
//        $data = explode('&',$cleaned);
//        if (!empty($cleaned))
//        {
            foreach ($data as $key => $tmp)
            {
                $key = urldecode($key);
                $tmp = urldecode($tmp);
//                $tmp = explode('=', $tmp);
                $group = preg_match('/^parentForm/', $key);
                $tmp = str_replace('+', ' ', $tmp);
                if ($group)
                {
                    $tmpVal = explode('[', $key);
                    $arrayName = $tmpVal[0];
                    $newKey = str_replace(']', '', $tmpVal[1]);
                    $build[$arrayName][$newKey] = $tmp;

                }
                else
                    $build[$key] = $tmp;

                $group = false;
            }
//        }

        return $build;
    }

    /**
     * Add some data from other table, tests the joinTables
     * property. If not empty add tables and join clauses.
     *
     * @param Zend_Db_Table_Select $select
     * @param array $params
     *
     * @return Zend_Db_Table_Select
     */
    private function _addJoinQuery($select, array $params = array())
    {
        if (isset($params['joinTables']) && count($params['joinTables']))
            $this->_joinTables = $params['joinTables'];

        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        if(count($this->_joinTables) > 0)
        {
            // Get the constraint attribute = foreign key to link tables.
            $constraint = $params['constraint'];
            // Loop on tables list(given by object class) to build the query
            foreach($this->_joinTables as $key => $object)
            {
                //Create an object and fetch data from object.
                $tmpObject      = new $object();
                $tmpDataTable   = $tmpObject->getDataTableName();
                $tmpIndexTable  = $tmpObject->getIndexTableName();
                $tmpColumnData  = $tmpObject->getDataColumns();
                $tmpColumnIndex = $tmpObject->getIndexColumns();
                //Add data to tables list
                $tables[$tmpDataTable]  = $tmpColumnData;
                $tables[$tmpIndexTable] = $tmpColumnIndex;
                //Get the primary key of the first data object to join table
                $tmpDataId  = $tmpObject->getDataId();
                // If it's the first loop, join first table to the current table
                if ($key == 0)
                {
                    $select->joinLeft($tmpDataTable, $tmpDataId . ' = ' . $constraint);
                    //If there's an index table then it too and filter according language
                    if (!empty($tmpIndexTable))
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                                $tmpIndexTable,
                                $tmpDataId . ' = ' . $tmpIndexId);
                        $select->where(
                                $tmpObject->getIndexLanguageId() . ' = ?',
                                $this->_defaultEditLanguage);
                    }
                    /* If there's more than one table to link, store the current
                     * table name for the next loop
                     */
                    if (count($this->_joinTables) > 1)
                        $prevConstraint = $tmpObject->getForeignKey();;
                }
                elseif ($key > 0)
                {
                    // We have an other table to join to previous.
                    $tmpDataId = $tmpObject->getDataId();
                    if (!empty($this->_constraint))
                        $prevConstraint = $this->_constraint;
                    $select->joinLeft(
                            $tmpDataTable,
                            $prevConstraint . ' = ' . $tmpDataId);
                    if (!empty($tmpIndexTable))
                    {

                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                                $tmpIndexTable,
                                $tmpDataId . ' = ' . $tmpIndexId);
                        $select->where(
                                $tmpObject->getIndexLanguageId() . ' = ?',
                                $this->_defaultEditLanguage);
                    }
                }
            }
        }

        return $select;
    }

    public function  getResourceId()
    {
        return $this->_moduleTitle;
    }

    public function ajaxAddAction()
    {

    }
}