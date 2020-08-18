<?php

namespace heimo\auth;

use Firebase\JWT\JWT;

class Auth
{
    const ENCODE = 1;
    const DECODE = 2;
    /**
     * @var string 签名SECRET
     */
    protected $secret;

    /**
     * @var string 私钥
     */
    protected $privateKey;

    /**
     * @var string 公钥
     */
    protected $publicKey;

    /**
     * @var int 过期时间
     */
    protected $ttl;

    /**
     * @var int 过期允许刷新时间
     */
    protected $refreshTtl;

    /**
     * @var string 加密方式
     */
    protected $alg;

    /**
     * @var string header key
     */
    protected $key;

    /**
     * @var string 传输类型
     */
    protected $type;

    /**
     * 设置额外的head参数
     * @var array
     */
    protected $head = [];

    /**
     * 解密后内容
     * @var object
     */
    protected $tokenData = null;

    private static $instance = null;

    /**
     * Auth constructor
     *
     * @throws AuthException
     */
    private function __construct()
    {
        $secret     = config('jwt.secret');
        $privateKey = config('jwt.private_key');
        $publicKey  = config('jwt.public_key');

        if (!$secret && (!$privateKey || !$publicKey)) {
            throw new AuthException('secret, private_key, public_key is Empty!');
        }

        $this->secret     = $secret;
        $this->privateKey = $privateKey;
        $this->publicKey  = $publicKey;
        $this->ttl        = config('jwt.ttl', 86400);
        $this->refreshTtl = config('jwt.refresh_ttl', 86400);
        $this->alg        = config('jwt.alg', 'RS256');
        $this->key        = config('jwt.key', 'authorization');
        $this->type       = config('jwt.type', 'header');

    }

    /**
     * 单例模式运行
     * @return Auth|null
     */
    public static function make()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 获取TOKEN
     *
     * @param array $data
     *
     * @return string
     */
    public function generateToken(array $data = [])
    {
        $hmc_data               = [];
        $hmc_data['timestamp']  = time();
        $hmc_data['verifyTime'] = time() + $this->ttl;
        $hmc_data['alg']        = $this->alg;
        $hmc_data['data']       = $data;

        $key = $this->getKey(self::ENCODE);

        $token = JWT::encode($hmc_data, $key, $this->alg, null, $this->head);
        header("$this->key:$token");

        return $token;
    }

    /**
     * 校验TOKEN
     *
     * @return bool|object
     * @throws AuthException
     */
    public function verifyToken()
    {
        $token = $this->getToken();

        if (empty($token)) {
            throw new AuthException('The token is Empty!', 401);
        }

        $decodeData = $this->JwtDecodeData($token);
        if (!isset($decodeData->timestamp) || !isset($decodeData->verifyTime)) {
            throw new AuthException('The token is Invalid!', 401);
        }

        if (time() > $decodeData->verifyTime) {
            //在允许刷新时间范围内自动刷新token
            if (time() < $decodeData->verifyTime + $this->refreshTtl) {
                $decodeData = $this->refreshToken();
            } else {
                throw new AuthException('The token is Expired!', 401);
            }
        }

        $this->tokenData = $decodeData;

        return $this->tokenData;
    }

    /**
     * 解密TOKEN
     *
     * @param $token
     *
     * @return object
     * @throws AuthException
     */
    private function JwtDecodeData($token)
    {
        if (empty($token)) {
            throw new AuthException('The token is Empty!', 401);
        }

        $this->head = $this->objectToArray($this->getHeads());

        $key = $this->getKey(self::DECODE);

        $token = JWT::decode($token, $key, ['HS256', 'HS384', 'HS512', 'RS256', 'RS384', 'RS512']);

        return $token;
    }

    /**
     * 获取传递的header数据
     *
     * @return object
     */
    public function getHeads()
    {
        $jwt = $this->getToken();
        [$headBase64, $bodyBase64, $cryptoBase64] = explode('.', $jwt);
        $head = JWT::jsonDecode(JWT::urlsafeB64Decode($headBase64));

        return $head;
    }

    /**
     * 刷新TOKEN
     * @return string
     * @throws AuthException
     */
    public function refreshToken()
    {
        if (!$this->tokenData) {
            $this->verifyToken();
        }

        if (time() - $this->tokenData->verifyTime > $this->refreshTtl) {
            throw new AuthException('The token is Expired!', 401);
        }

        return $this->generateToken($this->objectToArray($this->tokenData->data));
    }

    /**
     * 对象转数组
     *
     * @param $data
     *
     * @return mixed
     */
    public function objectToArray($data)
    {
        return json_decode(json_encode($data), true);
    }

    /**
     * 获取TOKEN
     *
     * @return mixed
     */
    public function getToken()
    {
        if ($this->type === 'header') {
            return request()->header($this->key);
        } else {
            return request()->param($this->key);
        }
    }

    /**
     * 获取所有的加密数据
     *
     * @return null
     */
    public function getTokenData()
    {
        return $this->tokenData;
    }

    protected function getKey($type = self::ENCODE)
    {
        switch ($this->alg) {

            case 'RS256':
            case 'RS384':
            case 'RS512':
                $key = $type == self::ENCODE ? $this->privateKey : $this->publicKey;
                break;

            case 'HS256':
            case 'HS384':
            case 'HS512':
            default :
                $key = $this->secret;
                break;
        }

        return $key;
    }
}