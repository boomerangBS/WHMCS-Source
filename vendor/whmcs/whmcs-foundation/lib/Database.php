<?php


namespace WHMCS;
class Database implements Database\DatabaseInterface
{
    protected $hostname;
    protected $username;
    protected $password;
    protected $databaseName;
    protected $characterSet = "latin1";
    protected $connection;
    protected $port;
    protected $pdoOptions = [];
    protected $pdo;
    protected $sqlMode = "";
    const WAIT_TIMEOUT = 600;
    public function __construct(Config\DatabaseInterface $config)
    {
        if(!extension_loaded("pdo")) {
            throw new Exception\Application\Configuration\PdoNotEnabled("PDO extension not found");
        }
        if($config->getDatabaseCharset() != "") {
            $this->setCharacterSet($config->getDatabaseCharset());
        }
        if($config->getDatabasePort() != "") {
            $this->setPort($config->getDatabasePort());
        }
        $this->setHostname($config->getDatabaseHost())->setUsername($config->getDatabaseUsername())->setPassword($config->getDatabasePassword())->setDatabaseName($config->getDatabaseName())->setTls($config->getDatabaseOptions())->setSqlMode($config->getSqlMode());
        $capsule = $this->capsuleFactory();
        if(defined("MYSQL_EXTENSION_ENABLED")) {
            $this->setConnection($this->connect());
        } else {
            $this->setPdo($capsule->getConnection()->getPdo());
        }
    }
    public function getPdo()
    {
        return $this->pdo;
    }
    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }
    protected function setPdoOption($option, $value) : \self
    {
        $this->pdoOptions[$option] = $value;
        return $this;
    }
    protected function setHostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }
    protected function getHostname()
    {
        return $this->hostname;
    }
    protected function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    protected function getUsername()
    {
        return $this->username;
    }
    protected function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    protected function getPassword()
    {
        return $this->password;
    }
    protected function getPort()
    {
        return $this->port;
    }
    protected function getPdoOptions() : array
    {
        return $this->pdoOptions;
    }
    protected function setTls($config) : \self
    {
        $isConfigured = function ($option) {
            return !is_null($option) && $option !== "";
        };
        if($isConfigured($config["db_tls_ca"])) {
            $this->setTlsCaFile($config["db_tls_ca"]);
        }
        if($isConfigured($config["db_tls_ca_path"])) {
            $this->setTlsCaDir($config["db_tls_ca_path"]);
        }
        if($isConfigured($config["db_tls_cert"])) {
            $this->setTlsCert($config["db_tls_cert"]);
        }
        if($isConfigured($config["db_tls_cipher"])) {
            $this->setTlsCiphers($config["db_tls_cipher"]);
        }
        if($isConfigured($config["db_tls_key"])) {
            $this->setTlsKey($config["db_tls_key"]);
        }
        if($isConfigured($config["db_tls_verify_cert"])) {
            $this->setTlsVerifyCert((bool) $config["db_tls_verify_cert"]);
        }
        return $this;
    }
    protected function setTlsCaFile($caFile) : \self
    {
        return $this->setPdoOption(\PDO::MYSQL_ATTR_SSL_CA, $caFile);
    }
    protected function setTlsCaDir($caDir) : \self
    {
        return $this->setPdoOption(\PDO::MYSQL_ATTR_SSL_CAPATH, $caDir);
    }
    protected function setTlsCert($cert) : \self
    {
        return $this->setPdoOption(\PDO::MYSQL_ATTR_SSL_CERT, $cert);
    }
    protected function setTlsCiphers($ciphers) : \self
    {
        return $this->setPdoOption(\PDO::MYSQL_ATTR_SSL_CIPHER, $ciphers);
    }
    protected function setTlsKey($key) : \self
    {
        return $this->setPdoOption(\PDO::MYSQL_ATTR_SSL_KEY, $key);
    }
    protected function setTlsVerifyCert($verifyCert) : \self
    {
        return $this->setPdoOption(\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT, (bool) $verifyCert);
    }
    protected function setPort($port)
    {
        $this->port = $port;
        return $this;
    }
    protected function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;
        return $this;
    }
    public function getDatabaseName()
    {
        return $this->databaseName;
    }
    protected function setCharacterSet($characterSet)
    {
        $this->characterSet = $characterSet;
        return $this;
    }
    public function getCharacterSet()
    {
        return $this->characterSet;
    }
    protected function setConnection($connection)
    {
        if(!is_resource($connection)) {
            throw new Exception("Please provide a mysql_connect() resource.");
        }
        $this->connection = $connection;
        return $this;
    }
    public function getConnection()
    {
        return $this->connection;
    }
    public function getSqlMode()
    {
        return $this->sqlMode;
    }
    public function setSqlMode($value)
    {
        $this->sqlMode = $value;
        return $this;
    }
    public function retrieveDatabaseConnection()
    {
        return $this->getConnection();
    }
    protected function connect()
    {
        $hostAndPort = $this->getHostname();
        if($this->getPort()) {
            $hostAndPort .= ":" . $this->getPort();
        }
        $connection = @mysql_connect($hostAndPort, @$this->getUsername(), @$this->getPassword());
        if($connection === false) {
            throw new Exception("Unable to connect to the database.");
        }
        $result = @mysql_select_db(@$this->getDatabaseName(), $connection);
        if(!$result) {
            throw new Exception("Could not connect to the " . $this->getDatabaseName() . " database");
        }
        full_query("SET SESSION wait_timeout=" . self::WAIT_TIMEOUT, $connection);
        if(!is_null($this->getCharacterSet())) {
            full_query("SET NAMES '" . db_escape_string($this->getCharacterSet()) . "'", $connection);
        }
        $sqlMode = $this->getSqlMode();
        if(!$this->applyLegacyConnectionSqlMode($sqlMode) && $sqlMode !== Config\Application::DEFAULT_SQL_MODE) {
            $this->applyLegacyConnectionSqlMode(Config\Application::DEFAULT_SQL_MODE);
        }
        global $whmcsmysql;
        $whmcsmysql = $connection;
        return $connection;
    }
    protected function getCollationFromCharacterSet($characterSet = "utf8")
    {
        $collations = ["big5" => "big5_chinese_ci", "dec8" => "dec8_swedish_ci", "cp850" => "cp850_general_ci", "hp8" => "hp8_english_ci", "koi8r" => "koi8r_general_ci", "latin1" => "latin1_swedish_ci", "latin2" => "latin2_general_ci", "swe7" => "swe7_swedish_ci", "ascii" => "ascii_general_ci", "ujis" => "ujis_japanese_ci", "sjis" => "sjis_japanese_ci", "hebrew" => "hebrew_general_ci", "tis620" => "tis620_thai_ci", "euckr" => "euckr_korean_ci", "koi8u" => "koi8u_general_ci", "gb2312" => "gb2312_chinese_ci", "greek" => "greek_general_ci", "cp1250" => "cp1250_general_ci", "gbk" => "gbk_chinese_ci", "latin5" => "latin5_turkish_ci", "armscii8" => "armscii8_general_ci", "utf8" => "utf8_unicode_ci", "ucs2" => "ucs2_general_ci", "cp866" => "cp866_general_ci", "keybcs2" => "keybcs2_general_ci", "macce" => "macce_general_ci", "macroman" => "macroman_general_ci", "cp852" => "cp852_general_ci", "latin7" => "latin7_general_ci", "utf8mb4" => "utf8mb4_general_ci", "cp1251" => "cp1251_general_ci", "utf16" => "utf16_general_ci", "utf16le" => "utf16le_general_ci", "cp1256" => "cp1256_general_ci", "cp1257" => "cp1257_general_ci", "utf32" => "utf32_general_ci", "binary" => "binary", "geostd8" => "geostd8_general_ci", "cp932" => "cp932_japanese_ci", "eucjpms" => "eucjpms_japanese_ci"];
        return isset($collations[$characterSet]) ? $collations[$characterSet] : $collations["utf8"];
    }
    protected function capsuleFactory()
    {
        $capsule = new Database\Capsule();
        $config = ["driver" => "mysql", "host" => $this->getHostname(), "database" => $this->getDatabaseName(), "username" => $this->getUsername(), "password" => $this->getPassword(), "charset" => $this->getCharacterSet(), "collation" => $this->getCollationFromCharacterSet($this->getCharacterSet()), "prefix" => "", "options" => $this->getPdoOptions()];
        if($port = $this->getPort()) {
            if(is_numeric($port)) {
                $config["port"] = $port;
            } else {
                $config["unix_socket"] = $port;
            }
        }
        $capsule->addConnection($config);
        $capsule->setFetchMode(\PDO::FETCH_OBJ);
        $capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher(new \Illuminate\Container\Container()));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $sqlMode = $this->getSqlMode();
        try {
            $this->applySqlMode($sqlMode);
        } catch (\Exception $e) {
            try {
                if($sqlMode !== Config\Application::DEFAULT_SQL_MODE) {
                    $this->applySqlMode(Config\Application::DEFAULT_SQL_MODE);
                }
            } catch (\Exception $e) {
            }
        }
        try {
            $this->disableFetchStringification();
        } catch (\Exception $e) {
        }
        return $capsule;
    }
    protected function applySqlMode($sqlMode)
    {
        Database\Capsule::connection()->getPdo()->prepare("set session sql_mode='" . $sqlMode . "'")->execute();
    }
    protected function disableFetchStringification()
    {
        Database\Capsule::connection()->getPdo()->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
    }
    protected function applyLegacyConnectionSqlMode($sqlMode)
    {
        return (bool) mysql_query("set session sql_mode='" . db_escape_string($sqlMode) . "'");
    }
    public function listTables()
    {
        $tables = Database\Capsule::connection()->getPdo()->query("SHOW TABLES");
        $tableArray = [];
        foreach ($tables as $table) {
            $tableArray[] = $table[0];
        }
        return $tableArray;
    }
    public function optimizeTables(array $tables)
    {
        $optimisedTables = [];
        try {
            foreach ($tables as $table) {
                $statement = "OPTIMIZE TABLE `" . $table . "`;";
                $pdo = Database\Capsule::connection()->getPdo();
                $pdo->query($statement);
                $optimisedTables[] = $table;
            }
        } catch (\Exception $e) {
            $tableList = implode(", ", $optimisedTables);
            $exceptionMessage = "Optimising table failed.";
            if($tableList) {
                $exceptionMessage .= " Successfully optimised tables are: " . $tableList;
            }
            throw new Exception($exceptionMessage);
        }
        return $this;
    }
    public function showVariable($variableName)
    {
        $result = Database\Capsule::connection()->selectOne("show variables where Variable_name = ?", [$variableName]);
        return is_null($result) ? NULL : $result->Value;
    }
    public function showLegacyConnectionVariable($variableName)
    {
        if(version_compare(PHP_VERSION, "7.0.0", ">=") && !function_exists("mysql_connect")) {
            return NULL;
        }
        $handle = $this->getConnection();
        if(!is_resource($handle)) {
            return NULL;
        }
        $result = @mysql_query("show variables where Variable_name = \"" . @db_escape_string($variableName) . "\"", $handle);
        if(!$result) {
            return NULL;
        }
        $data = @mysql_fetch_array($result);
        if(!is_array($data) || !array_key_exists("Value", $data)) {
            return NULL;
        }
        return (string) $data["Value"];
    }
    public function isSqlStrictMode()
    {
        $pdoSqlMode = $this->showVariable("sql_mode");
        strpos($pdoSqlMode, "STRICT_ALL_TABLES") !== false or $pdoIsStrict = strpos($pdoSqlMode, "STRICT_ALL_TABLES") !== false || strpos($pdoSqlMode, "STRICT_TRANS_TABLES") !== false;
        $legacyConnSqlMode = $this->showLegacyConnectionVariable("sql_mode");
        if(!is_null($legacyConnSqlMode)) {
            strpos($legacyConnSqlMode, "STRICT_ALL_TABLES") !== false or $legacyConnIsStrict = strpos($legacyConnSqlMode, "STRICT_ALL_TABLES") !== false || strpos($legacyConnSqlMode, "STRICT_TRANS_TABLES") !== false;
        } else {
            $legacyConnIsStrict = false;
        }
        return $pdoIsStrict || $legacyConnIsStrict;
    }
    public function getSqlVersion()
    {
        return $this->showVariable("version");
    }
    public function getSqlVersionComment()
    {
        return $this->showVariable("version_comment");
    }
}

?>