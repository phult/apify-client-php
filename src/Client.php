<?php
namespace Megaads\ApifyClient;

class Client
{
    private static $client;
    private $host = "";
    private $auth = null;
    private $entity = null;
    private $customFields = [];
    private $fields = [];
    private $filters = [];
    private $sorts = [];
    private $embeds = [];
    private $pageSize = null;
    private $pageId = null;
    private $requestHeaders = [];

    const OPTION_API_HOST = "API_HOST";
    const OPTION_API_AUTH = "API_AUTH";
    const OPTION_REQUEST_HEADER = "REQUEST_HEADER";

    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_PATCH = "PATCH";
    const METHOD_DELETE = "DELETE";

    const SELECTION_EQUAL = "=";
    const SELECTION_NOT_EQUAL = "!=";
    const SELECTION_GREATER = ">";
    const SELECTION_GREATER_EQUAL = ">=";
    const SELECTION_LESS = "<";
    const SELECTION_LESS_EQUAL = "<=";
    const SELECTION_IN = "={";
    const SELECTION_NOT_IN = "!={";
    const SELECTION_BETWEEN = "=[";
    const SELECTION_NOT_BETWEEN = "!=[";
    const SELECTION_LIKE = "~";
    const SELECTION_NOT_LIKE = "!~";

