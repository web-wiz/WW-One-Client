<?php

namespace Api;

require_once 'application/libraries/Api/ApiRequest.php';
require_once 'application/libraries/Exception/ApiResponseException.php';
require_once 'application/libraries/Exception/ValidationException.php';
require_once 'application/libraries/Interface/ApiRequestGetInterface.php';
require_once 'application/libraries/Interface/ApiResponseInterface.php';

use ApiResponseException;
use ArrayAccess;
use Countable;
use Interfaces\ApiRequestGetInterface;
use Interfaces\ApiResponseInterface;
use InvalidArgumentException;
use Iterator;
use OutOfBoundsException;
use stdClass;
use ValidationException;

/**
 * Class ApiResponse api response
 */
class ApiResponse implements ApiResponseInterface, Iterator, ArrayAccess, Countable
{
    /**
     * @var stdClass response body
     */
    private $body;
    /**
     * @var array response data
     */
    private $data;
    /**
     * @var array data keys for iterator
     */
    private $data_keys;
    /**
     * @var int http code of response
     */
    private $http_code;
    /**
     * @var array pagination
     */
    private $pagination;
    /**
     * @var int iterator position
     */
    private $position;
    /**
     * @var ApiRequest
     */
    private $request;

    /**
     * ApiResponse constructor. Make request.
     *
     * @param ApiRequestGetInterface $request api request
     * @throws ApiResponseException
     * @throws ValidationException
     */
    public function __construct(ApiRequestGetInterface $request)
    {
        $this->request = $request;
        $this->http_code = $request->getResponseHttpCode();
        $this->body = json_decode($request->getResponseString());

        if (isset($this->body->errors)) {
            $errors = '';
            foreach ($this->body->errors as $error) {
                $errors .= $error->message . ' ';
            }
            throw new ValidationException($errors);
        }

        if (intval($this->http_code / 100) != 2 && isset($this->body->message)) {
            throw new ApiResponseException($this->body->message);
        }

        if (! isset($this->body) || ! isset($this->body->data) || intval($this->http_code / 100) != 2) {
            throw new ApiResponseException('Invalid api response');
        }

        $this->iterate((array) $this->body->data);
        if (isset($this->body->pagination)) {
            $this->pagination = (array) $this->body->pagination;
        }
    }

    /**
     * Clone collection.
     */
    public function __clone()
    {
        foreach ($this->data as $id => $item) {
            $this->data[$id] = clone $item;
        }
    }

    /**
     * Get first row of response data.
     *
     * @return object response first row
     */
    public function row()
    {
        return $this->offsetExists(0) ? $this->offsetGet(0) : null;
    }

    /**
     * Return first row as associative array
     *
     * @return array response first row
     */
    public function rowArray()
    {
        return $this->offsetExists(0) ? (array) $this->offsetGet(0) : null;
    }

    /**
     * Return response http code.
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }

    /**
     * Return pagination
     *
     * @return array
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Return request made this response
     *
     * @return ApiRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Push item to end of collection
     *
     * @param object $item
     */
    public function push($item)
    {
        if (! is_object($item)) {
            throw new InvalidArgumentException("Response collection item must by object.");
        }

        $this->data[] = $item;
        $this->data_keys[] = end(array_keys($this->data));
    }

    /**
     * Check that collection empty or not
     *
     * @return bool true in case of empty
     */
    public function isEmpty()
    {
        return empty($this->data);
    }

    /**
     * Return number of items in collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Return collection as array
     *
     * @return array
     */
    public function toArray()
    {
        $obj = clone $this;

        return $obj->data;
    }

    /**
     * Return collection as assoc
     *
     * @return mixed
     */
    public function toAssoc()
    {
        return json_decode(json_encode($this->data), true);
    }

    /**
     * ArrayAccess to collection items. Whether or not item exists by index.
     * Example, empty($collection[34567])
     *
     * @param mixed $index collection item index
     * @return bool true in case of exists
     */
    public function offsetExists($index)
    {
        return isset($this->data[$index]);
    }

    /**
     * ArrayAccess to collection items. Get by index.
     * Example $collection[$index]
     *
     * @param mixed $index collection item index
     * @return object
     */
    public function offsetGet($index)
    {
        if (! $this->offsetExists($index)) {
            throw new OutOfBoundsException("No item by index $index in the collection");
        }

        return $this->data[$index];
    }

    /**
     * ArrayAccess to collection items. Create new or update existing.
     * Example, $collection[$id] = new someClass();
     *
     * @param mixed $index collection item index
     * @param object $item collection item
     * @return void
     */
    public function offsetSet($index, $item)
    {
        $position = array_search($index, $this->data_keys);
        if ($position === false) { // create new collection item
            $this->push($item);
        } else { // update existing collection item
            $this->data[$index] = $item;
        }
    }

    /**
     * ArrayAccess to collection items. Unset item from collection.
     * Example, unset($collection['gdm-1']
     *
     * @param mixed $index collection item index
     * @return void
     */
    public function offsetUnset($index)
    {
        $position = array_search($index, $this->data_keys);
        unset($this->data[$index]);
        array_splice($this->data_keys, $position, 1);

        // for right unset through foreach
        $this->prev();
    }

    /**
     * Construct iterator
     *
     * @param array $data response data
     */
    private function iterate(array $data)
    {
        $this->data = [];
        $this->data_keys = [];
        $this->position = 0;
        foreach ($data as $item) {
            $this->push($item);
        }
    }

    /**
     * Iterator. Jump to begging of of collection
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Iterator. Key of current element of collection
     *
     * @return int
     */
    public function key()
    {
        return $this->data_keys[$this->position];
    }

    /**
     * Iterator. Return current element of collection
     *
     * @return object
     */
    public function current()
    {
        return $this->data[$this->key()];
    }

    /**
     * Iterator. Increase collection pointer
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Iterator. Decrease collection pointer
     */
    public function prev()
    {
        $this->position--;
    }

    /**
     * Iterator. Check that current element of collection exists
     *
     * @return bool
     */
    public function valid()
    {
        return $this->position < $this->count();
    }
}