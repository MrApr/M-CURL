<?php


namespace Core;


class Curl
{
    private $ch;

    private $method = "GET";

    private static $url;

    private $post_fields;

    private $content_type;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->ch = curl_init();

        return $this;
    }

    public static function setUrl(string $url)
    {
        self::$url = $url;
        return new self;
    }

    public function setMethod(string $method = "GET")
    {
        $this->method = $method;
        return $this;
    }

    public function setPostFields(array $post_fields = [])
    {
        if(count($post_fields))
        {
            $this->post_fields = $post_fields;
        }
        else
        {
            throw new \Exception('Post fields need to be an array and get filled');
        }
        return $this;
    }

    public function contentType(array $content_type = [])
    {
        if(count($content_type))
        {
            $this->content_type = $content_type;
        }
        else
        {
            throw new \Exception('Content Type need to be an array and get filled');
        }
        return $this;
    }

    public function exec()
    {
        if(empty($this->ch))
        {
          $this->init();
        }

        curl_setopt($this->ch,CURLOPT_URL,self::$url);
        curl_setopt($this->ch,CURLOPT_ENCODING,"utf8");
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,$this->method);

        if(!empty($this->content_type))
        {
            curl_setopt($this->ch,CURLOPT_HTTPHEADER,$this->content_type);
        }

        if(!empty($this->post_fields))
        {
            curl_setopt($this->ch,CURLOPT_POSTFIELDS,json_encode($this->post_fields));
        }

        try{
            $result = curl_exec($this->ch);
            $curl_info = curl_getinfo($this->ch);
            $status_code = $curl_info['http_code'];
            $curl_error = curl_error($this->ch);
        }catch (\Exception $e)
        {
            return $this->resualtGenerator('500',$e->getMessage());
        }
        finally{
            curl_close($this->ch);
        }

        if(!empty($curl_error))
        {
            return $this->resualtGenerator('500',$curl_error);
        }
        else
        {
            return $this->resualtGenerator($status_code,$result);
        }
    }

    public function getStatusCode()
    {
        if(empty($this->ch))
        {
            $this->init();
        }

        curl_setopt($this->ch,CURLOPT_URL,self::$url);
        curl_setopt($this->ch,CURLOPT_ENCODING,"utf8");
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,$this->method);

        if(!empty($this->content_type))
        {
            curl_setopt($this->ch,CURLOPT_HTTPHEADER,$this->content_type);
        }

        if(!empty($this->post_fields))
        {
            curl_setopt($this->ch,CURLOPT_POSTFIELDS,json_encode($this->post_fields));
        }

        try{
            $result = curl_exec($this->ch);
            $curl_info = curl_getinfo($this->ch);
            $status_code = $curl_info['http_code'];
            $curl_error = curl_error($this->ch);
        }catch (\Exception $e)
        {
            return $this->resualtGenerator('500',$e->getMessage());
        }
        finally{
            curl_close($this->ch);
        }
        return $status_code;
    }

    public function resualtGenerator(int $status,string $message)
    {
        $message = array(
            'status' => $status,
            'message' => (is_string($message)) ? $message : json_decode($message,true)
        );

        return json_encode($message);
    }
}