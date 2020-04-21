<?php


namespace Keiryo\Database\Driver;


use PDO;
use PDOException;

class ProstgreDriver extends AbstractDriver
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Sqlite driver implementation
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;

        try {
            $this->connect();
        } catch (PDOException $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        try {
            $db = parse_url(getenv("DATABASE_URL"));
            $dsn = "pgsql:" . sprintf(
                    "host=%s;port=%s;user=%s;password=%s;dbname=%s",
                    $db["host"],
                    $db["port"],
                    $db["user"],
                    $db["pass"],
                    ltrim($db["path"], "/")
                );
            $this->pdo = new PDO($dsn);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function quoteTableName(string $table): string
    {
        return $table == '*'
            ? $table
            : '"' . $table . '"';
    }

    /**
     * @inheritDoc
     */
    public function quoteColumnName(string $column): string
    {
        return $column == '*'
            ? $column
            : '"' . $column . '"';
    }

    /**
     * @inheritDoc
     */
    public function quoteSingle($value)
    {
        return is_int($value) ? $value : '"' . $value . '"';
    }
}