<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 13-06-2015.
 *
 * Alphabetic paging for Yii 2.0
 */

namespace sjaakp\alphapager;

use yii\data\ActiveDataProvider as YiiActiveDataProvider;
use yii\db\QueryInterface;
use Yii;

class ActiveDataProvider extends YiiActiveDataProvider {
    use _AlphaTrait;

    /**
     * @var null|callable
     *
     * Function to prepare the WHERE predicate with a regular expression. The parameters of the function are the
     * attribute and the regular expression parameter.
     *
     * If null, it is set to work with MySQL. Presumably, this also works with SQLite and Cubrid.
     * @link https://dev.mysql.com/doc/refman/5.5/en/regexp.html
     *
     * For Oracle, the function body might be something like: return "REGEXP_LIKE($attribute, \"^$pattern\")";
     * @link http://docs.oracle.com/cd/B19306_01/appdev.102/b14251/adfns_regexp.htm
     * Confirmed by #3.
     *
     * For Postgres, the function body might be something like: return "$attribute ~* \"^$pattern\"";
     * @link http://www.postgresql.org/docs/8.3/static/functions-matching.html#FUNCTIONS-POSIX-REGEXP
     *
     * Maybe, but frankly I'm far from sure, this works with MSSQL: return "PATINDEX('%{$pattern}%', $attribute) = 1";
     * @link https://msdn.microsoft.com/en-us/library/ms188395.aspx
     *
     * Please, notice that THIS IS TESTED WITH MySQL DATABASE ONLY.
     *
     *
     */
    public $regex;

    public function init()  {
        if (! $this->regex) $this->regex = function($attribute, $pattern)   {
            // works well with MySQL, and probably with SQLite and Cubrid
            return "$attribute REGEXP \"^$pattern\"";
        };
        $this->initTrait();
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()    {
        $origQuery = $this->query;
        $this->modifyQuery($this->query);
        $r = parent::prepareModels();
        $this->query = $origQuery;
        return $r;
    }

    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()    {
        $origQuery = $this->query;
        $this->modifyQuery($this->query);
        $r = parent::prepareTotalCount();
        $this->query = $origQuery;
        return $r;
    }

    /**
     * @param $query QueryInterface
     */
    protected function modifyQuery(&$query)  {
        $attribute = $this->alphaAttribute;
        $pattern = $this->getPattern();

        if ($pattern !== false)  {
            if (is_array($pattern)) {
                $pattern = current($pattern);
                $query->andWhere(call_user_func($this->regex, $attribute, $pattern));
            }
            else    {
                $query->andWhere(['like', $attribute, $pattern . '%', false]);
            }
        }
    }
}
