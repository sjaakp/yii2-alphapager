Yii2 Alphapager
============

**Yii2 Alphapager** performs alphabetic paging with for instance `GridView`s or `ListView`s. 

Just use `sjaakp\alphapager\ActiveDataProvider` in stead of your normal `yii\dataActiveDataProvider`, and put an `AlphaPager` widget in front of your `GridView`, and you're running.

Likewise with `sjaakp\alphapager\ArrayDataProvider`.

Alpha paging is compatible with normal pagination. You can use them together.

A demonstration of **Yii2 Aphapager** is [here](http://www.sjaakpriester.nl/software/alphapager).

#### Important notice ####

**Yii2 Alphapager's ActiveDataProvider is only proven to work with MySQL databases. It should also work with SQLite and Cubrid, but this is not tested.**

In contrast to the Yii philosophy, **Yii2	Alphapager** is not transparent to the database, as I'm sorry to say. Presumably, `ActiveDataProvider` can be made to work with Oracle, Postgres, and MSSQL as well. See the comments near `$regex` in `ActiveDataProvider.php` for details. If you can provide more information on using **Yii2 Alphapager** with other databases, I'd be glad to hear it. 

## Installation ##

The preferred way to install **Yii2 Alphapager** is through [Composer](https://getcomposer.org/). Either add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-alphapager": "*"` 

Or run:

`$ php composer.phar require sjaakp/yii2-alphapager "*"` 

You can manually install **Yii2 Alphapager** by [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-alphapager/archive/master.zip).

## Usage ##

Using **Yii2 Alphapager** is easy. A minimum usage scenario would look like the following. In `PersonController.php` we would have something like:

    <?php
	use sjaakp\alphapager\ActiveDataProvider;

	class PersonController extends Controller
	{
		// ...

		public function actionIndex()    {
	        $dataProvider = new ActiveDataProvider([
	            'query' => Person::find()->orderBy('last_name, first_name'),
	            'alphaAttribute' => 'last_name'
	        ]);
	
	        return $this->render('index', [
	            'dataProvider' => $dataProvider
	        ]);
	    }

		// ... more actions ...
	}

The corresponding view file `index.php` could look something like:

    <?php
	use sjaakp\alphapager\AlphaPager;
	?>

    <?= AlphaPager::widget([
        'dataProvider' => $dataProvider
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'last_name',
            'first_name',
        ],
    ]); ?>

## Classes ##

### ActiveDataProvider and ActiveArrayProvider ###

These are extensions from their Yii-counterparts in `yii\data`, and can be used in the same way. It is important to set attribute `$alphaAttribute`.

#### $alphaAttribute ####

Set this to the name of the attribute which is used to define the pages. Must be set.

#### $alphaDigits ####

Setting for the way attribute values starting with a digit are handled. Can have the following values:

* `false`: no special handling; digits are treated just like any other non-alphabetic symbol (default)
* `'full'`: separate pages for each digit
* `'compact'`: one page for all digits
* `array` of characters: alpha pager doesn't display alphabetic characters, but uses the characters in this array; useful for non-western alphabets


#### $alphaPages ####

Settings to modify alpha pagers operation. For normal use, this can remain the default value of `[]` (empty array). For more information, see the `_AlphaTrait.php` source.

#### $alphaDefault ####

Default page value; this page opens when no explicit page is given. Default value: `'A'`.


----------

### AlphaPager ###

This is the widget that renders the actual alphapager. The attribute `$dataProvider` must be set.

#### $dataProvider ####

The **Yii2 Alphapager** `ActiveDataProvider` or `ArrayDataProvider` that this pager is associated with. Must be set.

#### $preButtons ####

`array` Page values of buttons which should appear left of the alphabetical buttons. Set this to `[]` if you don't want an '*all*' button. Default: `[ 'all' ]`.

#### $postButtons ####

`array` Page values of buttons which should appear right of the alphabetical buttons. Set this to `[]` if you don't want an '*#*' (non-alphabetic) button. Default: `[ 'symbol' ]`.

#### $lowerCase ####

`boolean` Whether the alphabetic buttons are rendered in lower case. Default: `false`. 

