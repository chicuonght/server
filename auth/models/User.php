<?php
namespace auth\models;

use Firebase\JWT\JWT;
use tuyakhov\jsonapi\ResourceInterface;
use tuyakhov\jsonapi\ResourceTrait;
use Yii;

use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\HttpException;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface, ResourceInterface
{
    use \damirka\JWT\UserTrait;
    use ResourceTrait;

    /**
     * @var User
     */
    public static $_instance;

    /**\
     * @var int User Id
     */
    public $user_id;



    /**
     * @param array $fields
     * @return array
     */
    public function getResourceAttributes(array $fields = [])
    {
        $attributes = [
            'user_id' => $this->user_id
        ];
        return $attributes;
    }


    // Override this method
    protected static function getSecretKey()
    {

        return isset(\Yii::$app->params['jwt-key']) && ! empty(\Yii::$app->params['jwt-key']) ? \Yii::$app->params['jwt-key'] : false;
    }

    // And this one if you wish
    public static function getHeaderToken()
    {
        return [];
    }

    protected static function authenticateJWT($token){
        $client = new Client();
        try{


        $headers = [
            'Authorization' => sprintf('Bearer %s', $token),
            'Content-Type' => 'application/json'
        ];

        $params = [];

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setHeaders($headers)
            ->setUrl(self::getAuthenticateJwtUrl())
            ->setData($params);
        $response = $request->send();

        $jsonString = ($response->getContent());
        $data = json_decode($jsonString);
        $data = $data ? $data : $jsonString;

        if ($response->isOk) {
            return $data;
        }else{
            throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
        }

        }catch (\Exception $ex){
            throw new HttpException(499, 'Authentication Connection failed.');
        }

    }

    protected static function getAuthenticateJwtUrl()
    {
        return isset(\Yii::$app->params['authenticate_jwt_url']) && ! empty(\Yii::$app->params['authenticate_jwt_url']) ? \Yii::$app->params['authenticate_jwt_url'] : false;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        //return new self(['user_id' => 1]);

        if(self::getSecretKey()) {
            $secret = static::getSecretKey();

            // Decode token and transform it into array.
            // Firebase\JWT\JWT throws exception if token can not be decoded
            try {
                $decoded = JWT::decode($token, $secret, [static::getAlgo()]);
            } catch (\Exception $e) {
                return false;
            }

            static::$decodedToken = (array)$decoded;

            // If there's no jti param - exception
            if (!isset(static::$decodedToken['jti'])) {
                return false;
            }

            // JTI is unique identifier of user.
            // For more details: https://tools.ietf.org/html/rfc7519#section-4.1.7
            $id = static::$decodedToken['jti'];

            self::$_instance = new self(['user_id' => $id]);

            return self::$_instance;
        }
         else if(self::getAuthenticateJwtUrl()) {
             $data = self::authenticateJWT($token);
             self::$_instance = new self($data);
             return self::$_instance;
         }
        return false;
    }




    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        if(self::$_instance && self::$_instance->user_id == $id){
            return self::$_instance;
        }
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }


}
