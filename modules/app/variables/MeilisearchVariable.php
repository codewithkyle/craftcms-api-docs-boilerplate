<?php

namespace modules\app\variables;

use Craft;
use Meilisearch\Client;

class MeilisearchVariable
{
    public function search(string $query): array
    {
        try
        {
            $client = new Client(getenv('MEILISEARCH_HOST'), getenv('MEILISEARCH_MASTER_KEY'));
            $index = $client->index('pages');
            $hits = $index->search($query, ['limit' => 5])->getHits();
            return $hits;
        }
        catch (\Exception $e)
        {
            Craft::error($e->getMessage(), __METHOD__);
            return [];
        }
    }
}
