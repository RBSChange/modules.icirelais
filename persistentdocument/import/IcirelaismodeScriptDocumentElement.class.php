<?php
/**
 * icirelais_IcirelaismodeScriptDocumentElement
 * @package modules.icirelais.persistentdocument.import
 */
class icirelais_IcirelaismodeScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return icirelais_persistentdocument_icirelaismode
     */
    protected function initPersistentDocument()
    {
    	return icirelais_IcirelaismodeService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_icirelais/icirelaismode');
	}
}