<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 13-06-2015.
 *
 * Alphabetic paging for Yii 2.0
 */

namespace sjaakp\alphapager;

use yii\data\ArrayDataProvider as YiiArrayDataProvider;
use Yii;

class ArrayDataProvider extends YiiArrayDataProvider {
    use _AlphaTrait;

    public function init()  {
        $this->initTrait();
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()    {
        $pattern = $this->getPattern();

        if ($pattern == false) {
            return parent::prepareModels();
        }
        $origAll = $this->allModels;
        $attribute = $this->alphaAttribute;

        $this->allModels = array_filter($this->allModels, function ($v) use ($attribute, $pattern) {
            $attrVal = $v->{$attribute};
            if (is_array($pattern)) {
                $pattern = '/^' . current($pattern) . '/';
                return preg_match($pattern, $attrVal) === 1;
            } else {
                return stripos($attrVal, $pattern) === 0;
            }
        });
        $r = parent::prepareModels();
        $this->allModels = $origAll;
        return $r;
    }
}