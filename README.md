# Karaden PHPライブラリ
Karaden PHPライブラリは、PHPで書かれたアプリケーションからKaraden APIへ簡単にアクセスするための手段を提供します。<br />
それにはAPIレスポンスから動的に初期化するAPIリソースの一連のクラス定義が含まれているため、Karaden APIの幅広いバージョンと互換性があります。
## インストール方法
パッケージを変更しないならば、このソースコードは必要ありません。<br />
パッケージを使用したいだけならば、下記を実行するだけです。
```
composer require karaden-prg/karaden-prg-php
```
## 動作環境
PHP 7.4～

このライブラリはHTTPクライアントを抽象化するライブラリである[HTTPlug](http://httplug.io/)を使用しています。<br />
使用する環境に合わせ、いずれかの[HTTPlugのクライアント/アダプタ](https://docs.php-http.org/en/latest/clients.html)をインストールする必要があります。<br />
例えばGuzzleの7.xを使用する場合、下記を実行する必要があります。
```
composer require php-http/guzzle7-adapter
```
## 使い方
Karadenでテナントを作成し、プロジェクト毎に発行できるトークンを発行する必要があります。<br />
作成したテナントID（テナントIDはテナント選択画面で表示されています）は、`Config::$tenantId`に、発行したトークンは`Config::$apiKey`にそれぞれ設定します。
```php
\Karaden\Config::$apiKey = '<トークン>';
\Karaden\Config::$tenantId = '<テナントID>';
$params = \Karaden\Param\Message\MessageCreateParams::newBuilder()
    ->withServiceId(1)
    ->withTo('09012345678')
    ->withBody('本文')
    ->build();
$message = \Karaden\Model\Message::create($params);
```
### リクエスト毎の設定
同一のプロセスで複数のキーを使用する必要がある場合、リクエスト毎にキーやテナントIDを設定することができます。
```php
$params = \Karaden\Param\Message\MessageDetailParams::newBuilder()
    ->withId('<メッセージID>')
    ->build();
$requestOptions = \Karaden\RequestOptions::newBuilder()
    ->withApiKey('<トークン>')
    ->withTenantId('<テナントID>')
    ->build();
$message = \Karaden\Model\Message::detail($params, $requestOptions);
```
### HTTPクライアントの明示的な指定
通常、PSR-18のインタフェースを実装したHTTPクライアントを指定しなくても適切な実装を検索し、検出したHTTPクライアントを使用しますが、明示的に指定することもできます。<br />
HTTPクライアントに依存したタイムアウトやプロキシなどのような設定を必要とするユースケースにおいて使用します。
```php
\Karaden\Config::$httpClient = \Http\Adapter\Guzzle7\Client::createWithConfig(['timeout' => <秒>]);
```
### タイムアウトについて
通信をするファイルサイズや実行環境の通信速度によってはHTTP通信時にタイムアウトが発生する可能性があります。<br />
何度も同じような現象が起こる際は、ファイルサイズの調整もしくは`HTTPクライアントの明示的な指定`からHTTPクライアントの指定及びタイムアウトの時間を増やして、再度実行してください。
