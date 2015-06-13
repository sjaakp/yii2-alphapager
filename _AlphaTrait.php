<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 13-06-2015.
 *
 * Alphabetic paging for Yii 2.0
 */

namespace sjaakp\alphapager;

use Yii;
use yii\web\Request;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;

trait _AlphaTrait {
    /**
     * @var string - name of the attribute the alpha pager works with.
     * This must be set.
     */
    public $alphaAttribute;

    /**
     * @var array patterns linked to pages
     * Keys are page values.
     * Values are patterns defining the select condition of the page.
     * - false      all models are selected (there is no alpha selection)
     * - string     page will select models of which [[attribute]] starts with string
     * - array      page will select models of which the start of attribute complies with the regular expression
     *                  in array[0]. Note that the start-of-string token ('^') is excluded from the expression.
     * If page is not in this array, pattern is equal to page.
     *
     * Example:
     *
     *       $patterns = [
     *           'P' => [ '[PpQq]' ],      // regular expression: include words starting with 'Q' under 'P'
     *           'Z' => [ '[X-Zx-z]' ],    // regular expression: include words starting with 'X' or 'Y' under 'Z'
     *       ];
     */
    public $alphaPatterns = [];

    /**
     * @var array of $page => $label links
     * If page is not set, label will be equal to page
     * If $label is false, the button will not be rendered.
     *
     * Example:
     *       $labels = [
     *           'P' => 'PQ',     // label button 'P' with 'PQ'
     *           'Q' => false,    // suppress button 'Q'
     *           'Z' => 'X-Z',    // label button 'Z' with 'X-Z'
     *           'X' => false,    // suppress button 'X'
     *           'Y' => false,    // suppress button 'Y'
     *       ];
     *
     */
    public $alphaLabels = [];

    /**
     * @var string
     * Name of the alpha pagination parameter. Not much reason to change this.
     */
    public $alphaParam = 'alpha';

    /**
     * @var string
     * Default page value
     */
    public $alphaDefault = 'A';

    protected $_page;
    protected $_patterns;

    protected function initTrait()  {
        if (! $this->alphaAttribute) {
            throw new InvalidConfigException('AlphaPagination::attribute must be set.');
        }
        $this->_patterns = array_merge([
            'all' => false,           // do not modify query
            '#' => [ '[^A-Za-z]' ],   // regular expression: any pattern not starting with an alphabetic character
        ], $this->alphaPatterns);
    }

    /**
     * @return string - the current page
     */
    protected function getPage()
    {
        /* @var $this BaseDataProvider */
        if ($this->_page === null) {
            $name = $this->id ? $this->id . '-' . $this->alphaParam : $this->alphaParam;
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];
            $page = isset($params[$name]) && is_scalar($params[$name]) ? $params[$name] : $this->alphaDefault;
            $this->_page = $page;
        }
        return $this->_page;
    }

    protected function getPattern()    {
        if (($page = $this->getPage()) !== null) {
            return isset($this->_patterns[$page]) ? $this->_patterns[$page] : $page;
        }
        return null;
    }

    public function createUrl($page)    {
        /* @var $this BaseDataProvider */
        $request = Yii::$app->getRequest();
        $params = $request instanceof Request ? $request->getQueryParams() : [];
        $suppress = $this->id ? $this->id . '-page' : 'page';
        if (isset($params[$suppress])) unset($params[$suppress]);
        $params[0] = Yii::$app->controller->getRoute();
        $params[$this->alphaParam] = $page;
        $urlManager = Yii::$app->getUrlManager();
        return $urlManager->createUrl($params);
    }
}