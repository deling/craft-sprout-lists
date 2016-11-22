<?php
namespace Craft;

class SproutLists_ListsElementsRelationsRecord extends BaseRecord
{
	/**
	 * @return string
	 */
	public function getTableName()
	{
		return 'sproutlists_lists_recpients_elements';
	}

	/**
	 * @return array
	 */
	public function defineAttributes()
	{
		return array(
			'type'              => AttributeType::String,
			'listRecipientId'   => AttributeType::Number
		);
	}

	public function defineRelations()
	{
		return array(
			'element' => array(
				static::BELONGS_TO,
				'ElementRecord',
				'elementId',
				'required' => true,
				'onDelete' => static::CASCADE,
			)
		);
	}

	public function defineIndexes()
	{
		return array(
			array('columns' => array('elementId'))
		);
	}
}