    public static function endpoint($entity, $options)
    {
        self::$client = new \Megaads\ApifyClient\Client();
        self::$client->entity = $entity;
        foreach ($options as $key => $value) {
            switch ($key) {
                case self::OPTION_API_HOST:
                    self::$client->host = $value;
                    break;
                case self::OPTION_API_AUTH:
                    self::$client->auth = $value;
                    break;
                case self::OPTION_REQUEST_HEADER:
                    self::$client->requestHeaders = $value;
                    break;
            }
        }
        return self::$client;
    }
    public function addField($field, $value)
    {
        self::$client->customFields[$field] = $value;
        return self::$client;
    }
    public function select($fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $item) {
                self::$client->fields[$item] = $item;
            }
        } else {
            self::$client->fields[$fields] = $fields;
        }
        return self::$client;
    }
    public function selectRaw($fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $item) {
                self::$client->fields['raw(' . $item . ')'] = 'raw(' . $item . ')';
            }
        } else {
            self::$client->fields['raw(' . $fields . ')'] = 'raw(' . $fields . ')';
        }
        return self::$client;
    }
    public function filter($field, $operator, $value)
    {
        self::$client->filters[$field . ":" . $operator . ":" . (is_array($value) ? implode(",", $value) : $value)] = [
            "field" => $field,
            "operator" => $operator,
            "value" => $value,
        ];
        return self::$client;
    }
    public function sort($sorts)
    {
        if (is_array($sorts)) {
            foreach ($sorts as $item) {
                self::$client->sorts[$item] = $item;
            }
        } else {
            self::$client->sorts[$sorts] = $sorts;
        }
        return self::$client;
    }
    public function sortRaw($sorts)
    {
        if (is_array($sorts)) {
            foreach ($sorts as $item) {
                self::$client->sorts['raw(' . $item . ')'] = 'raw(' . $item . ')';
            }
        } else {
            self::$client->sorts['raw(' . $sorts . ')'] = 'raw(' . $sorts . ')';
        }
        return self::$client;
    }
    public function pageSize($pageSize)
    {
        self::$client->pageSize = $pageSize;
        return self::$client;
    }
    public function pageId($pageId)
    {
        self::$client->pageId = $pageId;
        return self::$client;
    }
    public function embed($embeds)
    {
        if (is_array($embeds)) {
            foreach ($embeds as $item) {
                self::$client->embeds[$item] = $item;
            }
        } else {
            self::$client->embeds[$embeds] = $embeds;
        }
        return self::$client;
    }
    public function toURL()
    {
        $retval = self::$client->host;
        // entity
        $retval .= "/" . self::$client->entity . "?";
        //auth
        if (self::$client->auth != null) {
            $retval .= "&" . self::$client->auth;
        }
        // customFields
        if (count(self::$client->customFields) > 0) {
            foreach (self::$client->customFields as $key => $value) {
                $retval .= "&" . $key . "=" . $value;
            }
        }
        //fields
        if (count(self::$client->fields) > 0) {
            $retval .= "&fields=" . urlencode(implode(",", self::$client->fields));
        }
        //filters
        if (count(self::$client->filters) > 0) {
            $retval .= "&filters=";
            foreach (self::$client->filters as $key => $value) {
                switch ($value["operator"]) {
                    case self::SELECTION_EQUAL:
                    case self::SELECTION_NOT_EQUAL:
                    case self::SELECTION_GREATER:
                    case self::SELECTION_GREATER_EQUAL:
                    case self::SELECTION_LESS:
                    case self::SELECTION_LESS_EQUAL:
                    case self::SELECTION_LIKE:
                    case self::SELECTION_NOT_LIKE:
                        $retval .= $value["field"] . $value["operator"] . $value["value"] . ",";
                        break;
                    case self::SELECTION_IN:
                    case self::SELECTION_NOT_IN:
                        $retval .= $value["field"] . $value["operator"] . implode(";", $value["value"]) . "},";
                        break;
                    case self::SELECTION_BETWEEN:
                    case self::SELECTION_NOT_BETWEEN:
                        $retval .= $value["field"] . $value["operator"] . (is_array($value["value"]) ? implode(";", $value["value"]) : $value["value"]) . "],";
                        break;
                }
            }
            $retval = rtrim($retval, ',');
        }
        //sorts
        if (count(self::$client->sorts) > 0) {
            $retval .= "&sorts=" . urlencode(implode(",", self::$client->sorts));
        }
        if (self::$client->pageSize != null) {
            $retval .= "&page_size=" . self::$client->pageSize;
        }
        if (self::$client->pageId != null) {
            $retval .= "&page_id=" . self::$client->pageId;
        }
        //embeds
        if (count(self::$client->embeds) > 0) {
            $retval .= "&embeds=";
            $retval .= implode(",", self::$client->embeds);
        }
        return $retval;
    }
    public function find($id)
    {
        $originalEntity = self::$client->entity;
        self::$client->entity .= "/" . $id;
        $requestURL = self::$client->toURL();
        self::$client->entity = $originalEntity;
        return self::request($requestURL, self::METHOD_GET, [], self::$client->requestHeaders);
    }
    public function get()
    {
        $requestURL = self::$client->toURL();
        return self::request($requestURL, self::METHOD_GET, [], self::$client->requestHeaders);
    }
    public function first()
    {
        $requestURL = self::$client->toURL();
        $requestURL .= "&metric=first";
        return self::request($requestURL, self::METHOD_GET, [], self::$client->requestHeaders);
    }
    public function count()
    {
        $requestURL = self::$client->toURL();
        $requestURL .= "&metric=count";
        return self::request($requestURL, self::METHOD_GET, [], self::$client->requestHeaders);
    }
    public function increase()
    {
        $requestURL = self::$client->toURL();
        $requestURL .= "&metric=increment";
        return self::request($requestURL, self::METHOD_GET, [], self::$client->requestHeaders);
    }
    public function decrease()
    {
        $requestURL = self::$client->toURL();
        $requestURL .= "&metric=decrement";
        return self::request($requestURL, self::METHOD_GET, [], self::$client->requestHeaders);
    }

    public function create($data = [])
    {
        $requestURL = self::$client->toURL();
        return self::request($requestURL, self::METHOD_POST, $data, self::$client->requestHeaders);
    }
    public function update($id, $data = [])
    {
        $originalEntity = self::$client->entity;
        self::$client->entity .= "/" . $id;
        $requestURL = self::$client->toURL();
        self::$client->entity = $originalEntity;
        return self::request($requestURL, self::METHOD_PUT, $data, self::$client->requestHeaders);
    }
    public function delete($id)
    {
        $originalEntity = self::$client->entity;
        self::$client->entity .= "/" . $id;
        $requestURL = self::$client->toURL();
        self::$client->entity = $originalEntity;
        return self::request($requestURL, self::METHOD_DELETE, [], self::$client->requestHeaders);
    }
    public static function request($url, $method = "GET", $data = [], $headers = [])
    {
        $retval = null;
        $channel = curl_init();
        $headers[] = 'Content-Type:application/json';
        curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($channel, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($channel, CURLOPT_MAXREDIRS, 3);
        curl_setopt($channel, CURLOPT_POSTREDIR, 1);
        $response = curl_exec($channel);
        curl_close($channel);
        $retval = json_decode($response, true);
        if ($retval == null) {
            $retval = [
                "status" => "fail",
                "result" => $response,
            ];
        }
        return $retval;
    }
}
