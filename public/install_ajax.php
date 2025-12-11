<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 0);

$base = realpath(__DIR__ . "/..");
$dbConfigFile = __DIR__ . "/db_config.json";
$envFile = $base . "/.env";
$envExample = $base . "/.env.example";
$migrationDoneFile = $base . "/.migrations_done";
$seedDoneFile = $base . "/.seed_done";
$installedFlag = $base . "/installed";

// Helper: JSON response
function send($arr){
    echo json_encode($arr);
    exit;
}

// Helper: run shell commands safely
function run_cmd($cmd){
    if(!function_exists('shell_exec')) return "⚠ shell_exec disabled, skipped.";
    $output = shell_exec($cmd . " 2>&1");
    return $output ?: "(no output)";
}

// Determine next step
function nextStep($step){
    $steps = ["check","composer","db_config","env","key","migrate","seed","permissions","finish"];
    $i = array_search($step,$steps);
    return $steps[$i+1] ?? "finish";
}

$step = $_POST['step'] ?? 'check';

// --------------------
// SAVE DB CONFIG
// --------------------
if($step === "db_save"){
    $db_host = $_POST['db_host'] ?? '';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';

    if(!$db_name || !$db_user){
        send([
            "success"=>false,
            "output"=>"❌ Database name and username required",
            "percent"=>30,
            "next"=>"db_config",
            "show_db_form"=>true
        ]);
    }

    file_put_contents($dbConfigFile,json_encode([
        "host"=>$db_host,
        "name"=>$db_name,
        "user"=>$db_user,
        "pass"=>$db_pass
    ],JSON_PRETTY_PRINT));

    send([
        "success"=>true,
        "output"=>"✔ Database settings saved",
        "percent"=>30,
        "next"=>"env",
        "show_db_form"=>false
    ]);
}

// --------------------
// INSTALLER STEPS
// --------------------
try{
    switch($step){

        case "check":
            $out = "✔ PHP version: ".phpversion()."<br>";
            $required = ["pdo_mysql","openssl","mbstring","tokenizer","xml","ctype","json","bcmath","fileinfo","curl","zip"];
            foreach($required as $e){
                $out .= extension_loaded($e) ? "✔ $e<br>" : "❌ Missing: $e<br>";
            }

            // Composer check
            $composer = null;
            $paths = ["/usr/local/bin/composer","/usr/bin/composer","composer"];
            foreach($paths as $p){
                $v=@shell_exec("$p --version 2>&1");
                if($v && stripos($v,"Composer")!==false){ $composer=$p; break;}
            }

            if($composer){
                $out .= "✔ Composer found: $composer<br>";
            } else {
                $out .= "⚠ Composer not found or shell_exec disabled, will skip Composer step.<br>";
            }

            send([
                "success"=>true,
                "output"=>$out,
                "percent"=>10,
                "next"=>"composer",
                "show_db_form"=>false
            ]);
        break;

        case "composer":
            $composerCmd = null;
            if(function_exists('shell_exec')){
                $paths = ["/usr/local/bin/composer","/usr/bin/composer","composer"];
                foreach($paths as $p){
                    $v=@shell_exec("$p --version 2>&1");
                    if($v && stripos($v,"Composer")!==false){ $composerCmd=$p; break;}
                }
            }

            if($composerCmd){
                $cmd = "cd \"$base\" && $composerCmd install --no-interaction --prefer-dist --ignore-platform-reqs 2>&1";
                $out = run_cmd($cmd);
                $out .= "<br>✔ Composer completed.";
            } else {
                $out = "⚠ Composer skipped.";
            }

            send([
                "success"=>true,
                "output"=>$out,
                "percent"=>20,
                "next"=>"db_config",
                "show_db_form"=>false
            ]);
        break;

        case "db_config":
            send([
                "success"=>true,
                "output"=>"Please enter database configuration",
                "percent"=>30,
                "next"=>"db_save",
                "show_db_form"=>true
            ]);
        break;

        case "env":
            $db = json_decode(file_get_contents($dbConfigFile),true);
            $env = file_exists($envExample)?file_get_contents($envExample):"";
            $env .= "\nDB_HOST={$db['host']}\nDB_DATABASE={$db['name']}\nDB_USERNAME={$db['user']}\nDB_PASSWORD=\"{$db['pass']}\"\n";
            file_put_contents($envFile,$env);
            send([
                "success"=>true,
                "output"=>"✔ .env created",
                "percent"=>50,
                "next"=>"key",
                "show_db_form"=>false
            ]);
        break;

        case "key":
            $out = run_cmd("cd \"$base\" && php artisan key:generate --force");
            send([
                "success"=>true,
                "output"=>"✔ APP_KEY generated<br>".htmlspecialchars($out),
                "percent"=>60,
                "next"=>"migrate",
                "show_db_form"=>false
            ]);
        break;

        case "migrate":
            $out = run_cmd("cd \"$base\" && php artisan migrate --force");
            file_put_contents($migrationDoneFile,"done");
            send([
                "success"=>true,
                "output"=>"✔ Migrations complete<br>".htmlspecialchars($out),
                "percent"=>75,
                "next"=>"seed",
                "show_db_form"=>false
            ]);
        break;

        case "seed":
            $out = run_cmd("cd \"$base\" && php artisan db:seed --force");
            file_put_contents($seedDoneFile,"done");
            send([
                "success"=>true,
                "output"=>"✔ Seeding complete<br>".htmlspecialchars($out),
                "percent"=>85,
                "next"=>"permissions",
                "show_db_form"=>false
            ]);
        break;

        case "permissions":
            @chmod($base."/storage",0777);
            @chmod($base."/bootstrap/cache",0777);
            send([
                "success"=>true,
                "output"=>"✔ Permissions set",
                "percent"=>95,
                "next"=>"finish",
                "show_db_form"=>false
            ]);
        break;

        case "finish":
            file_put_contents($installedFlag,"installed");
            send([
                "success"=>true,
                "output"=>"🎉 Installation complete!",
                "percent"=>100,
                "next"=>"finish",
                "show_db_form"=>false
            ]);
        break;

        default:
            send([
                "success"=>false,
                "output"=>"Unknown step: $step",
                "percent"=>0,
                "next"=>"check",
                "show_db_form"=>false
            ]);
        break;
    }
}catch(Exception $e){
    send([
        "success"=>false,
        "output"=>"Installer error: ".$e->getMessage(),
        "percent"=>0,
        "next"=>$step,
        "show_db_form"=>false
    ]);
}
