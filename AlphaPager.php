<?php
/**
 * MIT licence
 * Version 1.0.2
 * Sjaak Priester, Amsterdam 13-06-2015.
 *
 * Alphabetic paging for Yii 2.0
 */

namespace sjaakp\alphapager;

use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use Yii;

class AlphaPager extends Widget {

    /**
     * @var ActiveDataProvider|ArrayDataProvider that this pager is associated with.
     * This property must be set.
     */
    public $dataProvider;

    /**
     * @var array - page values of buttons which should appear left of the alphabetical buttons
     * Set this to [] if you don't want an 'all' button.
     */
    public $preButtons = [ 'all' ];

    /**
     * @var array - page values of buttons which should appear right of the alphabetical buttons
     * Set this to [] if you don't want a '#' (non-alphabetic) button.
     */
    public $postButtons = [ 'symbol' ];


//    public $digits = false;

    /**
     * @var boolean - whether the alphabetic button labels are in lower case.
     */
    public $lowerCase = false;

    /**
     * @var array HTML attributes for the pager container tag.
     * Default makes pager look good with Bootstrap.
     */
    public $options = ['class' => 'pagination'];

    /**
     * @var array HTML attributes for the button in a pager container tag.
     */
    public $buttonOptions = [];

    /**
     * @var array HTML attributes for the link in a pager container tag.
     */
    public $linkOptions = [];

    /**
     * @var string the CSS class for the active (currently selected) page button.
     */
    public $activePageCssClass = 'active';

    public function init()  {
        if (! $this->dataProvider) {
            throw new InvalidConfigException('AlphaPager::dataProvider must be set.');
        }
    }

    public function run()   {
        $digits = $this->dataProvider->alphaDigits;

        if ($digits == 'full') {
            $digs = array_merge(range('A', 'Z'), range('0', '9'));
        } elseif ($digits == 'compact') {
            $digs = array_merge(range('A', 'Z'), ['digits']);
        } elseif ($digits) {
            $digs = $digits;
        } else {
            $digs = range('A', 'Z');
        }

        $pages = array_merge($this->preButtons, $digs, $this->postButtons);

        $current = $this->dataProvider->page;
        $pager = $this;

        $buttons = array_map(function($p) use ($pager, $current) {
            $p = (string) $p;
            return $pager->renderPageButton($p, $p === $current);
        }, $pages);

        echo Html::tag('ul', implode("\n", $buttons), $this->options);
    }

    protected function renderPageButton($page, $active)
    {
        $label = $this->dataProvider->getAlphaLabel($page, $this->lowerCase);

        if ($label === false) return '';

        $options = $this->buttonOptions;
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;
        return Html::tag('li', Html::a($label, $this->dataProvider->createUrl($page), $linkOptions), $options);
    }
}
