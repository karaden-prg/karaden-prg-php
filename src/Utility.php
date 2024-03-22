<?php

namespace Karaden;

use Exception;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Karaden\Exception\FileUploadFailedException;
use Karaden\Model\BulkFile;
use Karaden\Model\BulkMessage;
use Karaden\Model\Collection;
use Karaden\Model\KaradenObject;
use Karaden\Model\Error;
use Karaden\Model\Message;
use Karaden\RequestOptions;

class Utility
{
    const objectTypes = [
        Error::OBJECT_NAME => Error::class,
        Collection::OBJECT_NAME => Collection::class,
        Message::OBJECT_NAME => Message::class,
        BulkFile::OBJECT_NAME => BulkFile::class,
        BulkMessage::OBJECT_NAME => BulkMessage::class,
    ];

    public static function convertToKaradenObject(object $contents, RequestOptions $requestOptions)
    {
        $class = property_exists($contents, 'object') && isset(static::objectTypes[$contents->object]) ?
            static::objectTypes[$contents->object] : KaradenObject::class;

        return static::constructFrom($class, $contents, $requestOptions);
    }

    protected static function constructFrom(string $class, object $contents, RequestOptions $requestOptions): KaradenObject
    {
        $id = property_exists($contents, 'id') ? $contents->id : null;
        $object = new $class($id, $requestOptions);

        foreach (get_object_vars($contents) as $key => $value) {
            if (is_array($value)) {
                $object->setProperty($key, static::convertToArray($value, $requestOptions));
            } else if(is_object($value)) {
                $object->setProperty($key, static::convertToKaradenObject($value, $requestOptions));
            } else {
                $object->setProperty($key, $value);
            }
        }

        return $object;
    }

    protected static function convertToArray(array $contents, RequestOptions $requestOptions): array
    {
        $array = [];
        foreach ($contents as $key => $value) {
            $array[$key] = is_object($value) ? static::convertToKaradenObject($value, $requestOptions) : $value;
        }

        return $array;
    }

    public static function isDisabled($disableFunctionsOutput, $functionName)
    {
        $disabledFunctions = explode(',', $disableFunctionsOutput);
        foreach ($disabledFunctions as $disabledFunction) {
            if (trim($disabledFunction) === $functionName) {
                return true;
            }
        }

        return false;
    }

    public static function putSignedUrl(string $signedUrl, string $filename, string $contentType = 'application/octet-stream'): void
    {
        try {
            $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
            $streamFactory = Psr17FactoryDiscovery::findStreamFactory();
            $httpClient = Config::$httpClient ?? Psr18ClientDiscovery::find();

            $request = $requestFactory->createRequest('PUT', $signedUrl)
                ->withHeader('Content-Type', $contentType)
                ->withBody($streamFactory->createStreamFromFile($filename));
            $response = $httpClient->sendRequest($request);

            if ($response->getStatusCode() !== 200) {
                throw new FileUploadFailedException();
            }
        } catch (FileUploadFailedException $e1) {
            throw $e1;
        } catch (Exception $e2) {
            throw new FileUploadFailedException($e2);
        }
    }
}
