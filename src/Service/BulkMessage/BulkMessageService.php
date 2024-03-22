<?php

namespace Karaden\Service\BulkMessage;

use Exception;
use Http\Client\Common\PluginClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Karaden\Exception\BulkMessageCreateFailedException;
use Karaden\Exception\BulkMessageListMessageRetryLimitExceedException;
use Karaden\Exception\BulkMessageShowRetryLimitExceedException;
use Karaden\Exception\FileDownloadFailedException;
use Karaden\Exception\FileNotFoundException;
use Karaden\Model\BulkFile;
use Karaden\Model\BulkMessage;
use Karaden\Param\Message\Bulk\BulkMessageCreateParams;
use Karaden\Param\Message\Bulk\BulkMessageDownloadParams;
use Karaden\Param\Message\Bulk\BulkMessageListMessageParams;
use Karaden\Param\Message\Bulk\BulkMessageShowParams;
use Karaden\Config;
use Karaden\RequestOptions;
use Karaden\Utility;

class BulkMessageService
{
    const BUFFER_SIZE = 1024 * 1024;
    const REGEX_PATTERN = '/filename="([^"]+)"/';

    public static function create(string $filename, ?RequestOptions $requestOptions = null): BulkMessage
    {
        if (!is_file($filename)) {
            throw new FileNotFoundException();
        }

        $bulkFile = BulkFile::create($requestOptions);

        Utility::putSignedUrl($bulkFile->getUrl(), $filename, 'text/csv');

        $bulkMessageParams = BulkMessageCreateParams::newBuilder()
            ->withBulkFileId($bulkFile->getId())
            ->build();
        return BulkMessage::create($bulkMessageParams, $requestOptions);
    }

    public static function download(BulkMessageDownloadParams $params, ?RequestOptions $requestOptions = null)
    {
        $params->validate();
        $showParams = BulkMessageShowParams::newBuilder()
            ->withId($params->id)
            ->build();
        if (!BulkMessageService::checkBulkMessageStatus($params->maxRetries, $params->retryInterval, $showParams, $requestOptions)) {
            throw new BulkMessageShowRetryLimitExceedException();
        }

        $listMessageParams = BulkMessageListMessageParams::newBuilder()
            ->withId($params->id)
            ->build();
        $downloadUrl = BulkMessageService::getDownloadUrl($params->maxRetries, $params->retryInterval, $listMessageParams, $requestOptions);
        if (is_null($downloadUrl)) {
            throw new BulkMessageListMessageRetryLimitExceedException();
        }

        try {
            BulkMessageService::getContents($downloadUrl, realpath($params->directoryPath));
        } catch(Exception  $e) {
            throw new FileDownloadFailedException();
        }
    }

    private static function getContents(string $downloadUrl, string $directoryPath)
    {
        $httpClient = new PluginClient(Config::$httpClient ?? HttpClientDiscovery::find());
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $request = $requestFactory->createRequest('GET', $downloadUrl);
        $response = $httpClient->sendRequest($request);
        if (!preg_match(BulkMessageService::REGEX_PATTERN, $response->getHeader('content-disposition')[0], $matches)) {
            throw new FileDownloadFailedException();
        }
        $body = $response->getBody();
        $filename = implode(DIRECTORY_SEPARATOR, [$directoryPath, $matches[1]]);
        $fileHandle = fopen($filename, 'w');
        while (!$body->eof()) {
            fwrite($fileHandle, $body->read(BulkMessageService::BUFFER_SIZE));
        }
        fclose($fileHandle);
    }

    private static function checkBulkMessageStatus(int $retryCount, int $retryInterval, BulkMessageShowParams $params, ?RequestOptions $requestOptions = null): bool
    {
        foreach(range(0, $retryCount) as $count) {
            if($count > 0) {
                sleep($retryInterval);
            }

            $bulkMessage = BulkMessage::show($params, $requestOptions);
            if ($bulkMessage->getStatus() == BulkMessage::STATUS_ERROR) {
                throw new BulkMessageCreateFailedException();
            }

            if ($bulkMessage->getStatus() == BulkMessage::STATUS_DONE) {
                return true;
            }
        }

        return false;
    }

    private static function getDownloadUrl(int $retryCount, int $retryInterval, BulkMessageListMessageParams $params, ?RequestOptions $requestOptions = null): ?string
    {
        foreach(range(0, $retryCount) as $count) {
            if($count > 0) {
                sleep($retryInterval);
            }

            $result = BulkMessage::listMessage($params, $requestOptions);
            if(!is_null($result)) {
                return $result;
            }
        }

        return null;
    }
}
