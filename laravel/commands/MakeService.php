<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    protected $signature = 'make:service 
                            {name : The name of the service class, with optional namespace (e.g., Services/User/UserService)} 
                            {--i|interface : Generate a corresponding interface}';

    protected $description = 'Create a new service class with an optional interface';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $createInterface = $this->option('interface');

        $servicePath = app_path('Services/' . str_replace('\\', '/', $name) . '.php');
        $interfacePath = app_path('Services/' . str_replace('\\', '/', $name) . 'Interface.php');

        // Create the service class
        $this->createFile($servicePath, $this->getServiceStub($name, $createInterface));
        $this->info("Service created: {$servicePath}");

        // Optionally create the interface
        if ($createInterface) {
            $this->createFile($interfacePath, $this->getInterfaceStub($name));
            $this->info("Interface created: {$interfacePath}");
        }
    }

    protected function createFile($path, $content)
    {
        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if (!$this->files->exists($path)) {
            $this->files->put($path, $content);
        } else {
            $this->error("File already exists: {$path}");
        }
    }

    protected function getServiceStub($name, $createInterface)
    {
        $namespace = $this->getNamespace($name);
        $className = $this->getClassName($name);
        $interface = $createInterface ? "{$className}Interface" : '';

        return <<<EOT
<?php

namespace $namespace;

class $className{$this->getImplements($interface)}
{
    /**
     * Create a new service instance.
     * 
     */
    public function __construct()
    {
        //
    }
        
}
EOT;
    }

    protected function getInterfaceStub($name)
    {
        $namespace = $this->getNamespace($name);
        $className = $this->getClassName($name);

        return <<<EOT
<?php

namespace $namespace;

interface {$className}Interface
{
    // Define your service contract here
}
EOT;
    }

    protected function getNamespace($name)
    {
        $parts = explode('/', str_replace('\\', '/', $name));
        array_pop($parts);
        return 'App\\Services' . (!empty($parts) ? '\\' . implode('\\', $parts) : '');
    }

    protected function getClassName($name)
    {
        $parts = explode('/', str_replace('\\', '/', $name));
        return end($parts);
    }

    protected function getImplements($interface)
    {
        return $interface ? " implements $interface" : '';
    }
}
