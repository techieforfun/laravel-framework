<?php

namespace Illuminate\Queue\Connectors;

use Aws\Sqs\SqsClient;
use Illuminate\Queue\SqsFifoQueue;
use Illuminate\Support\Arr;

class SqsFifoConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = $this->getDefaultConfiguration($config);

        if (! empty($config['key']) && ! empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return new SqsFifoQueue(
            new SqsClient(
                Arr::except($config, ['token'])
            ),
            $config['queue'],
            $config['prefix'] ?? '',
            $config['suffix'] ?? '',
            $config['after_commit'] ?? null,
            $config['message_group_id'] ?? null,
            $config['message_deduplication_id'] ?? null,
            $config['allow_delay'] ?? false
        );
    }

    /**
     * Get the default configuration for SQS.
     *
     * @param  array  $config
     * @return array
     */
    protected function getDefaultConfiguration(array $config)
    {
        return array_merge([
            'version' => 'latest',
            'http' => [
                'timeout' => 60,
                'connect_timeout' => 60,
            ],
        ], $config);
    }
}
