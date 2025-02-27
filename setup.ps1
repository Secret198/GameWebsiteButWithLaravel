$dbName
$dbUser
$dbPassword

$mysql = "C:\xampp\mysql\bin\mysql.exe"

if(Test-Path .env){
    $envContent = Get-Content .\.env -Encoding UTF8
    foreach($var in $envContent){
        $splitVar = $var.Split('=')
        
        switch ($splitVar[0]) {
            "DB_DATABASE" { 
                $dbName = $splitVar[1]
             }
             "DB_USERNAME" { 
                $dbUser = $splitVar[1]
             }
             "DB_PASSWORD" { 
                $dbPassword = $splitVar[1]
             }
            Default {}
        }
    }

    Write-Host "Függőségek telepítése..."
    composer install
    Write-Host "Adatabázis létrehozása..."
    & $mysql --user=$dbUser --password=$dbPassword -e "CREATE DATABASE IF NOT EXISTS $dbName DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"
    Write-Host "Kulcs generálása..."
    php artisan key:generate
    Write-Host "Táblák létrehozása"
    php artisan migrate
    Write-Host "Adatabázis feltöltése teszt adatokkal..."
    php artisan db:seed
}
else{
    throw ".env fájl nem létezik"
}