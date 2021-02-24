<?php

namespace Hostville\Modullo\UserLaravel\Auth;


use Hostville\Modullo\Sdk;
use Illuminate\Auth\GenericUser;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Notifications\Notifiable;

class ModulloUser extends GenericUser implements Arrayable, \JsonSerializable
{
    use Notifiable;

    /** @var Sdk  */
    private $sdk;

    /**
     * modulloUser constructor.
     *
     * @param array    $attributes
     * @param Sdk|null $sdk
     */
    public function __construct(array $attributes = [], Sdk $sdk = null)
    {
        parent::__construct($attributes);
        $this->sdk = $sdk ?: Container::getInstance()->make(Sdk::class);
    }

    /**
     * Sets the attributes on the resource.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Returns the modullo Sdk in use by the instance.
     *
     * @return Sdk
     */
    public function getmodulloSdk(): Sdk
    {
        return $this->sdk;
    }

//    /**
//     * Returns the company information, if available.
//     *
//     * @param bool $requestIfNotAvailable request the information from the API if it's not available
//     * @param bool $asObject
//     *
//     * @return array|null|object
//     */
//    public function company(bool $requestIfNotAvailable = true, bool $asObject = false)
//    {
//        if (!array_key_exists('company', $this->attributes) && $requestIfNotAvailable) {
//            $service = $this->sdk->createProfileService();
//            $response = $service->addQueryArgument('include', 'company')->send('get');
//            # make a request to the API
//            if (!$response->isSuccessful()) {
//                return null;
//            }
//            $this->attributes = $response->getData();
//        }
//        $user = $this->attributes['company']['data'] ?? [];
//        return $asObject ? (object) $user : $user;
//    }

    /**
     * @inheritdoc
     */
    public function getRememberTokenName()
    {
        return 'token';
    }

    /**
     * @return string
     */
    public function routeNotificationForSms()
    {
        return (string)$this->attributes['phone_number'] ?? "";
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \Illuminate\Database\Eloquent\JsonEncodingException
     */
    public function toJson($options = 0)
    {
      try {
        $json = json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR | $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
          throw JsonEncodingException::forModel($this, json_last_error_msg());
        }

        return $json;
      }
      catch (\JsonException $e) {
      }


    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}