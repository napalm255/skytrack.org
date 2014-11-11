<?php

  require(dirname(__FILE__) . '/aws.phar');
  date_default_timezone_set('America/New_York');

  use Aws\DynamoDb\DynamoDbClient;
  use Aws\Common\Enum\Region;
  use Aws\DynamoDb\Enum\KeyType;
  use Aws\DynamoDb\Enum\Type;
  use Aws\DynamoDb\Enum\ProjectionType;
  use Aws\DynamoDb\Enum\AttributeAction;
    
  class Weather {

    private $db;
    private $default;

    function __construct () {
      global $db;

      $db = new StdClass();
      $def = new StdClass();

      $db->client = DynamoDbClient::factory(array(
          'profile' => 'weather',
          'region' => Region::US_EAST_1
        ));

      $db->prefixTable = "weather";

      #$default = json_decode(file_get_contents('weather.defaults.json'));
      #var_dump($default);

    }

    // ***********************************************************
    // TABLE FUNCTIONS
    // ***********************************************************

    // table - create 'users' table
    function table_create_users (
      $readCapacity = 5,
      $writeCapacity = 5
    ) {
      global $db;

      $table = $db->prefixTable . 'Users';
      $db->client->createTable(array(
          'TableName' => $table,
          'AttributeDefinitions' => array(
              array(
                  'AttributeName' => 'email',
                  'AttributeType' => Type::STRING
              )
          ),
          'KeySchema' => array(
              array(
                  'AttributeName' => 'email',
                  'KeyType'       => 'HASH'
              )
          ),
          'ProvisionedThroughput' => array(
              'ReadCapacityUnits'  => $readCapacity,
              'WriteCapacityUnits' => $writeCapacity
          )
      ));
      
      // Wait until the table is created and active
      $db->client->waitUntil('TableExists', array(
          'TableName' => $table
      ));

      return true;
    }

    // table - create 'hourly' table
    function table_create_hourly (
      $readCapacity = 5,
      $writeCapacity = 5
    ) {
      global $db;

      $table = $db->prefixTable . 'Hourly';
      $db->client->createTable(array(
          'TableName' => $table,
          'AttributeDefinitions' => array(
              array(
                  'AttributeName' => 'name',
                  'AttributeType' => Type::STRING
              ),
              array(
                  'AttributeName' => 'epoch',
                  'AttributeType' => Type::NUMBER
              )
          ),
          'KeySchema' => array(
              array(
                  'AttributeName' => 'name',
                  'KeyType'       => 'HASH'
              ),
              array(
                  'AttributeName' => 'epoch',
                  'KeyType'       => 'RANGE'
              )
          ),
          'ProvisionedThroughput' => array(
              'ReadCapacityUnits'  => $readCapacity,
              'WriteCapacityUnits' => $writeCapacity
          )
      ));
      
      // wait until the table is created and active
      $db->client->waitUntil('TableExists', array(
          'TableName' => $table
      ));

      return true;
    }

    // ***********************************************************
    // HOURLY FUNCTIONS
    // ***********************************************************

    // hourly - add
    function hourlyAdd (
      $name,
      $recorded,
      $epoch,
      $weather_temp,
      $weather_pop,
      $weather_wspd,
      $weather_windchill
    ) {
      global $db;

      $response = $db->client->putItem(array(
        "TableName" => $db->prefixTable . 'Hourly', 
        "Item" => array(
            "name"      => array( Type::STRING => $name ), // Primary Key
            "recorded"  => array( Type::NUMBER => $recorded ),
            "epoch"     => array( Type::NUMBER => $epoch ),
            "weather_temp"      => array( Type::NUMBER => $weather_temp ),
            "weather_pop"       => array( Type::NUMBER => $weather_pop ),
            "weather_wsdl"      => array( Type::NUMBER => $weather_wspd ),
            "weather_windchill" => array( Type::NUMBER => $weather_windchill )
        )
      ));

      return $response;
    }

    // hourly - read
    public function hourlyRead (
      $name,
      $epoch
    ) {
      global $db;

      $response = $db->client->getItem(array(
        "TableName" => $db->prefixTable . 'Hourly',
        "Key" => array(
            "name" => array( Type::STRING => $name ), // Primary Key
            "epoch" => array( Type::NUMBER => $epoch )
        )
      ));

      return $response['Item'];
    }

    // ***********************************************************
    // CHORES FUNCTIONS
    // ***********************************************************

    // chores - create
    function choresCreate (
      $name,
      $group,
      $gold = 10,
      $xp = 10
    ) {
      global $db;

      $response = $db->client->putItem(array(
        "TableName" => $db->prefixTable . 'Hourly', 
        "Item" => array(
            "name"  => array( Type::STRING => $name ), // Primary Key
            "group" => array( Type::STRING => $group ),
            "gold"  => array( Type::NUMBER => $gold ),
            "xp"    => array( Type::NUMBER => $xp )
        )
      ));

      return $response;
    }

    // chores - read
    function choresRead (
      $name,
      $group
    ) {
      global $db;

      $response = $db->client->getItem(array(
        "TableName" => $db->prefixTable . 'Hourly',
        "Key" => array(
            "name"  => array( Type::STRING => $name ), // Primary Key
            "group" => array( Type::STRING => $group )
        )
      ));

      return $response;
    }

    // ***********************************************************
    // USER FUNCTIONS
    // ***********************************************************

    // user - attribute add (or subtract with negative value)
    function userAttrAdd (
      $email,
      $attr,
      $value
    ) {
      global $db;

      $response = $db->client->updateItem(array(
        "TableName" => $db->prefixTable . 'Users', 
        "Key" => array(
          "email" => array( Type::STRING => $email ) // Primary Key
        ),
        "AttributeUpdates" => array(
          $attr => array(
            "Action" => AttributeAction::ADD,
            "Value"  => array( Type::NUMBER => $value )
          )
        )
      ));

      #return $response;
    }

    // user - delete
    function userDelete (
      $email
    ) {
      global $db;

      $response = $db->client->deleteItem(array(
        "TableName" => $db->prefixTable . 'Users', 
        "Key" => array(
            "email" => array( Type::STRING  => $email ), // Primary Key
        )
      ));

      return $response;
    }

    // user - create
    function userCreate (
      $email,
      $password
    ) {
      global $db;

      $response = $db->client->putItem(array(
        "TableName" => $db->prefixTable . 'Users', 
        "Item" => array(
            "email"         => array( Type::STRING  => $email ), // Primary Key
            "password"      => array( Type::STRING  => $password )
        )
      ));

      return $response;
    }

    // user - read
    function userRead (
      $email
    ) {
      global $db;

      $response = $db->client->getItem(array(
        "TableName" => $db->prefixTable . 'Users',
        "Key" => array(
            "email" => array( Type::STRING => $email ), // Primary Key
        )
      ));

      return $response;
    }

    // end class
  }
