<?php
ob_start();
header('Content-Type: application/json');

// --------------------
// Paths & files
// --------------------
$basePath = realpath(__DIR__ . '/..');
$envFile = $basePath . '/.env';
$dbConfigFile = __DIR__ . '/db_config.json';
$migrationDoneFile = $basePath . '/.migrations_done';
$seedDoneFile = $basePath . '/.seed_done';
$installedFlag = $basePath . '/installed';

// --------------------
// Helpers
// --------------------
function out($msg){
    return $msg;
}

function fail($msg){
    echo json_encode(['message'=>"❌ $msg", 'show_db_form'=>false]);
    exit;
}

function nextStep($current){
    $steps = ["check","composer","db_config","env","key","migrate","seed","permissions","finish"];
    $i = array_search($current, $steps);
    return $steps[$i+1] ?? 'finish';
}

// --------------------
// Get step
// --------------------
$step = $_REQUEST['step'] ?? 'check';

// --------------------
// Handle DB save
// --------------------
if($step === 'db_config' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $db_host = $_POST['db_host'] ?? '';
    $db_database = $_POST['db_database'] ?? '';
    $db_username = $_POST['db_username'] ?? '';
    $db_password = $_POST['db_password'] ?? '';

    if(!$db_host || !$db_database || !$db_username){
        echo json_encode([
            'message' => 'Please fill all DB fields',
            'show_db_form' => true,
            'next' => 'db_config'
        ]);
        exit;
    }

    file_put_contents($dbConfigFile, json_encode([
        'host'=>$db_host,
        'database'=>$db_database,
        'username'=>$db_username,
        'password'=>$db_password
    ]));

    echo json_encode([
        'message' => 'Database config saved ✔',
        'show_db_form' => false,
        'next' => 'env'
    ]);
    exit;
}

// --------------------
// Steps
// --------------------
try {
    switch($step){
        case 'check':
            $msg = "<strong>Checking system requirements...</strong><br>";
            $allGood = true;

            // PHP version
            if(version_compare(PHP_VERSION, '8.0', '>=')){
                $msg .= "✔ PHP ".PHP_VERSION." OK<br>";
            }else{
                $msg .= "❌ PHP 8.0+ required, current: ".PHP_VERSION."<br>";
                $allGood=false;
            }

            // PHP extensions
            $required = ['pdo','pdo_mysql','openssl','mbstring','tokenizer','xml','ctype','json','bcmath','fileinfo','curl','gd','zip'];
            foreach($required as $ext){
                if(extension_loaded($ext)){
                    $msg .= "✔ $ext enabled<br>";
                }else{
                    $msg .= "❌ Missing extension: $ext<br>";
                    $allGood=false;
                }
            }

            if($allGood){
                $next = nextStep($step);
                echo json_encode(['message'=>$msg.'✔ All requirements OK','show_db_form'=>false,'next'=>$next]);
            }else{
                echo json_encode(['message'=>$msg.'❌ Fix errors and refresh','show_db_form'=>false]);
            }
            exit;

        case 'composer':
            $projectPath = $basePath;
            $isWindows = strtoupper(substr(PHP_OS,0,3))==='WIN';

            $composerCmd = $isWindows ? 'composer' : 'composer';
            $cmd = $isWindows
                ? "cd /d \"$projectPath\" && $composerCmd install --no-interaction --prefer-dist"
                : "cd \"$projectPath\" && COMPOSER_HOME=/tmp HOME=/tmp $composerCmd install --no-interaction --prefer-dist";

            $output = shell_exec($cmd." 2>&1");
            if($output === null){
                fail("Composer command failed. Ensure composer is installed and shell_exec enabled.");
            }

            echo json_encode([
                'message'=>"Composer completed.<br><pre>$output</pre>",
                'show_db_form'=>false,
                'next'=>nextStep($step)
            ]);
            exit;

        case 'db_config':
            // Show DB form if config not exists
            if(!file_exists($dbConfigFile)){
                echo json_encode([
                    'message'=>'Please enter database info',
                    'show_db_form'=>true,
                    'next'=>$step
                ]);
            }else{
                echo json_encode([
                    'message'=>'Database config already exists ✔',
                    'show_db_form'=>false,
                    'next'=>nextStep($step)
                ]);
            }
            exit;

        case 'env':
            if(!file_exists($dbConfigFile)) fail("DB config missing");

            $config = json_decode(file_get_contents($dbConfigFile), true);
            if(!$config) fail("DB config invalid");

            $envExample = $basePath.'/.env.example';
            if(!file_exists($envExample)) fail(".env.example not found");

            $env = file_get_contents($envExample);
            $env = preg_replace('/DB_HOST=.*/', 'DB_HOST='.$config['host'], $env);
            $env = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE='.$config['database'], $env);
            $env = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME='.$config['username'], $env);
            $env = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD="'.$config['password'].'"', $env);

            file_put_contents($envFile, $env);

            echo json_encode([
                'message'=>'.env created ✔',
                'show_db_form'=>false,
                'next'=>nextStep($step)
            ]);
            exit;

        case 'key':
            system("php $basePath/artisan key:generate --force", $ret);
            if($ret!==0) fail("APP_KEY generation failed");

            echo json_encode([
                'message'=>'✔ APP_KEY generated',
                'show_db_form'=>false,
                'next'=>nextStep($step)
            ]);
            exit;

        case 'migrate':
            system("php $basePath/artisan migrate --force", $ret);
            if($ret!==0) fail("Migration failed");

            file_put_contents($migrationDoneFile,"done");

            echo json_encode([
                'message'=>'✔ Migrations completed',
                'show_db_form'=>false,
                'next'=>nextStep($step)
            ]);
            exit;

        case 'seed':
            system("php $basePath/artisan db:seed --force", $ret);
            if($ret!==0) fail("Seeding failed");

            file_put_contents($seedDoneFile,"done");

            echo json_encode([
                'message'=>'✔ Database seeded',
                'show_db_form'=>false,
                'next'=>nextStep($step)
            ]);
            exit;

        case 'permissions':
            // For Linux: chmod storage & bootstrap/cache
            @chmod($basePath.'/storage',0775);
            @chmod($basePath.'/bootstrap/cache',0775);

            echo json_encode([
                'message'=>'✔ Permissions set',
                'show_db_form'=>false,
                'next'=>nextStep($step)
            ]);
            exit;

        case 'finish':
            file_put_contents($installedFlag,"installed");
            // Set APP_INSTALLED=true
            $env = file_get_contents($envFile);
            if(str_contains($env,'APP_INSTALLED=')){
                $env = preg_replace('/APP_INSTALLED=.*/','APP_INSTALLED=true',$env);
            }else{
                $env .= "\nAPP_INSTALLED=true\n";
            }
            file_put_contents($envFile,$env);

            echo json_encode([
                'message'=>'✔ Installation complete! <a href="/">Open Application</a>',
                'show_db_form'=>false
            ]);
            exit;

        default:
            fail("Invalid step: $step");
    }
} catch(Exception $e){
    fail($e->getMessage());
}

ob_end_flush();
