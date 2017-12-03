<?php
/*** 
*@copyright  Copyright (c) 2017 UFan
*@mail uf.lins1128@gmail.com  
***/


Class Redis_PDO {
    protected $host_url;
    protected $redisobj;
    
    function __construct($host){
        $this->host_url = $host;
        $this->redisobj = new Redis();
        $this->redisobj->connect($this->host_url, 6379);
    }

    function setKeyValue($key,$value){
        $this->redisobj->set($key, $value);
    }

    function ifKeyExists($key){
        return $this->redisobj->exists($key);
    }
  
   function getValue($key){
       return $this->redisobj->get($key);
   }

}



?>
