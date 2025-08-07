<?php

namespace MatusStafura\SystemInfo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;

class Info extends AbstractHelper
{
    protected ProductMetadataInterface $productMetadata;
    protected ResourceConnection $resourceConnection;
    protected State $appState;

    public function __construct(
        Context $context,
        ProductMetadataInterface $productMetadata,
        ResourceConnection $resourceConnection,
        State $appState
    ) {
        parent::__construct($context);
        $this->productMetadata = $productMetadata;
        $this->resourceConnection = $resourceConnection;
        $this->appState = $appState;
    }

    public function getSystemInfo(): string
    {
        $info = [
            'Magento Version' => $this->productMetadata->getVersion(),
            'Magento Mode' => $this->appState->getMode(),
            'PHP Version' => phpversion(),
            'Redis Version' => $this->getRedisVersion(),
            'MySQL Version' => $this->getMySQLVersion(),
            'Elasticsearch Version' => $this->getElasticsearchVersion(),
            'OS' => php_uname(),
            'Loaded PHP Extensions' => implode(', ', get_loaded_extensions()),
        ];

        $output = "";
        foreach ($info as $label => $value) {
            $output .= "$label: $value\n";
        }

        return $output;
    }

    public function getSystemInfoArray(): array
    {
        $info = [
            'Magento Version' => $this->productMetadata->getVersion(),
            'Magento Mode' => $this->appState->getMode(),
            'PHP Version' => phpversion(),
            'Redis Version' => $this->getRedisVersion(),
            'MySQL Version' => $this->getMySQLVersion(),
            'Elasticsearch Version' => $this->getElasticsearchVersion(),
            'OS' => php_uname(),
            'Loaded PHP Extensions' => get_loaded_extensions(),
        ];

        return $info;
    }

    private function getMySQLVersion(): string
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            return $connection->fetchOne('SELECT VERSION()');
        } catch (\Exception $e) {
            return 'Not Available or Connection Failed';
        }
    }

    private function getRedisVersion(): string
    {
        try {
            $redis = new \Redis();
            $redis->connect('127.0.0.1', 6379, 1.5);
            $info = $redis->info();
            return $info['redis_version'] ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Not Available or Connection Failed';
        }
    }

    private function getElasticsearchVersion(): string
    {
        try {
            $url = 'http://localhost:9200'; // adjust if using different port/host
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            return $data['version']['number'] ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Not Available or Connection Failed';
        }
    }

}
