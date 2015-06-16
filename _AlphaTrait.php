<?php
/**
 * MIT licence
 * Version 1.0.1
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
     * @var string - name of the model attribute the alpha pager works with.
     * This must be set.
     */
    public $alphaAttribute;

    /**
     * @var bool|string - how to handle attribute values starting with a digit
     * - false      no special handling; digits are a kind of symbol (default)
     * - 'full'     separate pages for each digit
     * - 'compact'  one page for all digits
     */
    public $alphaDigits = false;

    /**
     * @var array
     * Settings to modify operation. For normal use, this can remain an empty array.
     *
     * Keys are page values ('A' through 'Z', 'all', 'digits', 'symbol', or any values in AlphaPager's [[preButtons]]
     *      or [[postButtons]]). They should be non-numeric.
     * If a page is not set, the corresponding pattern is equal to the page. So, in default situation, most pages are
     *      equal to their pattern.
     *
     * Values are:
     * - false      the page is disregarded, the button is not rendered
     * - array with the following keys:
     * --- label    the text appearing on the button; if unset, label is equal to page (optional)
     * --- pattern  (optional)
     *
     * pattern can have one of the following values:
     * - false      all models are selected (there is no alpha selection)
     * - string     page will select models of which [[alphaAttribute]] starts with <pattern>
     * - array      page will select models of which the start of [[alphaAttribute]] matches with the regular expression
     *                  in <pattern>[0]. The expression is interpreted by the database. Note that the start-of-string token ('^')
     *                  is excluded from the expression.
     *
     * Example:
     *       $alphaPages = [
     *           'P' => [
     *                  'label' => 'PQ',                // label button 'P' with 'PQ'
     *                  'pattern' => [ '[PpQq]' ],      // regular expression: include words starting with 'Q' under 'P'
     *              ],
     *           'Q' => false,                          // suppress page 'Q'
     *           'Z' => [
     *                  'label' => 'X-Z',               // label button 'Z' with 'X-Z'
     *                  'pattern' => [ '[X-Zx-z]' ],    // regular expression: include words starting with 'X' or 'Y' under 'Z'
     *              ],
     *           'X' => false,                          // suppress page 'X'
     *           'Y' => false,                          // suppress page 'Y'
     *       ];
     *
     */
    public $alphaPages = [];

    /**
     * @var string
     * Default page value
     */
    public $alphaDefault = 'A';

    /**
     * @var string
     * Name of the alpha pagination parameter. Not much reason to change this (unless you have a conflict
     *      with another widget).
     */
    public $alphaParam = 'alpha';

    protected $_page;
    protected $_patterns;

    protected function initTrait()  {
        if (! $this->alphaAttribute) {
            throw new InvalidConfigException('AlphaPagination::alphaAttribute must be set.');
        }
        $this->_patterns = array_merge([        // note that user can override these defaults
            'all' => [
                'pattern' => false              // no alpha selection, do not modify query
            ],
            'digits' => [
                'label' => '0-9',
                'pattern' => [ '[0-9]' ]        // regular expression: any digit
            ],
            'symbol' => [
                'label' => '#',
                'pattern' => $this->alphaDigits ? [ '[^[:alnum:]]' ]   // any not alphanumeric character
                        : [ '[^[:alpha:]]' ]   // any not alphabetic character (including digits)
            ]
        ], $this->alphaPages);
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
            if (isset($this->_patterns[$page]))    {
                $p = $this->_patterns[$page];
                return isset($p['pattern']) ? $p['pattern'] : $page;
            }
            else    {
                return $page;
            }
        }
        return null;
    }

    public function getAlphaLabel($page, $bToLower = false) {
        if (isset($this->_patterns[$page]))    {
            $p = $this->_patterns[$page];
            if ($p === false) return $p;
            return isset($p['label']) ? $p['label'] : $page;
        }
        else    {
            return $bToLower ? strtolower($page) : $page;
        }
    }

    public function createUrl($page)    {
        /* @var $this BaseDataProvider */
        $request = Yii::$app->getRequest();
        $params = $request instanceof Request ? $request->getQueryParams() : [];

        // don't copy query parameter from 'normal' pagination
        $suppress = $this->id ? $this->id . '-page' : 'page';
        if (isset($params[$suppress])) unset($params[$suppress]);

        $params[0] = Yii::$app->controller->getRoute();
        $params[$this->alphaParam] = $page;
        $urlManager = Yii::$app->getUrlManager();
        return $urlManager->createUrl($params);
    }
}
