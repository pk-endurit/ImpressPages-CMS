<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\standard\content_management;


if (!defined('CMS')) exit;

require_once (__DIR__.'/site_db.php');

/**
 * Website zone element. Typically each element represents one page on zone.<br />
 *
 * @package ImpressPages
 */

class Element extends \Frontend\Element {
    protected $linkIgnoreRedirect;

    /**
     * @var string
     */
    protected $controllerAction;
    
    public function hydrate($dbElement)
    {
        $this->setButtonTitle($dbElement['button_title']);
        $this->setPageTitle($dbElement['page_title']);
        $this->setKeywords($dbElement['keywords']);
        $this->setDescription($dbElement['description']);
        $this->setUrl($dbElement['url']);
        $this->setText($dbElement['cached_text']);
        $this->setLastModified($dbElement['last_modified']);
        $this->setCreatedOn($dbElement['created_on']);
        $this->setModifyFrequency($dbElement['modify_frequency']);
        $this->setRss($dbElement['rss']);
        $this->setVisible($dbElement['visible']);
        $this->setHtml($dbElement['html']);
        $this->setType($dbElement['type']);
        $this->setRedirectUrl($dbElement['redirect_url']);
        $this->controllerAction = $dbElement['controllerAction'];
    }

    public function makeActions()
    {
        if ($this->controllerAction) {
            $site = \Ip\ServiceLocator::getSite();
            $site->setBlockContent('main', $this->generateContent());
        }
    }

    public function getControllerAction()
    {
        return $this->controllerAction;
    }

    public function getLink($ignoreRedirect = false) {
        global $site;
        if ($site->managementState()) {
            $ignoreRedirect = true;
        }
        
        
        if($this->link == null || $this->linkIgnoreRedirect == null) {
            $this->generateDepthAndLink();
        }

        if($ignoreRedirect)
        {
            return $this->linkIgnoreRedirect;
        }
        else
        {
            return $this->link;
        }
    }



    public function getDepth() {
        if($this->depth == null)
        $this->generateDepthAndLink();

        return $this->depth;
    }

    private function generateDepthAndLink() {
        global $site;
        $tmpUrlVars = array();
        $tmpId = $this->getId();
        $element = DbFrontend::getElement($tmpId);
        while($element['parent'] !== null) {
            $tmpUrlVars[] = $element['url'];
            $element = DbFrontend::getElement($element['parent']);
        }
        $languageId = DbFrontend::languageByRootElement($element['id']);

        $urlVars = array();

        for($i=sizeof($tmpUrlVars)-1; $i >= 0; $i--) // " - 1: eliminating invisible root content element"
        {
            $urlVars[] = $tmpUrlVars[$i];
        }

        $this->depth = sizeof($urlVars);

        switch($this->type) {
            case 'subpage':
                $tmpChildren = $site->getZone($this->zoneName)->getElements($languageId, $this->id, 0, $limit = 1);
                if(sizeof($tmpChildren) == 1)
                $this->link = $tmpChildren[0]->getLink();
                else
                $this->link = $site->generateUrl($languageId, $this->zoneName, $urlVars);  //open current page if no subpages exist
                break;
            case 'redirect':
                if($site->managementState()) {
                    if(strpos($this->redirectUrl, BASE_URL) === 0) {
                        if(strpos($this->redirectUrl, 'cms_action=manage') === false) {
                            if(strpos($this->redirectUrl, '?') === false) {
                                $this->redirectUrl .= '?cms_action=manage';
                            } else {
                                $this->redirectUrl .= '&cms_action=manage';
                            }
                        }
                    }
                }
                $this->link = $this->redirectUrl;
                break;
            case 'inactive':
            case 'default':
            default:
                $this->link = $site->generateUrl($languageId, $this->zoneName, $urlVars);
                break;
        }

        $this->linkIgnoreRedirect = $site->generateUrl($languageId, $this->zoneName, $urlVars);
    }

    public function generateContent()
    {
        if ($this->controllerAction) {
            $site = \Ip\ServiceLocator::getSite();
            $controllerInfo = $site->_parseControllerAction($this->controllerAction, 'SiteController');

            if (!class_exists($controllerInfo['controller'])) {
                throw new \Ip\CoreException('Requested controller doesn\'t exist');
            }

            $controller = new $controllerInfo['controller']();

            return $controller->$controllerInfo['action']();
        } else {
            return parent::generateContent();
        }
    }

}




