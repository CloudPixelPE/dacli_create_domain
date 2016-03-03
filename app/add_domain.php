<?php
namespace DACMD;
require_once '../vendor/autoload.php';

define("__SCRIPT_VERSION__", "1.0");

use Ulrichsg\Getopt\Getopt as Getopt;
use Ulrichsg\Getopt\Option as Option;
use Ulrichsg\Getopt\Argument as Argument;

$getopt = new Getopt(array(
    (new Option('d', 'domain', Getopt::REQUIRED_ARGUMENT))->setArgument(new Argument(1, function($value){
        return filter_var('foo@'.$value, FILTER_VALIDATE_EMAIL);
    })),
    (new Option('l', 'login', Getopt::REQUIRED_ARGUMENT))->setArgument(new Argument('_', function($value){
        return $value !="_" and !is_null($value);
    })),
    (new Option('p', 'password', Getopt::REQUIRED_ARGUMENT))->setArgument(new Argument('_', function($value){
        return $value !="_" and !is_null($value);
    }))
    (new Option(null, 'server-host', Getopt::REQUIRED_ARGUMENT))->setDefaultValue("127.0.0.1")->setDescription("server hostname"),
    (new Option(null, 'server-port', Getopt::REQUIRED_ARGUMENT))->setDefaultValue("2222")->setDescription("server port"),
    (new Option(null, 'ssl', Getopt::NO_ARGUMENT))->setDefaultValue("ON"),
    (new Option(null, 'cgi', Getopt::NO_ARGUMENT))->setDefaultValue("ON"),
    (new Option(null, 'php', Getopt::NO_ARGUMENT))->setDefaultValue("ON"),
    (new Option(null, 'uquota', Getopt::REQUIRED_ARGUMENT))->setDefaultValue("unlimited"),
    (new Option(null, 'ubandwidth', Getopt::REQUIRED_ARGUMENT))->setDefaultValue("unlimited"),
    (new Option(null, 'version')),
    (new Option(null, 'help'))
));

$getopt->setBanner("Direct Admin Script - Create new domain %s\n");

try{
    $getopt->parse();
    
    if ($getopt['version']) {
        echo __SCRIPT_VERSION__;
        echo "\n";
        exit(0);
    }elseif($getopt['help']){
        echo $getopt->getHelpText();
        exit(0);
    }

    $sock = new \HTTPSocket;
    $sock->connect($getopt['server-host'],$getopt['server-port']); 
    $sock->set_login($getopt['login'],$getopt['password']); 
    $sock->set_method('POST'); 
    $sock->query('/CMD_DOMAIN', array( 
        'action' => 'create', 
        'domain' => $getopt['domain'], 
        'ubandwidth' => $getopt['ubandwidth'], 
        'uquota' => $getopt['uquota'], 
        'ssl' => $getopt['ssl'], 
        'cgi' => $getopt['ssl'], 
        'php' => $getopt['ssl'], 
        'create' => 'Create' )
        ); 

    $result = (int)$sock->get_status_code();
 
    if($result === 200){
        exit(0);
    }else{
        exit(1);
    }

    
}catch (\UnexpectedValueException $e) {
    echo "Error: ".$e->getMessage()."\n\n";
    echo $getopt->getHelpText();
    exit(1);
}