<?php
return [

    //加密密钥
    'secret'      => MD5('secret'),

    //加密私钥
    'private_key' => <<<EOF

EOF,

    //解密私钥
    'public_key'  => <<<EOF

EOF,
    //加密的有效期(s)
    'ttl'         => 60,

    //过期多久以内允许刷新(s)
    'refresh_ttl' => 60,

    //传输方式(header/body)
    'type'        => 'header',

    //密文传输的字段名称
    'key'         => 'Authorization',

    //加密方式 'HS256','HS384','HS512','RS256','RS384','RS512'
    'alg'         => 'RS256'

];