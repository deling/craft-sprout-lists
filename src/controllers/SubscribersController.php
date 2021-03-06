<?php

namespace barrelstrength\sproutlists\controllers;

use barrelstrength\sproutbase\contracts\sproutlists\BaseListType;
use barrelstrength\sproutlists\elements\Subscribers;
use barrelstrength\sproutlists\integrations\sproutlists\SubscriberListType;
use barrelstrength\sproutlists\SproutLists;
use craft\web\Controller;
use Craft;

class SubscribersController extends Controller
{
    /**
     * Prepare variables for Subscriber Edit Template
     *
     * @param null $id
     * @param null $subscriber
     *
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionEditSubscriberTemplate($id = null, $subscriber = null)
    {
        $listType = SproutLists::$app->lists->getListType(SubscriberListType::class);
        $listTypes[] = $listType;

        if ($id != null AND $subscriber == null) {
            $subscriber = $listType->getSubscriberById($id);
        }

        return $this->renderTemplate('sprout-lists/subscribers/_edit', [
            'subscriber' => $subscriber,
            'listTypes' => $listTypes
        ]);
    }

    /**
     *  Saves a subscriber
     *
     * @throws \Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveSubscriber()
    {
        $this->requirePostRequest();

        $subscriberId = Craft::$app->getRequest()->getBodyParam('subscriberId');

        $subscriber = new Subscribers();

        if ($subscriberId != null) {
            $subscriber = Craft::$app->getElements()->getElementById($subscriberId);
        }

        $subscriber->email = Craft::$app->getRequest()->getBodyParam('email');
        $subscriber->firstName = Craft::$app->getRequest()->getBodyParam('firstName');
        $subscriber->lastName = Craft::$app->getRequest()->getBodyParam('lastName');
        $subscriber->subscriberLists = Craft::$app->getRequest()->getBodyParam('sproutlists.subscriberLists');

        $type = Craft::$app->getRequest()->getBodyParam('type');

        /**
         * @var $listType BaseListType
         */
        $listType = SproutLists::$app->lists->getListType($type);

        $session = Craft::$app->getSession();

        if ($session AND $listType->saveSubscriber($subscriber)) {
            $session->setNotice(Craft::t('sprout-lists', 'Subscriber saved.'));

            return $this->redirectToPostedUrl($subscriber);
        }

        $session->setError(Craft::t('sprout-lists', 'Unable to save subscriber.'));

        return Craft::$app->getUrlManager()->setRouteParams([
            'subscriber' => $subscriber
        ]);
    }

    /**
     * Deletes a subscriber
     *
     * @return null|\yii\web\Response
     * @throws \Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDeleteSubscriber()
    {
        $this->requirePostRequest();

        $subscriberId = Craft::$app->getRequest()->getBodyParam('subscriberId');

        $listTypeParam = Craft::$app->getRequest()->getBodyParam('type');

        $listType = SproutLists::$app->lists->getListType($listTypeParam);

        $session = Craft::$app->getSession();

        if ($session AND $subscriber = $listType->deleteSubscriberById($subscriberId)) {
            $session->setNotice(Craft::t('sprout-lists', 'Subscriber deleted.'));

            return $this->redirectToPostedUrl($subscriber);
        }

        $session->setError(Craft::t('sprout-lists', 'Unable to delete subscriber.'));

        Craft::$app->getUrlManager()->setRouteParams([
            'subscriber' => $subscriber
        ]);

        return null;
    }
}