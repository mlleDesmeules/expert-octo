<?php

namespace app\models\tag;

use app\helpers\ArrayHelperEx;
use app\models\app\Lang;
use Yii;

/**
 * This is the model class for table "tag_lang".
 *
 * @property int     $tag_id
 * @property int     $lang_id
 * @property string  $name
 * @property string  $slug
 *
 * Relations :
 * @property Lang    $lang
 * @property TagBase $tag
 */
abstract class TagLangBase extends \yii\db\ActiveRecord
{
	const ERROR   = 0;
	const SUCCESS = 1;

	const ERR_ON_SAVE            = "ERR_ON_SAVE";
	const ERR_ON_DELETE          = "ERR_ON_DELETE";
	const ERR_NOT_FOUND          = "ERR_NOT_FOUND";
	const ERR_TAG_NOT_FOUND      = "ERR_TAG_NOT_FOUND";
	const ERR_LANG_NOT_FOUND     = "ERR_LANG_NOT_FOUND";
	const ERR_TRANSLATION_EXISTS = "ERR_TRANSLATION_ALREADY_EXISTS";

	const ERR_FIELD_REQUIRED   = "ERR_FIELD_VALUE_REQUIRED";
	const ERR_FIELD_TYPE       = "ERR_FIELD_VALUE_WRONG_TYPE";
	const ERR_FIELD_TOO_LONG   = "ERR_FIELD_VALUE_TOO_LONG";
	const ERR_FIELD_NOT_FOUND  = "ERR_FIELD_VALUE_NOT_FOUND";
	const ERR_FIELD_NOT_UNIQUE = "ERR_FIELD_VALUE_NOT_UNIQUE";

	/** @inheritdoc */
	public static function tableName () { return 'tag_lang'; }

	/** @inheritdoc */
	public function rules ()
	{
		return [
			[ "tag_id", "required", "message" => self::ERR_FIELD_REQUIRED ],
			[ "tag_id", "integer",  "message" => self::ERR_FIELD_TYPE ],
			[
				[ 'tag_id' ], 'exist',
				'skipOnError'     => true,
				'targetClass'     => Tag::className(),
				'targetAttribute' => [ 'tag_id' => 'id' ],
				"message"         => self::ERR_FIELD_NOT_FOUND,
			],

			[ "lang_id", "required", "message" => self::ERR_FIELD_REQUIRED ],
			[ "lang_id", "integer",  "message" => self::ERR_FIELD_TYPE ],
			[
				[ 'lang_id' ], 'exist',
				'skipOnError'     => true,
				'targetClass'     => Lang::className(),
				'targetAttribute' => [ 'lang_id' => 'id' ],
				"message"         => self::ERR_FIELD_NOT_FOUND,
			],

			[ [ 'tag_id', 'lang_id' ], 'unique', 'targetAttribute' => [ 'tag_id', 'lang_id' ], "message" => self::ERR_FIELD_NOT_UNIQUE ],

			[ "name", "required", "message" => self::ERR_FIELD_REQUIRED ],
			[ "name", "string", "max" => 255, "tooLong" => self::ERR_FIELD_TOO_LONG ],
			[
				"name", "unique",
				"targetAttribute" => [ "name", "lang_id" ],
				"message"         => self::ERR_FIELD_NOT_UNIQUE,
				"when"            => function ( self $model, string $attribute ) {
					$found = self::find()->byLang($model->lang_id)->where([ $attribute => $model->$attribute ])->one();

					return ($found) ? ($model->tag_id !== $found->tag_id) : true;
				},
			],

			[ "slug", "required", "message" => self::ERR_FIELD_REQUIRED ],
			[ "slug", "string", "max" => 255, "tooLong" => self::ERR_FIELD_TOO_LONG ],
			[
				"slug", "unique",
				"targetAttribute" => [ "slug", "lang_id" ],
				"message"         => self::ERR_FIELD_NOT_UNIQUE,
				"when"            => function ( self $model, string $attribute ) {
					$found = self::find()->byLang($model->lang_id)->where([ $attribute => $model->$attribute ])->one();

					return ($found) ? ($model->tag_id !== $found->tag_id) : true;
				},
			],
		];
	}

	/** @inheritdoc */
	public function attributeLabels ()
	{
		return [
			'tag_id'  => Yii::t('app.tag', 'Tag ID'),
			'lang_id' => Yii::t('app.tag', 'Lang ID'),
			'name'    => Yii::t('app.tag', 'Name'),
			'slug'    => Yii::t('app.tag', 'Slug'),
		];
	}

	/** @return \yii\db\ActiveQuery */
	public function getLang ()
	{
		return $this->hasOne(Lang::className(), [ 'id' => 'lang_id' ]);
	}

	/** @return \yii\db\ActiveQuery */
	public function getTag ()
	{
		return $this->hasOne(TagBase::className(), [ 'id' => 'tag_id' ]);
	}

	/**
	 * @inheritdoc
	 * @return TagLangQuery the active query used by this AR class.
	 */
	public static function find ()
	{
		return new TagLangQuery(get_called_class());
	}

	/**
	 * @param integer $tagId
	 * @param integer $langId
	 *
	 * @return bool
	 */
	public static function translationExists ( $tagId, $langId )
	{
		return self::find()->byTag($tagId)->byLang($langId)->exists();
	}

	/**
	 * Build an array to use when returning from another method. The status will automatically
	 * set to ERROR, then $error passed in param will be associated to the error key.
	 *
	 * @param $error
	 *
	 * @return array
	 */
	public static function buildError ( $error )
	{
		return [ "status" => self::ERROR, "error" => $error ];
	}

	/**
	 * Build an array to use when returning from another method. The status will be automatically
	 * set to SUCCESS, then the $params will be merged with the array and be returned.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public static function buildSuccess ( $params )
	{
		return ArrayHelperEx::merge([ "status" => self::SUCCESS ], $params);
	}
}
