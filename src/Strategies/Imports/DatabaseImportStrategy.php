<?php

namespace Henrotaym\LaravelMysqlDump\Strategies\Imports;

use Exception;
use Henrotaym\LaravelMysqlDump\Contracts\Strategies\ImportStrategy;
use Illuminate\Console\Command;

class DatabaseImportStrategy implements ImportStrategy
{
    public function __construct(
        protected string $host,
        protected string $port,
        protected string $username,
        protected string $password,
        protected string $path
    ) {}

    public function import(): void
    {
        $process = "mysql --host=$this->host --port=$this->port --user=$this->username --password=$this->password --loose-ssl-verify-server-cert=0 --execute='source $this->path'";

        exec($process, $output, $code);
        $isFailure = $code === Command::FAILURE;

        if ($isFailure) {
            throw new Exception("Unable to import to mysql file [$this->path].");
        }
    }
}
