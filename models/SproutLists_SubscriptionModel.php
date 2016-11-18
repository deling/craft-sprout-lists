<?php
namespace Craft;

class SproutLists_SubscriptionModel extends BaseModel
{
	public function defineAttributes()
	{
		return array(
			'elementId' => AttributeType::Number,
			'type'      => AttributeType::String,
			'email'     => AttributeType::String,
			'list'      => AttributeType::Mixed
		);
	}
}