<?php

namespace barrelstrength\sproutlists\services;

use barrelstrength\sproutbase\contracts\sproutlists\BaseListType;
use barrelstrength\sproutlists\events\RegisterListTypesEvent;
use barrelstrength\sproutlists\records\Subscription;
use craft\base\Component;
use barrelstrength\sproutlists\records\Lists as ListsRecord;
use yii\base\Exception;
use Craft;

class Lists extends Component
{
    /**
     * @event RegisterListTypesEvent
     */
    const EVENT_REGISTER_LIST_TYPES = 'registerSproutListsListTypes';

    /**
     * Registered List Types
     *
     * @var array
     */
    protected $listTypes = [];

    /**
     * Gets all registered list types.
     *
     * @return array
     */
    public function getAllListTypes()
    {
        $event = new RegisterListTypesEvent([
            'listTypes' => []
        ]);

        $this->trigger(self::EVENT_REGISTER_LIST_TYPES, $event);

        $listTypes = $event->listTypes;

        if (!empty($listTypes)) {
            foreach ($listTypes as $listTypeClass) {
                /**
                 * @var $listType BaseListType
                 */
                $listType = new $listTypeClass;

                $this->listTypes[$listTypeClass] = $listType;
            }
        }

        return $this->listTypes;
    }

    /**
     * Returns a new List Type Class for the given List Type
     *
     * @param $className
     *
     * @return mixed
     * @throws \Exception
     */
    public function getListType($className)
    {
        $listTypes = $this->getAllListTypes();

        if (!isset($listTypes[$className])) {
            throw new Exception('Invalid List Type.');
        }

        return new $listTypes[$className];
    }

    /**
     * @param $listHandle
     *
     * @return mixed
     * @throws Exception
     */
    public function getListTypeByHandle($listHandle)
    {
        $list = ListsRecord::find()->where([
            'handle' => $listHandle
        ])->one();

        if ($list === null)
        {
            throw new Exception(Craft::t('sprout-lists','No list could be found with the handle `{listHandle}"', [
                'listHandle' => $listHandle
            ]));
        }

        return new $list->type;
    }

    /**
     * Deletes a list.
     *
     * @param $listId
     *
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteList($listId)
    {
        $listRecord = ListsRecord::findOne($listId);

        if ($listRecord == null) {
            return false;
        }

        if ($listRecord AND $listRecord->delete()) {
            $subscriptions = Subscription::find()->where([
                'listId' => $listId
            ]);

            if ($subscriptions != null) {
                Subscription::deleteAll('listId = :listId', [
                    ':listId' => $listId
                ]);
            }

            return true;
        }

        return false;
    }
}