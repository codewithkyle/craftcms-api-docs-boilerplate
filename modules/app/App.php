<?php

namespace modules\app;

use Craft;
use yii\base\Module as BaseModule;
use yii\base\Event;
use craft\events\ElementEvent;
use craft\services\Elements;
use Meilisearch\Client;
use craft\helpers\StringHelper;
use modules\app\variables\MeilisearchVariable;
use craft\web\twig\variables\CraftVariable;
use craft\elements\Entry;

/**
 * App module
 *
 * @method static App getInstance()
 */
class App extends BaseModule
{
    public function init(): void
    {
        Craft::setAlias('@modules/app', __DIR__);

        // Set the controllerNamespace based on whether this is a console or web request
        if (Craft::$app->request->isConsoleRequest) {
            $this->controllerNamespace = 'modules\\app\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\app\\controllers';
        }

        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)

        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function (ElementEvent $event)
        {
            if (!empty($event->element->url) && !$event->element->isDraft && !$event->element->isRevision)
            {
                try
                {
                    $entry = $event->element;
                    $navigationEntry = Entry::find()->section('navigation')->document($entry)->one();
                    $groupEntry = $navigationEntry->parent;
                    $blocks = $entry->contentBuilder->all();
                    $documents = [];
                    foreach ($blocks as $block)
                    {
                        switch ($block->type)
                        {
                            case 'heading':
                                $documents[] = [
                                    'id' => $entry->id . "-" . $block->id,
                                    'title' => $block->text,
                                    'url' => $entry->url . "#" . StringHelper::toKebabCase($block->text),
                                    'content' => $block->text,
                                    'nav' => $groupEntry->title . ' / ' . $navigationEntry->title,
                                ];
                                break;
                            case 'endpoint':
                                $endpoint = $block->endpoint->one();
                                $documents[] = [
                                    'id' => $entry->id . "-" . $block->id,
                                    'title' => $endpoint->heading,
                                    'url' => $entry->url . "#" . StringHelper::toKebabCase($endpoint->heading),
                                    'content' => $endpoint->copy,
                                    'endpoint' => $endpoint->title,
                                    'nav' => $groupEntry->title . ' / ' . $navigationEntry->title,
                                ];
                                break;
                            case 'model':
                                $model = $block->model->one();
                                $documents[] = [
                                    'id' => $entry->id . "-" . $block->id,
                                    'title' => $model->heading,
                                    'url' => $entry->url . "#" . StringHelper::toKebabCase($model->heading),
                                    'content' => $model->copy,
                                    'nav' => $groupEntry->title . ' / ' . $navigationEntry->title,
                                ];
                                break;
                            default:
                                break;
                        }
                    }
                    $client = new Client(getenv('MEILISEARCH_HOST'), getenv('MEILISEARCH_MASTER_KEY'));
                    $index = $client->index('pages');
                    $index->updateDocuments($documents);
                }
                catch (\Exception $e)
                {
					Craft::error($e->getMessage(), __METHOD__);
				}
			}
        });

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event)
            {
                $variable = $event->sender;
                $variable->set('meilisearch', MeilisearchVariable::class);
            }
        );
    }
}
