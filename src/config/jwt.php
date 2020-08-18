<?php
return [

    //加密密钥
    'secret' => MD5('secret'),

    //加密密钥
    'private_key' => '',

    //加密密钥
    'public_key' => '',

    //加密的有效期(s)
    'ttl' => 180,

    //过期多久以内允许刷新(s)
    'refresh_ttl' => 360,

    //传输方式(header/body)
    'type' => 'header',

    //密文传输的字段名称
    'key' => 'Authorization',

    //加密方式 'HS256','HS384','HS512','RS256','RS384','RS512'
    'alg' => 'RS256'

];